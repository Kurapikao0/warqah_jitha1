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
        $baseUrl = rtrim((string) config('services.yemen_rates_api.base_url', 'https://cygrlhmnmckoefefnsjc.supabase.co/functions/v1/public-api'), '/');
        $city = config('services.yemen_rates_api.city', 'sanaa');

        $response = Http::acceptJson()
            ->timeout(15)
            ->get("{$baseUrl}/latest", [
                'city' => $city,
            ]);

        if (! $response->successful()) {
            Log::warning('Yemen exchange rate API request failed.', [
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            throw new RuntimeException('Yemen exchange rate API request failed.');
        }

        $payload = $response->json();
        $cityData = data_get($payload, 'data.0.rates', []);

        if (! is_array($cityData) || empty($cityData)) {
            throw new RuntimeException('Yemen exchange rate API returned no rates.');
        }

        $stored = [];

        foreach ($cityData as $entry) {
            $code = data_get($entry, 'code');
            $sell = data_get($entry, 'sell');
            $buy = data_get($entry, 'buy');

            if (! $code || ! is_numeric($sell) || ! is_numeric($buy)) {
                continue;
            }

            // نستخدم متوسط سعري البيع والشراء كسعر مرجعي واحد للعرض
            $averageRate = ((float) $sell + (float) $buy) / 2;

            $stored[] = ExchangeRate::create([
                'base_currency' => strtoupper((string) $code),
                'target_currency' => 'YER',
                'rate' => $averageRate,
                'fetched_at' => now(),
            ]);
        }

        if (empty($stored)) {
            throw new RuntimeException('No valid currency rates found in API response.');
        }

        return $stored;
    }

    public function getLatestRates(): array
    {
        $latestRecords = ExchangeRate::query()
            ->where('target_currency', 'YER')
            ->orderByDesc('fetched_at')
            ->get()
            ->groupBy('base_currency')
            ->map(fn ($items) => $items->first())
            ->values();

        $rates = [];

        foreach ($latestRecords as $record) {
            $rates[$record->base_currency] = (float) $record->rate;
        }

        $latest = ExchangeRate::query()
            ->where('target_currency', 'YER')
            ->latest('fetched_at')
            ->first();

        $lastUpdatedAt = $latest?->fetched_at ?? null;

        return [
            'base_currency' => 'YER',
            'rates' => $rates,
            'last_updated_at' => $lastUpdatedAt ? $lastUpdatedAt->toISOString() : null,
            'is_stale' => $lastUpdatedAt === null || $lastUpdatedAt->diffInHours(now()) > 24,
        ];
    }
}

