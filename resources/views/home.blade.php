@extends('layouts.app')

@section('title', 'Home')
@section('page-title', 'Home')

@section('content')

{{-- ── Pass server data to Alpine via a JSON config object ── --}}
@php
$__homeData = [
    'temperature'  => $temperature,
    'humidity'     => $humidity,
    'airQuality'   => $airQuality,
    'timestamp'    => $timestamp,
    'apiError'     => $apiError ?? false,
    'delay'        => $settings->refresh_delay ?? 30,
    'labels'       => $timestamps,
    'temperatures' => $temperatures,
    'humidities'   => $humidities,
    'airQualities' => $airQualities,
];
@endphp
<script>
window.__HOME_DATA__ = @json($__homeData);
</script>

<div x-data="homePoller(window.__HOME_DATA__)">



{{-- ── Welcome Banner ── --}}
<div style="background:linear-gradient(135deg,#14532d 0%,#15803d 55%,#166534 100%);border-radius:20px;padding:32px;margin-bottom:28px;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 80 80%22><circle cx=%2240%22 cy=%2240%22 r=%2240%22 fill=%22rgba(255,255,255,0.04)%22/></svg>') repeat;opacity:0.4;"></div>
    <div style="position:relative;z-index:1;">
        <p style="color:rgba(255,255,255,0.7);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 8px;">Environment Monitor</p>
        <h2 style="color:#fff;font-size:1.8rem;font-weight:800;margin:0 0 6px;letter-spacing:-0.03em;">Real-time Sensor Overview</h2>
        <p style="color:rgba(255,255,255,0.65);font-size:0.9rem;margin:0;">Data streamed live from ThingSpeak IoT Channel</p>
    </div>
</div>

