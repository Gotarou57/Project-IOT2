<?php

namespace App\Http\Controllers;

use App\Models\SensorSetting;
use App\Services\SensorDataService;

class SensorController extends Controller
{
    public function __construct(protected SensorDataService $service) {}

    /**
     * Home overview page.
     */
    public function home()
    {
        $range    = request()->query('range', '20');
        $data     = $this->service->fetch($range);
        $settings = SensorSetting::current();
        return view('home', array_merge($data, compact('settings')));
    }

    /**
     * Temperature detail page.
     */
    public function suhu()
    {
        $range    = request()->query('range', '20');
        $data     = $this->service->fetchSingle('temperature', $range);
        $settings = SensorSetting::current();
        return view('sensor.detail', array_merge($data, compact('settings'), [
            'sensorType'  => 'temperature',
            'sensorLabel' => 'Temperature',
            'sensorUnit'  => '°C',
            'sensorColor' => '#ef4444',
            'sensorField' => 'temperatures',
        ]));
    }

    /**
     * Humidity detail page.
     */
    public function humidity()
    {
        $range    = request()->query('range', '20');
        $data     = $this->service->fetchSingle('humidity', $range);
        $settings = SensorSetting::current();
        return view('sensor.detail', array_merge($data, compact('settings'), [
            'sensorType'  => 'humidity',
            'sensorLabel' => 'Humidity',
            'sensorUnit'  => '%',
            'sensorColor' => '#3b82f6',
            'sensorField' => 'humidities',
        ]));
    }

    /**
     * Air Quality detail page.
     */
    public function airQuality()
    {
        $range    = request()->query('range', '20');
        $data     = $this->service->fetchSingle('air-quality', $range);
        $settings = SensorSetting::current();
        return view('sensor.detail', array_merge($data, compact('settings'), [
            'sensorType'  => 'air-quality',
            'sensorLabel' => 'Air Quality',
            'sensorUnit'  => 'PPM',
            'sensorColor' => '#a855f7',
            'sensorField' => 'airQualities',
        ]));
    }

    /**
     * Legacy dashboard — redirect to home.
     */
    public function index()
    {
        return redirect()->route('home');
    }
}
