<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_customization_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('base_product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->nullOnDelete();
            $table->foreignId('design_pattern_id')->nullable()->constrained('design_patterns')->nullOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('length_cm', 6, 2)->nullable();
            $table->decimal('width_cm', 6, 2)->nullable();
            $table->decimal('height_cm', 6, 2)->nullable();
            $table->text('customer_notes')->nullable();
            $table->text('craftsman_notes')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('customization_fee', 10, 2)->nullable();
            $table->decimal('packaging_shipping_fee', 10, 2)->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->enum('status', ['pending_approval', 'in_production', 'completed'])->default('pending_approval');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->softDeletesTz();
        });

        DB::statement('ALTER TABLE product_customization_requests ADD CONSTRAINT chk_product_customization_requests_quantity CHECK (quantity > 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('product_customization_requests');
    }
};
