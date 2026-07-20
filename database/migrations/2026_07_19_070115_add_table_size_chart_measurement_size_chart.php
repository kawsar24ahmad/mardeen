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
        Schema::create('measurement_size_chart', function (Blueprint $table) {
            $table->foreignId('size_chart_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('size_chart_measurement_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->primary([
                'size_chart_id',
                'size_chart_measurement_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measurement_size_chart');
    }
};
