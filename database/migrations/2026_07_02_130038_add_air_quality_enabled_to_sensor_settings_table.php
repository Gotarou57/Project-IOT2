<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensor_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('sensor_settings', 'air_quality_enabled')) {
                $table->boolean('air_quality_enabled')->default(true)->after('humidity_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sensor_settings', function (Blueprint $table) {
            $table->dropColumn('air_quality_enabled');
        });
    }
};
