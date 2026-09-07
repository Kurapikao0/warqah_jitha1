<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Public;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use App\Models\ExchangeRate;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    /**
     * Expose public store settings and currency configurations.
     */
    public function index(): JsonResponse
    {
        $settings = SystemSetting::first();
        // Assuming base currency is YER. Fallback secondary to SAR if not configured.
        $secondaryCurrency = $settings?->default_currency ?? 'SAR';
        $primaryCurrency = 'YER';

        // Fetch the latest exchange rate for YER -> Secondary
        $exchangeRate = ExchangeRate::where('target_currency', $secondaryCurrency)
            ->where('base_currency', $primaryCurrency)
            ->latest('fetched_at')
            ->first();

        // If no rate is found in DB, provide a realistic fallback for the Sanaa Old Edition market
        if (!$exchangeRate) {
            $rate = match (strtoupper($secondaryCurrency)) {
                'SAR' => 140.0,
                'USD' => 530.0,
                default => 140.0,
            };
        } else {
            $rate = (float) $exchangeRate->rate;
        }

        return response()->json([
            'primary_currency' => $primaryCurrency,
            'secondary_currency' => $secondaryCurrency,
            'exchange_rate' => $rate,
        ]);
    }
}
