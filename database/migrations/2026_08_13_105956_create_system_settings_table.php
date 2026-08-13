<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->default('Waraqh');
            $table->string('store_logo')->nullable();
            $table->string('contact_email')->default('admin@waraqh.com');
            $table->string('contact_phone')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(15);
            $table->string('default_currency')->default('SAR');
            $table->boolean('maintenance_mode')->default(false);
            $table->string('maintenance_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
