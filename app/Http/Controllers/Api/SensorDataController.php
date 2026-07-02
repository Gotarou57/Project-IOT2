<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SensorSetting;
use App\Services\SensorDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function __construct(protected SensorDataService $service) {}

    /**
     * GET /api/sensors/overview?range=20
     * Home dashboard — all 3 sensors + chart series.
     */
    public function overview(Request $request): JsonResponse
    {
        $range    = $request->query('range', '20');
        $data     = $this->service->fetch($range);
        $settings = SensorSetting::current();

        return response()->json([
            'temperature'  => $data['temperature'],
            'humidity'     => $data['humidity'],
            'airQuality'   => $data['airQuality'],
            'timestamp'    => $data['timestamp'],
            'apiError'     => $data['apiError'],
            'series' => [
                'labels'       => $data['timestamps'],
                'temperatures' => $data['temperatures'],
                'humidities'   => $data['humidities'],
                'airQualities' => $data['airQualities'],
            ],
            'settings' => [
                'temperature_enabled' => $settings->temperature_enabled,
                'humidity_enabled'    => $settings->humidity_enabled,
                'air_quality_enabled' => $settings->air_quality_enabled,
                'refresh_delay'       => $settings->refresh_delay,
            ],
        ]);
    }

    /**
     * GET /api/sensors/temperature?range=20
     */
    public function temperature(Request $request): JsonResponse
    {
        return $this->singleSensor('temperature', $request->query('range', '20'));
    }

    /**
     * GET /api/sensors/humidity?range=20
     */
    public function humidity(Request $request): JsonResponse
    {
        return $this->singleSensor('humidity', $request->query('range', '20'));
    }

    /**
     * GET /api/sensors/air-quality?range=20
     */
    public function airQuality(Request $request): JsonResponse
    {
        return $this->singleSensor('air-quality', $request->query('range', '20'));
    }

    // ─── shared ─────────────────────────────────────────────────

    private function singleSensor(string $type, string $range): JsonResponse
    {
        $data = $this->service->fetchSingle($type, $range);

        $seriesKey = match ($type) {
            'temperature' => 'temperatures',
            'humidity'    => 'humidities',
            'air-quality' => 'airQualities',
            default       => 'temperatures',
        };

        return response()->json([
            'current'   => $data['current'],
            'timestamp' => $data['timestamp'],
            'apiError'  => $data['apiError'],
            'min'       => $data['min'],
            'max'       => $data['max'],
            'avg'       => $data['avg'],
            'series' => [
                'labels' => $data['timestamps'],
                'values' => $data[$seriesKey],
            ],
        ]);
    }
}
