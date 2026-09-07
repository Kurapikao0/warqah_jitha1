<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\StockReservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanExpiredStockReservationsCommand extends Command
{
    protected $signature = 'stock:clean-expired';

    protected $description = 'Clean up expired stock reservations and release reserved stock back to available inventory.';

    public function handle(): int
    {
        $this->info('Starting expired stock reservations cleanup...');

        try {
            $cleanedCount = DB::transaction(function () {
                $now = now();
                $expiredReservations = StockReservation::where('status', 'active')
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<=', $now)
                    ->lockForUpdate()
                    ->get();

                $affectedProductIds = [];

                foreach ($expiredReservations as $reservation) {
                    $reservation->update(['status' => 'expired']);
                    $affectedProductIds[] = $reservation->product_id;

                    if ($reservation->cart_item_id) {
                        CartItem::where('id', $reservation->cart_item_id)->update([
                            'reserved_at' => null,
                            'expires_at' => null,
                        ]);
                    }
                }

                // Clean up any remaining cart items that have expired timestamp
                $expiredCartItems = CartItem::whereNotNull('expires_at')
                    ->where('expires_at', '<=', $now)
                    ->lockForUpdate()
                    ->get();

                foreach ($expiredCartItems as $cartItem) {
                    $affectedProductIds[] = $cartItem->product_id;
                    $cartItem->update([
                        'reserved_at' => null,
                        'expires_at' => null,
                    ]);
                }

                // Recalculate reserved_quantity on all affected products
                $uniqueProductIds = array_unique(array_filter($affectedProductIds));
                foreach ($uniqueProductIds as $productId) {
                    $product = Product::lockForUpdate()->find($productId);
                    if ($product) {
                        $product->recalculateReservedQuantity();
                    }
                }

                return count($expiredReservations) + count($expiredCartItems);
            });

            $this->info("Expired stock cleanup completed successfully. Cleaned items: {$cleanedCount}");
            Log::info("CleanExpiredStockReservations: Cleaned {$cleanedCount} expired reservations/cart items.");

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error('Failed to clean expired stock reservations: ' . $exception->getMessage());
            Log::error('CleanExpiredStockReservations failed', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
