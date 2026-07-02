<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
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
});

it('home page returns 200 and contains expected content', function () {
    $this->get('/home')->assertOk()->assertSee('EnvMonitor');
});

it('temperature page returns 200', function () {
    $this->get('/suhu')->assertOk()->assertSee('Temperature');
});

it('humidity page returns 200', function () {
    $this->get('/humidity')->assertOk()->assertSee('Humidity');
});

it('air quality page returns 200', function () {
    $this->get('/air-quality')->assertOk()->assertSee('Air Quality');
});

it('settings page returns 200', function () {
    $this->get('/settings')->assertOk()->assertSee('Sensor Configuration');
});

it('root redirects to home', function () {
    $this->get('/')->assertRedirect('/home');
});

it('settings POST saves and redirects', function () {
    $this->post('/settings', [
        'temperature_enabled' => '1',
        'humidity_enabled'    => '1',
        'air_quality_enabled' => '1',
        'refresh_delay'       => '30',
    ])->assertRedirect('/settings');
});
