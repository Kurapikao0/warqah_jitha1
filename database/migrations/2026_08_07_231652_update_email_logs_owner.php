<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_logs', function (Blueprint $table): void {
            $table->string('owner_type', 255)
                ->nullable()
                ->after('id');

            $table->unsignedBigInteger('owner_id')
                ->nullable()
                ->after('owner_type');

            $table->index(
                ['owner_type', 'owner_id'],
                'email_logs_owner_type_owner_id_index'
            );
        });

        /*
         * Existing customer_id values are intentionally not copied here.
         *
         * After confirming that the new polymorphic structure works,
         * customer_id will be removed in the final cleanup migration.
         */
    }

    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table): void {
            $table->dropIndex('email_logs_owner_type_owner_id_index');

            $table->dropColumn([
                'owner_type',
                'owner_id',
            ]);
        });
    }
};
