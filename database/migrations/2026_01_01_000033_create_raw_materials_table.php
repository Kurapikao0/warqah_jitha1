<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('unit', 20);
            $table->decimal('quantity_available', 10, 2)->default(0.00);
            $table->decimal('reorder_point', 10, 2)->default(0.00);
            $table->enum('status', ['in_stock', 'low_stock', 'out_of_stock'])->default('in_stock');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->nullable();
        });

        DB::statement('ALTER TABLE raw_materials ADD CONSTRAINT chk_raw_materials_quantity_available CHECK (quantity_available >= 0)');
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
