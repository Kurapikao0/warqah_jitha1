<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table): void {
            $table->id();
            $table->string('base_currency', 10)->default('USD');
            $table->string('target_currency', 10);
            $table->decimal('rate', 18, 6);
            $table->timestamp('fetched_at')->useCurrent();
            $table->timestamps();

            $table->index(['base_currency', 'target_currency', 'fetched_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
