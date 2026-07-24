<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('shipping_recipient_name', 255);
            $table->string('shipping_phone', 20);
            $table->text('shipping_address_full');
            $table->string('shipping_city', 100);
            $table->string('shipping_country', 100);
            $table->enum('order_type', ['ready_made', 'custom', 'mixed'])->default('ready_made');
            $table->enum('status', ['received', 'in_production', 'in_transit', 'cancelled'])->default('received');
            $table->foreignId('current_production_stage_id')->nullable()->constrained('order_production_stages')->nullOnDelete();
            $table->date('expected_delivery_date')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_fee', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->softDeletesTz();

            $table->index('status');
        });

        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_subtotal CHECK (subtotal >= 0)');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_shipping_fee CHECK (shipping_fee >= 0)');
        DB::statement('ALTER TABLE orders ADD CONSTRAINT chk_orders_total_amount CHECK (total_amount >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
