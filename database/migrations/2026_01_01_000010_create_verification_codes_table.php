<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            $table->enum('purpose', ['signup_email_verification','signup_phone_verification', 'password_reset_email_link', 'password_reset_phone_otp']);
            $table->string('code_or_token', 255);
            $table->string('contact_value', 255);
            $table->timestampTz('expires_at');
            $table->timestampTz('consumed_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['customer_id', 'purpose']);
            
            // التأكد من وجود عمود code هنا
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_codes');
    }
};