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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('courier_id')
                ->nullable()
                ->after('id')
                ->constrained('couriers')
                ->nullOnDelete();

            $table->string('tracking_code')
                ->nullable()
                ->after('courier_id');

            $table->string('consignment_id')
                ->nullable()
                ->after('tracking_code');

            $table->string('courier_status')
                ->nullable()
                ->after('consignment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['courier_id']);

            $table->dropColumn([
                'courier_id',
                'tracking_code',
                'consignment_id',
                'courier_status',
            ]);
        });
    }
};
