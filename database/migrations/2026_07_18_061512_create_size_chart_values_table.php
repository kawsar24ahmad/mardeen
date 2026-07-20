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
        Schema::create('size_chart_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_chart_size_id')
                ->constrained('size_chart_sizes')
                ->cascadeOnDelete();

            $table->foreignId('size_chart_measurement_id')
                ->constrained('size_chart_measurements')
                ->cascadeOnDelete();

            $table->string('value');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('size_chart_values');
    }
};
