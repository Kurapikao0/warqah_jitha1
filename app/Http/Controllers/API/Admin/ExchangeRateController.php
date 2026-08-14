<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExchangeRateService;

class ExchangeRateController extends Controller
{
    public function index(ExchangeRateService $service)
    {
        $rates = $service->getLatestRates();

        if (empty($rates['rates'])) {
            try {
                $service->syncFromApi();
                $rates = $service->getLatestRates();
            } catch (\Throwable $exception) {
                $rates = [
                    'base_currency' => 'USD',
                    'rates' => [],
                    'last_updated_at' => null,
                    'is_stale' => true,
                ];
            }
        }

        return response()->json([
            'data' => $rates,
        ]);
    }
}
