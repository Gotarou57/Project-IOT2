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
        Schema::create('sensor_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('temperature_enabled')->default(true);
            $table->boolean('humidity_enabled')->default(true);
            $table->unsignedInteger('refresh_delay')->default(30); // seconds
            $table->timestamps();
        });

        // Seed a single default row
        \DB::table('sensor_settings')->insert([
            'temperature_enabled' => true,
            'humidity_enabled'    => true,
            'refresh_delay'       => 30,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_settings');
    }
};
