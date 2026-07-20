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
        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_size_id')
                ->nullable()
                ->after('product_variant_id')
                ->constrained('size_chart_sizes') // আপনার সঠিক টেবিল নেম
                ->nullOnDelete();                 // মূল সাইজ মুছে গেলেও অর্ডার হিস্ট্রি সেফ থাকবে

            $table->string('product_size')->nullable()->after('product_size_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_size_id']);
            $table->dropColumn(['product_size_id', 'product_size']);
        });
    }
};
