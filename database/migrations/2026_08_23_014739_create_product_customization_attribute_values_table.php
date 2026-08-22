<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_customization_attribute_values', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('customization_request_id')
                ->constrained('product_customization_requests')
                ->cascadeOnDelete();

            $table->foreignId('attribute_id')
                ->constrained('product_attributes')
                ->restrictOnDelete();

            $table->string('value', 255);

            $table->timestamps();

            $table->unique(
                ['customization_request_id', 'attribute_id'],
                'customization_request_attribute_unique'
            );

            $table->index(
                'attribute_id',
                'product_customization_attribute_values_attribute_id_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_customization_attribute_values');
    }
};
