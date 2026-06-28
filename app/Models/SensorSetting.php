<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorSetting extends Model
{
    protected $table = 'sensor_settings';

    protected $fillable = [
        'temperature_enabled',
        'humidity_enabled',
        'refresh_delay',
    ];

    protected $casts = [
        'temperature_enabled' => 'boolean',
        'humidity_enabled'    => 'boolean',
        'refresh_delay'       => 'integer',
    ];

    /**
     * Always return the single settings row, creating it if missing.
     */
    public static function current(): self
    {
        return self::firstOrCreate([], [
            'temperature_enabled' => true,
            'humidity_enabled'    => true,
            'refresh_delay'       => 30,
        ]);
    }
}
