<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's consent screen.
     */
    public function redirectToGoogle(Request $request)
    {
        $state = Str::random(32);
        $request->session()->put('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => config('services.google.redirect'),
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    /**
     * Exchange the Google code for an access token, validate it, then log in the user.
     */
    public function handleGoogleCallback(Request $request)
    {
        $state = (string) $request->query('state', '');
        $code = (string) $request->query('code', '');

        if ($state === '' || $code === '') {
            return response()->json([
                'success' => false,
                'message' => 'Missing OAuth parameters.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $storedState = (string) $request->session()->pull('google_oauth_state', '');

        if ($storedState === '' || !hash_equals($storedState, $state)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OAuth state. CSRF validation failed.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $clientId = trim((string) config('services.google.client_id', ''));
        $clientSecret = trim((string) config('services.google.client_secret', ''));
        $redirectUri = trim((string) config('services.google.redirect', ''));

        if ($clientId === '' || $clientSecret === '' || $redirectUri === '') {
            Log::error('Google OAuth is not configured', [
                'client_id_present' => $clientId !== '',
                'client_secret_present' => $clientSecret !== '',
                'redirect_uri_present' => $redirectUri !== '',
                'config_cached' => app()->configurationIsCached(),
            ]);

            $payload = [
                'success' => false,
                'message' => 'Google OAuth is not configured on the server.',
            ];

            if (app()->isLocal() && config('app.debug')) {
                $payload['details'] = [
                    'client_id_present' => $clientId !== '',
                    'client_secret_present' => $clientSecret !== '',
                    'redirect_uri_present' => $redirectUri !== '',
                    'config_cached' => app()->configurationIsCached(),
                ];
            }

            return response()->json($payload, Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $googleHttp = Http::withOptions([
            'verify' => $this->googleCaBundle(),
        ])
            ->connectTimeout((float) config('services.google.connect_timeout', 5))
            ->timeout((float) config('services.google.timeout', 15));

        try {
            $tokenResponse = $googleHttp->asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => $code,
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ]);
        } catch (ConnectionException|RequestException $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'تعذر الاتصال بخدمة Google، يرجى المحاولة لاحقاً.',
            ], Response::HTTP_BAD_GATEWAY);
        }

        if ($tokenResponse->failed()) {
            $googleError = $tokenResponse->json();
            Log::warning('Google OAuth token exchange failed', [
                'status' => $tokenResponse->status(),
                'error' => $googleError['error'] ?? null,
                'error_description' => $googleError['error_description'] ?? null,
                'client_id_suffix' => substr($clientId, -12),
                'redirect_uri' => $redirectUri,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Google token exchange failed.',
                'details' => app()->isLocal() && config('app.debug')
                    ? $googleError
                    : ['error' => $googleError['error'] ?? 'google_token_exchange_failed'],
            ], Response::HTTP_BAD_GATEWAY);
        }

        $idToken = $tokenResponse->json('id_token');

        if (! is_string($idToken) || trim($idToken) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Google did not return an id_token.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $googleInfoResponse = $googleHttp->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $idToken,
            ]);
        } catch (ConnectionException|RequestException $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'تعذر الاتصال بخدمة Google، يرجى المحاولة لاحقاً.',
            ], Response::HTTP_BAD_GATEWAY);
        }

        if ($googleInfoResponse->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Google token.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $googleUser = $googleInfoResponse->json();

        if (($googleUser['aud'] ?? null) !== config('services.google.client_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Google client mismatch.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!($googleUser['email_verified'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => 'Google email is not verified.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $email = strtolower((string) ($googleUser['email'] ?? ''));
        $name = (string) ($googleUser['name'] ?? explode('@', $email)[0] ?? 'Google User');
        $avatar = $googleUser['picture'] ?? null;

        $customer = Customer::withTrashed()->firstOrNew(
            ['email' => $email],
            [
                'full_name' => $name,
                'email' => $email,
                'phone_country_code' => null,
                'phone' => null,
                'password_hash' => Hash::make(Str::random(32)),
                'avatar_url' => $avatar,
                'category' => 'regular',
                'email_verified_at' => now(),
            ]
        );

        if ($customer->trashed()) {
            $customer->restore();
        }

        if (! $customer->exists) {
            $customer->save();
        }

        $customer->update([
            'full_name' => $customer->full_name ?: $name,
            'avatar_url' => $customer->avatar_url ?: $avatar,
            'email_verified_at' => $customer->email_verified_at ?? now(),
        ]);

        $token = $customer->createToken(
            'google-auth-token',
            ['*'],
            now()->addDays(7),
        )->plainTextToken;

        $frontendUrl = rtrim((string) env('FRONTEND_URL', 'http://localhost:5173'), '/');

        $exchangeCode = Str::random(64);
        Cache::put(
            'google-auth-exchange:' . hash('sha256', $exchangeCode),
            ['token' => $token, 'customer_id' => $customer->id],
            now()->addMinute(),
        );

        return redirect($frontendUrl . '/auth/google/callback?code=' . urlencode($exchangeCode));
    }

    public function exchangeCode(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:64'],
        ]);

        $payload = Cache::pull(
            'google-auth-exchange:' . hash('sha256', $validated['code']),
        );

        if (!is_array($payload) || !isset($payload['token'], $payload['customer_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Google login code.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $customer = Customer::findOrFail((int) $payload['customer_id']);

        return response()->json([
            'token' => $payload['token'],
            'user' => [
                'id' => (string) $customer->id,
                'full_name' => $customer->full_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'avatar_url' => $customer->avatar_url,
            ],
        ]);
    }

    private function googleCaBundle(): string|bool
    {
        $configuredPath = (string) config('services.google.ca_bundle', '');

        return $configuredPath !== '' && is_file($configuredPath)
            ? $configuredPath
            : true;
    }
}
