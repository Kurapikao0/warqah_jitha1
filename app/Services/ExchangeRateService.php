<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ExchangeRateService
{
    public function syncFromApi(): array
    {
        $apiKey = config('services.exchange_rate_api.key');
        $baseUrl = rtrim((string) config('services.exchange_rate_api.base_url', 'https://v6.exchangerate-api.com/v6'), '/');

        if (blank($apiKey)) {
            throw new RuntimeException('ExchangeRate-API key is not configured.');
        }

        $response = Http::acceptJson()
            ->timeout(15)
            ->get("{$baseUrl}/{$apiKey}/latest/USD");

        if (! $response->successful()) {
            $errorType = $response->json('error-type') ?? 'request_failed';
            Log::warning('Exchange rate API request failed.', [
                'status' => $response->status(),
                'error_type' => $errorType,
                'body' => $response->json(),
            ]);

            throw new RuntimeException("Exchange rate API request failed: {$errorType}");
        }

        $payload = $response->json();
        $rates = data_get($payload, 'conversion_rates', []);

        if (! is_array($rates) || empty($rates)) {
            throw new RuntimeException('Exchange rate API returned no conversion rates.');
        }

        $stored = [];

        foreach ($rates as $currencyCode => $rate) {
            if (! is_numeric($rate)) {
                continue;
            }

            $stored[] = ExchangeRate::create([
                'base_currency' => 'USD',
                'target_currency' => strtoupper((string) $currencyCode),
                'rate' => (float) $rate,
                'fetched_at' => now(),
            ]);
        }

        return $stored;
    }

    public function getLatestRates(): array
    {
        $latestRecords = ExchangeRate::query()
            ->where('base_currency', 'USD')
            ->orderByDesc('fetched_at')
            ->get()
            ->groupBy('target_currency')
            ->map(fn ($items) => $items->first())
            ->values();

        $rates = [];

        foreach ($latestRecords as $record) {
            $rates[$record->target_currency] = (float) $record->rate;
        }

        $latest = ExchangeRate::query()
            ->where('base_currency', 'USD')
            ->latest('fetched_at')
            ->first();

        $lastUpdatedAt = $latest?->fetched_at ?? null;

        return [
            'base_currency' => 'USD',
            'rates' => $rates,
            'last_updated_at' => $lastUpdatedAt ? $lastUpdatedAt->toISOString() : null,
            'is_stale' => $lastUpdatedAt === null || $lastUpdatedAt->diffInHours(now()) > 24,
        ];
    }
}
