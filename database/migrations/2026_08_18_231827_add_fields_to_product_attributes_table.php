<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_attributes', function (Blueprint $table) {

            $table->string('display_name')
                ->nullable()
                ->after('name');

            $table->boolean('is_required')
                ->default(false)
                ->after('input_type');

            $table->json('options')
                ->nullable()
                ->after('is_required');

        });

        DB::table('product_attributes')
            ->update([
                'display_name' => DB::raw('name'),
            ]);

        Schema::table('product_attributes', function (Blueprint $table) {

            $table->string('display_name')
                ->nullable(false)
                ->change();

        });
    }

    public function down(): void
    {
        Schema::table('product_attributes', function (Blueprint $table) {

            $table->dropColumn([
                'display_name',
                'is_required',
                'options',
            ]);

        });
    }
};
