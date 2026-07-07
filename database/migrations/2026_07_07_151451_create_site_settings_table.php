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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('My Website');
            $table->string('site_logo')->nullable();
            $table->string('admin_email')->nullable();
            $table->text('site_description')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('whats_up_number')->nullable();
            $table->string('twitter_url')->nullable();
            $table->text('top_bar_text')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
