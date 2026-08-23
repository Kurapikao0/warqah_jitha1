<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_design_request_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('custom_design_request_id')
                ->constrained('custom_design_requests')
                ->cascadeOnDelete();

            $table->string('url', 2048);
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index([
                'custom_design_request_id',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_design_request_images');
    }
};
