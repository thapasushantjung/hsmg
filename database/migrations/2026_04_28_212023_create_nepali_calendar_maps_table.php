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
        Schema::create('nepali_calendar_maps', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->unique();
            $table->json('months'); // Array of 12 integers representing days in each month
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nepali_calendar_maps');
    }
};
