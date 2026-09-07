<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_design_requests', function (Blueprint $table) {
            $table->decimal('quoted_price', 12, 2)
                ->nullable()
                ->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('custom_design_requests', function (Blueprint $table) {
            $table->dropColumn('quoted_price');
        });
    }
};
