<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class SensorDataService
{
    /**
     * Fetch sensor data from ThingSpeak.
     *
     * @param  string  $range  Number of points ("20"), minutes suffix ("60m"), or "all"
     */
    public function fetch(string $range = '20'): array
    {
        $channelId = env('THINGSPEAK_CHANNEL_ID');
        $apiKey    = env('THINGSPEAK_READ_API_KEY');

        $queryParams = ['api_key' => $apiKey];

        if (str_ends_with($range, 'm')) {
            $minutes = (int) str_replace('m', '', $range);
            $queryParams['minutes'] = $minutes;
            $queryParams['results'] = 8000;

            if ($minutes >= 43200) {        // 1 month
                $queryParams['average'] = 720;
            } elseif ($minutes >= 10080) {  // 1 week
                $queryParams['average'] = 60;
            } elseif ($minutes >= 1440) {   // 24 hours
                $queryParams['average'] = 5;
            }
        } elseif ($range === 'all') {
            $queryParams['results'] = 8000;
            $queryParams['average'] = 1440;
        } else {
            $queryParams['results'] = max(1, (int) $range);
        }

        $url = "https://api.thingspeak.com/channels/{$channelId}/feeds.json";

        $timestamps   = [];
        $temperatures = [];
        $humidities   = [];
        $airQualities = [];
        $temperature  = 'N/A';
        $humidity     = 'N/A';
        $airQuality   = 'N/A';
        $timestamp    = 'N/A';
        $apiError     = false;

        try {
            $response = Http::timeout(8)->get($url, $queryParams);

            if ($response->successful()) {
                $feeds = $response->json('feeds') ?? [];

                $dateFormat = $this->dateFormat($range);

                foreach ($feeds as $feed) {
                    if (! isset($feed['field1']) && ! isset($feed['field2']) && ! isset($feed['field3'])) {
                        continue;
                    }
                    $timestamps[]   = Carbon::parse($feed['created_at'])->setTimezone('Asia/Jakarta')->format($dateFormat);
                    $temperatures[] = isset($feed['field1']) ? round((float) $feed['field1'], 1) : null;
                    $humidities[]   = isset($feed['field2']) ? round((float) $feed['field2'], 1) : null;
                    $airQualities[] = isset($feed['field3']) ? round((float) $feed['field3'], 1) : null;
                }

                // Fetch latest 10 to get current readings
                $latestResponse = Http::timeout(8)->get($url, [
                    'api_key' => $apiKey,
                    'results' => 10,
                ]);

                if ($latestResponse->successful()) {
                    $latestFeeds = $latestResponse->json('feeds') ?? [];
                    foreach (array_reverse($latestFeeds) as $feed) {
                        if (isset($feed['field1']) || isset($feed['field2']) || isset($feed['field3'])) {
                            $temperature = isset($feed['field1']) ? round((float) $feed['field1'], 1) : 'N/A';
                            $humidity    = isset($feed['field2']) ? round((float) $feed['field2'], 1) : 'N/A';
                            $airQuality  = isset($feed['field3']) ? round((float) $feed['field3'], 1) : 'N/A';
                            $timestamp   = Carbon::parse($feed['created_at'])->setTimezone('Asia/Jakarta')->format('H:i:s');
                            break;
                        }
                    }
                }
            } else {
                $apiError = true;
            }
        } catch (\Throwable) {
            $apiError = true;
        }

        return compact(
            'temperature',
            'humidity',
            'airQuality',
            'timestamp',
            'timestamps',
            'temperatures',
            'humidities',
            'airQualities',
            'apiError'
        );
    }

    /**
     * Return per-sensor detail with min / max / avg stats.
     */
    public function fetchSingle(string $field, string $range = '20'): array
    {
        $data = $this->fetch($range);

        $fieldMap = [
            'temperature' => ['values' => 'temperatures', 'current' => 'temperature'],
            'humidity'    => ['values' => 'humidities',   'current' => 'humidity'],
            'air-quality' => ['values' => 'airQualities', 'current' => 'airQuality'],
        ];

        $keys   = $fieldMap[$field] ?? $fieldMap['temperature'];
        $values = array_filter($data[$keys['values']], fn ($v) => $v !== null);

        $stats = [
            'min' => count($values) ? min($values) : 'N/A',
            'max' => count($values) ? max($values) : 'N/A',
            'avg' => count($values) ? round(array_sum($values) / count($values), 1) : 'N/A',
        ];

        return array_merge($data, $stats, ['current' => $data[$keys['current']]]);
    }

    // ─── helpers ────────────────────────────────────────────────

    private function dateFormat(string $range): string
    {
        if ($range === 'all') {
            return 'd M Y';
        }
        if (str_ends_with($range, 'm')) {
            $mins = (int) str_replace('m', '', $range);
            return $mins >= 10080 ? 'd M H:i' : 'H:i';
        }
        return 'H:i';
    }
}
