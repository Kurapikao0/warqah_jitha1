<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_reservations', function (Blueprint $table): void {
            $table->unsignedInteger('reserved_quantity')
                ->default(0)
                ->after('quantity');
        });

        DB::table('stock_reservations')
            ->where('reserved_quantity', 0)
            ->update(['reserved_quantity' => DB::raw('quantity')]);
    }

    public function down(): void
    {
        Schema::table('stock_reservations', function (Blueprint $table): void {
            $table->dropColumn('reserved_quantity');
        });
    }
};
