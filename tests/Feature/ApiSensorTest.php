<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use App\Models\SensorSetting;

uses(RefreshDatabase::class);

it('returns valid JSON from overview endpoint', function () {
    Http::fake([
        'api.thingspeak.com/*' => Http::response([
            'feeds' => [
                [
                    'created_at' => '2025-01-01T10:00:00Z',
                    'field1' => '28.5',
                    'field2' => '65.0',
                    'field3' => '120.0',
                ],
            ],
        ], 200),
    ]);

    $response = $this->getJson('/api/sensors/overview');

    $response->assertOk()
             ->assertJsonStructure([
                 'temperature',
                 'humidity',
                 'airQuality',
                 'timestamp',
                 'apiError',
                 'series' => ['labels', 'temperatures', 'humidities', 'airQualities'],
                 'settings',
             ]);
});

it('returns false apiError on successful ThingSpeak response', function () {
    Http::fake([
        'api.thingspeak.com/*' => Http::response([
            'feeds' => [
                [
                    'created_at' => '2025-01-01T10:00:00Z',
                    'field1' => '28.5',
                    'field2' => '65.0',
                    'field3' => '120.0',
                ],
            ],
        ], 200),
    ]);

    $response = $this->getJson('/api/sensors/overview');

    $response->assertOk()->assertJson(['apiError' => false]);
});

it('returns single sensor temperature endpoint', function () {
    Http::fake([
        'api.thingspeak.com/*' => Http::response([
            'feeds' => [
                ['created_at' => '2025-01-01T10:00:00Z', 'field1' => '30.0'],
            ],
        ], 200),
    ]);

    $response = $this->getJson('/api/sensors/temperature');

    $response->assertOk()
             ->assertJsonStructure(['current', 'timestamp', 'apiError', 'min', 'max', 'avg', 'series']);
});

it('returns single sensor humidity endpoint', function () {
    Http::fake([
        'api.thingspeak.com/*' => Http::response(['feeds' => []], 200),
    ]);

    $this->getJson('/api/sensors/humidity')->assertOk();
});

it('returns single sensor air-quality endpoint', function () {
    Http::fake([
        'api.thingspeak.com/*' => Http::response(['feeds' => []], 200),
    ]);

    $this->getJson('/api/sensors/air-quality')->assertOk();
});

it('returns true apiError when ThingSpeak is unreachable', function () {
    Http::fake([
        'api.thingspeak.com/*' => Http::response(null, 500),
    ]);

    $response = $this->getJson('/api/sensors/overview');

    $response->assertOk()->assertJson(['apiError' => true]);
});
