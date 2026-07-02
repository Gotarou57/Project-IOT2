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
        Schema::table('sensor_settings', function (Blueprint $table) {
            $table->boolean('air_quality_enabled')->default(true)->after('humidity_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sensor_settings', function (Blueprint $table) {
            $table->dropColumn('air_quality_enabled');
        });
    }
};
