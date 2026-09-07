<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->timestampTz('reserved_at')->nullable()->after('customization_note');
            $table->timestampTz('expires_at')->nullable()->after('reserved_at');
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropColumn(['reserved_at', 'expires_at']);
        });
    }
};
