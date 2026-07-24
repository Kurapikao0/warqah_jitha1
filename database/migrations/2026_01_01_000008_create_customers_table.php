<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone_country_code', 5)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('password_hash', 255);
            $table->string('avatar_url', 255)->nullable();
            $table->enum('category', ['regular', 'vip'])->default('regular');
            $table->timestampTz('email_verified_at')->nullable();
            $table->timestampTz('phone_verified_at')->nullable();
            $table->integer('total_orders')->default(0);
            $table->decimal('total_purchases', 12, 2)->default(0.00);
            $table->timestampTz('last_order_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->nullable();
            $table->softDeletesTz();

            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