{{-- ── Stats Grid ── --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:28px;">

    {{-- Temperature Card --}}
    <div class="stat-card" style="{{ !$settings->temperature_enabled ? 'opacity:0.5;' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:rgba(239,68,68,0.18);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/></svg>
            </div>
            @if($settings->temperature_enabled)
            <a href="{{ route('suhu') }}" style="font-size:0.75rem;color:var(--accent);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px;" aria-label="Temperature detail page">Detail <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></a>
            @else
            <span style="font-size:0.7rem;background:rgba(239,68,68,0.15);color:#f87171;padding:2px 10px;border-radius:99px;font-weight:700;border:1px solid rgba(239,68,68,0.3);">Disabled</span>
            @endif
        </div>
        <p class="stat-label">Temperature</p>
        <div style="display:flex;align-items:baseline;gap:4px;">
            <span class="sensor-value" id="val-temp" style="font-size:2.6rem;font-weight:900;color:var(--text);line-height:1;" aria-live="polite">
                @if($settings->temperature_enabled)<span x-text="temp !== 'N/A' ? temp : '—'">{{ $temperature }}</span>@else —@endif
            </span>
            @if($settings->temperature_enabled)<span style="font-size:1.1rem;font-weight:500;color:var(--text-muted);">°C</span>@endif
        </div>
        <div class="gauge-track" style="margin-top:12px;" role="progressbar" :aria-valuenow="gaugePercent(temp,60)" aria-valuemin="0" aria-valuemax="100">
            <div class="gauge-fill" :style="'width:'+gaugePercent(temp,60)+'%;background:'+gaugeColor(gaugePercent(temp,60))"></div>
        </div>
    </div>

    {{-- Humidity Card --}}
    <div class="stat-card" style="{{ !$settings->humidity_enabled ? 'opacity:0.5;' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:rgba(59,130,246,0.18);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/></svg>
            </div>
            @if($settings->humidity_enabled)
            <a href="{{ route('humidity') }}" style="font-size:0.75rem;color:var(--accent);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px;" aria-label="Humidity detail page">Detail <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></a>
            @else
            <span style="font-size:0.7rem;background:rgba(59,130,246,0.15);color:#60a5fa;padding:2px 10px;border-radius:99px;font-weight:700;border:1px solid rgba(59,130,246,0.3);">Disabled</span>
            @endif
        </div>
        <p class="stat-label">Humidity</p>
        <div style="display:flex;align-items:baseline;gap:4px;">
            <span class="sensor-value" id="val-hum" style="font-size:2.6rem;font-weight:900;color:var(--text);line-height:1;" aria-live="polite">
                @if($settings->humidity_enabled)<span x-text="hum !== 'N/A' ? hum : '—'">{{ $humidity }}</span>@else —@endif
            </span>
            @if($settings->humidity_enabled)<span style="font-size:1.1rem;font-weight:500;color:var(--text-muted);">%</span>@endif
        </div>
        <div class="gauge-track" style="margin-top:12px;" role="progressbar" :aria-valuenow="gaugePercent(hum,100)" aria-valuemin="0" aria-valuemax="100">
            <div class="gauge-fill" :style="'width:'+gaugePercent(hum,100)+'%;background:'+gaugeColor(gaugePercent(hum,100))"></div>
        </div>
    </div>

    {{-- Air Quality Card --}}
    <div class="stat-card" style="{{ !$settings->air_quality_enabled ? 'opacity:0.5;' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:rgba(168,85,247,0.18);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#c084fc" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3H6a3 3 0 00-3 3v12a3 3 0 003 3h12a3 3 0 003-3V9.75M9.75 3v6.75H16.5M9.75 3L16.5 9.75"/></svg>
            </div>
            @if($settings->air_quality_enabled)
            <a href="{{ route('air-quality') }}" style="font-size:0.75rem;color:var(--accent);font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px;" aria-label="Air quality detail page">Detail <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></a>
            @else
            <span style="font-size:0.7rem;background:rgba(168,85,247,0.15);color:#c084fc;padding:2px 10px;border-radius:99px;font-weight:700;border:1px solid rgba(168,85,247,0.3);">Disabled</span>
            @endif
        </div>
        <p class="stat-label">Air Quality</p>
        <div style="display:flex;align-items:baseline;gap:4px;">
            <span class="sensor-value" id="val-aq" style="font-size:2.6rem;font-weight:900;color:var(--text);line-height:1;" aria-live="polite">
                @if($settings->air_quality_enabled)<span x-text="aq !== 'N/A' ? aq : '—'">{{ $airQuality }}</span>@else —@endif
            </span>
            @if($settings->air_quality_enabled)<span style="font-size:1.1rem;font-weight:500;color:var(--text-muted);">PPM</span>@endif
        </div>
        <div class="gauge-track" style="margin-top:12px;" role="progressbar" :aria-valuenow="gaugePercent(aq,1000)" aria-valuemin="0" aria-valuemax="100">
            <div class="gauge-fill" :style="'width:'+gaugePercent(aq,1000)+'%;background:'+gaugeColor(gaugePercent(aq,1000))"></div>
        </div>
    </div>

    {{-- System Status Card --}}
    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:rgba(34,197,94,0.15);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#4ade80" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <span :class="error ? '' : ''"
                  :style="error ? 'background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.3);font-size:0.72rem;padding:3px 10px;border-radius:99px;font-weight:700;' : 'background:rgba(34,197,94,0.15);color:#4ade80;border:1px solid rgba(34,197,94,0.3);font-size:0.72rem;padding:3px 10px;border-radius:99px;font-weight:700;'">
                <span x-text="error ? 'Error' : 'Online'">{{ ($apiError ?? false) ? 'Error' : 'Online' }}</span>
            </span>
        </div>
        <p class="stat-label">System Status</p>
        <div style="font-size:1.5rem;font-weight:800;color:var(--text);" x-text="error ? 'Offline' : 'Connected'">Connected</div>
        <p style="font-size:0.78rem;color:var(--text-muted);margin:6px 0 0;">ThingSpeak IoT Channel</p>
        <p style="font-size:0.72rem;color:var(--text-dim);margin:4px 0 0;" x-text="'Updated: ' + ts">Updated: {{ $timestamp }}</p>
    </div>

</div>

{{-- ── Combined Chart ── --}}
<div class="chart-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;flex-direction:column;gap:10px;">
            <h3 class="card-heading">Environmental Trends</h3>
            @php $currRange = request()->query('range', '20'); @endphp
            <div role="group" aria-label="Time range filter" style="display:flex;background:var(--bg-muted);padding:4px;border-radius:8px;font-size:0.75rem;font-weight:600;align-items:center;gap:2px;width:fit-content;">
                @foreach(['20'=>'20 Pts','50'=>'50 Pts','60m'=>'1 Hour','360m'=>'6 Hours','1440m'=>'24 Hours','10080m'=>'1 Wk','43200m'=>'1 Mo','all'=>'Max'] as $val => $label)
                <a href="?range={{ $val }}"
                   aria-current="{{ $currRange == $val ? 'true' : 'false' }}"
                   style="padding:4px 12px;border-radius:6px;text-decoration:none;white-space:nowrap;{{ $currRange == $val ? 'background:var(--bg-surface2);color:var(--text);box-shadow:0 1px 3px rgba(0,0,0,0.2);' : 'color:var(--text-dim);' }}transition:all 0.2s;">{{ $label }}</a>
                @endforeach
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:16px;font-size:0.78rem;flex-wrap:wrap;">
            <button @click="toggleChartView()"
                    style="background:var(--bg-muted);border:1px solid var(--border-solid);padding:7px 14px;border-radius:8px;color:var(--text-muted);font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all 0.2s ease;font-size:0.78rem;font-family:inherit;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                <span x-text="chartViewPref === 'separated' ? 'Combined View' : 'Separate Views'">Separate Views</span>
            </button>
            <div id="chart-legend" style="display:flex;gap:16px;" role="list">
                <div style="display:flex;align-items:center;gap:6px;" role="listitem">
                    <span style="width:12px;height:12px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                    <span style="color:var(--text-muted);font-weight:500;">Temp (°C)</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;" role="listitem">
                    <span style="width:20px;height:3px;background:#3b82f6;display:inline-block;border-radius:2px;"></span>
                    <span style="color:var(--text-muted);font-weight:500;">Humidity (%)</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;" role="listitem">
                    <span style="width:20px;height:3px;display:inline-block;border-radius:2px;border-top:2px dotted #a855f7;"></span>
                    <span style="color:var(--text-muted);font-weight:500;">Air (PPM)</span>
                </div>
            </div>
        </div>
    </div>
    <div id="combined-chart-container" style="height:340px;position:relative;">
        <canvas id="homeChart"></canvas>
    </div>
    <div id="separated-charts-container" style="display:none;flex-direction:column;gap:20px;">
        <div style="height:200px;position:relative;"><canvas id="tempChart"></canvas></div>
        <div style="height:200px;position:relative;"><canvas id="humChart"></canvas></div>
        <div style="height:200px;position:relative;"><canvas id="aqChart"></canvas></div>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection
