<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('cart_item_id')->nullable()->constrained('cart_items')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('session_id', 100)->nullable();
            $table->integer('quantity')->default(1);
            $table->timestampTz('reserved_at')->useCurrent();
            $table->timestampTz('expires_at')->nullable();
            $table->enum('status', ['active', 'released', 'consumed', 'expired'])->default('active');
            $table->timestampsTz();

            $table->index(['product_id', 'status', 'expires_at']);
            $table->index('expires_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_reservations');
    }
};
