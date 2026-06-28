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

        $queryParams = ['api_key' => $apiKey];

        $range = request()->query('range', '20');
        if (str_ends_with($range, 'm')) {
            $minutes = (int) str_replace('m', '', $range);
            $queryParams['minutes'] = $minutes;
            $queryParams['results'] = 8000;
            
            if ($minutes >= 43200) { // 1 month (43200m)
                $queryParams['average'] = 720; // 12 hour average
            } elseif ($minutes >= 10080) { // 1 week (10080m)
                $queryParams['average'] = 60; // 1 hour average
            } elseif ($minutes >= 1440) { // 24 hours (1440m)
                $queryParams['average'] = 5; // 5 minute average
            }
        } elseif ($range === 'all') {
            $queryParams['results'] = 8000;
            $queryParams['average'] = 1440; // daily average for max range
        } else {
            $queryParams['results'] = (int) $range;
        }

        $url = "https://api.thingspeak.com/channels/{$channelId}/feeds.json";
        $response = Http::get($url, $queryParams);

        $timestamps   = [];
        $temperatures = [];
        $humidities   = [];
        $airQualities = [];

        if ($response->successful()) {
            $data  = $response->json();
            $feeds = $data['feeds'] ?? [];

            $dateFormat = 'H:i';
            if ($range === 'all') {
                $dateFormat = 'd M Y';
            } elseif (str_ends_with($range, 'm')) {
                $mins = (int) str_replace('m', '', $range);
                if ($mins >= 10080) {
                    $dateFormat = 'd M H:i';
                }
            }

            foreach ($feeds as $feed) {
                if (!isset($feed['field1']) && !isset($feed['field2']) && !isset($feed['field3'])) {
                    continue;
                }
                $timestamps[]   = Carbon::parse($feed['created_at'])->setTimezone('Asia/Jakarta')->format($dateFormat);
                $temperatures[] = isset($feed['field1']) ? round((float) $feed['field1'], 1) : null;
                $humidities[]   = isset($feed['field2']) ? round((float) $feed['field2'], 1) : null;
                $airQualities[] = isset($feed['field3']) ? round((float) $feed['field3'], 1) : null;
            }

            // Instead of taking the last value from the chart (which might be a monthly average),
            // let's fetch the absolute latest valid reading for the stats cards.
            $temperature = 'N/A';
            $humidity    = 'N/A';
            $airQuality  = 'N/A';
            $timestamp   = 'N/A';
            
            $latestResponse = Http::get("https://api.thingspeak.com/channels/{$channelId}/feeds.json", [
                'api_key' => $apiKey,
                'results' => 10 // fetch last 10 to ensure we find a non-blank one
            ]);
            
            if ($latestResponse->successful()) {
                $latestFeeds = $latestResponse->json('feeds') ?? [];
                foreach (array_reverse($latestFeeds) as $feed) {
                    if (isset($feed['field1']) || isset($feed['field2']) || isset($feed['field3'])) {
                        $temperature = isset($feed['field1']) ? round((float) $feed['field1'], 1) : 'N/A';
                        $humidity    = isset($feed['field2']) ? round((float) $feed['field2'], 1) : 'N/A';
                        $airQuality  = isset($feed['field3']) ? round((float) $feed['field3'], 1) : 'N/A';
                        $timestamp   = Carbon::parse($feed['created_at'])->setTimezone('Asia/Jakarta')->format('H:i');
                        break;
                    }
                }
            }
        } // Close the outer if ($response->successful())

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
