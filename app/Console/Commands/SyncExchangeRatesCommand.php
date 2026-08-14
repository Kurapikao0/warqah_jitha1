<?php

namespace App\Console\Commands;

use App\Services\ExchangeRateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncExchangeRatesCommand extends Command
{
    protected $signature = 'exchange-rates:sync';

    protected $description = 'Fetch the latest exchange rates from the external API and store the last successful values locally.';

    public function handle(ExchangeRateService $service): int
    {
        try {
            $records = $service->syncFromApi();

            $this->info('Exchange rates synced successfully. Total records: ' . count($records));

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            Log::warning('Exchange rate sync failed. Using fallback values if any are cached.', [
                'message' => $exception->getMessage(),
            ]);

            $this->warn('Exchange rate sync failed. The system will continue using the last known cached rates.');

            return self::SUCCESS;
        }
    }
}
