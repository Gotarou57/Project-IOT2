<?php

namespace App\Http\Controllers;

use App\Models\SensorSetting;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SensorController extends Controller
{
    /**
     * Fetch the latest sensor data from ThingSpeak.
     */
    private function fetchSensorData(): array
    {
        $channelId = env('THINGSPEAK_CHANNEL_ID');
        $apiKey    = env('THINGSPEAK_READ_API_KEY');

        $url = "https://api.thingspeak.com/channels/{$channelId}/feeds.json?results=20";

        $response = Http::get($url, ['api_key' => $apiKey]);

        $timestamps   = [];
        $temperatures = [];
        $humidities   = [];
        $airQualities = [];

        if ($response->successful()) {
            $data  = $response->json();
            $feeds = $data['feeds'] ?? [];

            foreach ($feeds as $feed) {
                $timestamps[]   = Carbon::parse($feed['created_at'])->setTimezone('Asia/Jakarta')->format('H:i');
                $temperatures[] = (float) ($feed['field1'] ?? 0);
                $humidities[]   = (float) ($feed['field2'] ?? 0);
                $airQualities[] = (float) ($feed['field3'] ?? 0);
            }

            $temperature = end($temperatures) ?: 'N/A';
            $humidity    = end($humidities)   ?: 'N/A';
            $airQuality  = end($airQualities) ?: 'N/A';
            $timestamp   = end($timestamps)   ?: 'N/A';
        } else {
            $temperature = 'Error';
            $humidity    = 'Error';
            $airQuality  = 'Error';
            $timestamp   = 'N/A';
        }

        return compact(
            'temperature',
            'humidity',
            'airQuality',
            'timestamp',
            'timestamps',
            'temperatures',
            'humidities',
            'airQualities'
        );
    }

    /**
     * Home overview page.
     */
    public function home()
    {
        $data     = $this->fetchSensorData();
        $settings = SensorSetting::current();
        return view('home', array_merge($data, compact('settings')));
    }

    /**
     * Suhu (temperature) detail page.
     */
    public function suhu()
    {
        $data     = $this->fetchSensorData();
        $settings = SensorSetting::current();
        return view('suhu', array_merge($data, compact('settings')));
    }

    /**
     * Humidity detail page.
     */
    public function humidity()
    {
        $data     = $this->fetchSensorData();
        $settings = SensorSetting::current();
        return view('humidity', array_merge($data, compact('settings')));
    }

    /**
     * Air Quality detail page.
     */
    public function airQuality()
    {
        $data     = $this->fetchSensorData();
        $settings = SensorSetting::current();
        return view('air_quality', array_merge($data, compact('settings')));
    }

    /**
     * Legacy dashboard — redirect to home.
     */
    public function index()
    {
        return redirect()->route('home');
    }
}
