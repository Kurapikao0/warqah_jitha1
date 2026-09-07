<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

        $tokenResponse = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'code' => $code,
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri' => config('services.google.redirect'),
            'grant_type' => 'authorization_code',
        ]);

        if ($tokenResponse->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Google token exchange failed.',
                'details' => $tokenResponse->json(),
            ], Response::HTTP_BAD_GATEWAY);
        }

        $idToken = $tokenResponse->json('id_token');

        if (! is_string($idToken) || trim($idToken) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Google did not return an id_token.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $googleInfoResponse = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

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

        $customer = Customer::query()->firstOrCreate(
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

        $customer->update([
            'full_name' => $customer->full_name ?: $name,
            'avatar_url' => $customer->avatar_url ?: $avatar,
            'email_verified_at' => $customer->email_verified_at ?? now(),
        ]);

        $token = $customer->createToken('google-auth-token')->plainTextToken;

        $frontendUrl = rtrim((string) env('FRONTEND_URL', 'http://localhost:5173'), '/');

        return redirect(
            $frontendUrl . '/auth/google/callback?token=' . urlencode($token)
            . '&email=' . urlencode($customer->email)
            . '&full_name=' . urlencode($customer->full_name)
        );
    }
}
