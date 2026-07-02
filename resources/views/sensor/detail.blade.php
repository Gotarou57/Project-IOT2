@extends('layouts.app')

@section('title', $sensorLabel)
@section('page-title', $sensorLabel . ' Monitor')

@section('content')

@php
    $heroGradient = match($sensorType) {
        'temperature' => 'linear-gradient(135deg,#7f1d1d 0%,#dc2626 60%,#f97316 100%)',
        'humidity'    => 'linear-gradient(135deg,#1e3a5f 0%,#1d4ed8 60%,#0891b2 100%)',
        'air-quality' => 'linear-gradient(135deg,#3b0764 0%,#7e22ce 60%,#a855f7 100%)',
        default       => 'linear-gradient(135deg,#1e293b 0%,#334155 100%)',
    };
    $gaugeMax = match($sensorType) {
        'temperature' => 60,
        'humidity'    => 100,
        'air-quality' => 1000,
        default       => 100,
    };
    $numericCurrent = is_numeric($current) ? (float)$current : 0;
    $pct = min(100, max(0, ($numericCurrent / $gaugeMax) * 100));

    $levelLabel = '';
    $levelColor = $sensorColor;
    if ($sensorType === 'temperature') {
        if ($numericCurrent < 20)      { $levelLabel = 'Cool';        $levelColor = '#3b82f6'; }
        elseif ($numericCurrent < 30)  { $levelLabel = 'Comfortable'; $levelColor = '#22c55e'; }
        elseif ($numericCurrent < 38)  { $levelLabel = 'Warm';        $levelColor = '#f97316'; }
        else                           { $levelLabel = 'Hot';          $levelColor = '#ef4444'; }
    } elseif ($sensorType === 'humidity') {
        if ($numericCurrent < 30)      { $levelLabel = 'Dry';         $levelColor = '#f59e0b'; }
        elseif ($numericCurrent < 60)  { $levelLabel = 'Comfortable'; $levelColor = '#22c55e'; }
        else                           { $levelLabel = 'Humid';        $levelColor = '#3b82f6'; }
    } elseif ($sensorType === 'air-quality') {
        if ($numericCurrent < 200)     { $levelLabel = 'Good';        $levelColor = '#22c55e'; }
        elseif ($numericCurrent < 500) { $levelLabel = 'Moderate';    $levelColor = '#f59e0b'; }
        else                           { $levelLabel = 'Poor';         $levelColor = '#ef4444'; }
    }

    $valuesArray = $$sensorField;
    $apiRoute    = '/api/sensors/' . $sensorType;

    $__sensorData = [
        'current'   => $current,
        'timestamp' => $timestamp,
        'min'       => $min,
        'max'       => $max,
        'avg'       => $avg,
        'apiError'  => $apiError ?? false,
        'delay'     => $settings->refresh_delay ?? 30,
        'labels'    => $timestamps,
        'values'    => $valuesArray,
        'label'     => $sensorLabel . ' (' . $sensorUnit . ')',
        'color'     => $sensorColor,
        'unit'      => $sensorUnit,
        'apiRoute'  => $apiRoute,
    ];
@endphp

<script>
window.__SENSOR_DATA__ = @json($__sensorData);
</script>

<div x-data="sensorDetailPoller(window.__SENSOR_DATA__)">



{{-- ── Hero + Stats Panel ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;" class="sensor-top-grid">

    {{-- Hero Card --}}
    <div style="background:{{ $heroGradient }};border-radius:20px;padding:36px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-50px;left:-20px;width:180px;height:180px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    @if($sensorType === 'temperature')
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/></svg>
                    @elseif($sensorType === 'humidity')
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/></svg>
                    @else
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3H6a3 3 0 00-3 3v12a3 3 0 003 3h12a3 3 0 003-3V9.75M9.75 3v6.75H16.5M9.75 3L16.5 9.75"/></svg>
                    @endif
                </div>
                <span style="color:rgba(255,255,255,0.8);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;">Current {{ $sensorLabel }}</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:8px;">
                <span class="sensor-value" id="val-current" style="font-size:5rem;font-weight:900;color:#fff;line-height:1;letter-spacing:-0.04em;" aria-live="polite" x-text="current !== 'N/A' && current !== null ? current : '—'">{{ $current }}</span>
                <span style="font-size:2rem;font-weight:500;color:rgba(255,255,255,0.7);">{{ $sensorUnit }}</span>
            </div>
            <p style="color:rgba(255,255,255,0.6);font-size:0.8rem;margin:12px 0 0;">Updated: <span x-text="ts">{{ $timestamp }}</span></p>
        </div>
    </div>

    {{-- Stats Panel --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="stat-card" style="flex:1;">
            <p class="stat-label">Max (Session)</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span class="sensor-value" style="font-size:1.8rem;font-weight:800;color:{{ $sensorColor }};" x-text="max !== null ? max : '—'">{{ $max }}</span>
                <span style="color:var(--text-muted);font-weight:500;">{{ $sensorUnit }}</span>
            </div>
        </div>
        <div class="stat-card" style="flex:1;">
            <p class="stat-label">Min (Session)</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span class="sensor-value" style="font-size:1.8rem;font-weight:800;color:var(--text-muted);" x-text="min !== null ? min : '—'">{{ $min }}</span>
                <span style="color:var(--text-muted);font-weight:500;">{{ $sensorUnit }}</span>
            </div>
        </div>
        <div class="stat-card" style="flex:1;">
            <p class="stat-label">Average</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span class="sensor-value" style="font-size:1.8rem;font-weight:800;color:#f59e0b;" x-text="avg !== null ? avg : '—'">{{ $avg }}</span>
                <span style="color:var(--text-muted);font-weight:500;">{{ $sensorUnit }}</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Gauge Bar ── --}}
<div class="chart-card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h3 class="card-heading">{{ $sensorLabel }} Level</h3>
        @if($levelLabel)
        <span style="font-size:0.8rem;font-weight:700;color:{{ $levelColor }};">{{ $levelLabel }}</span>
        @endif
    </div>
    <div class="gauge-track"
         role="progressbar"
         :aria-valuenow="gaugePct(current, {{ $gaugeMax }})"
         aria-valuemin="0" aria-valuemax="100"
         aria-label="{{ $sensorLabel }} level gauge">
        <div class="gauge-fill"
             :style="'width:'+gaugePct(current,{{ $gaugeMax }})+'%;background:'+gaugeColor(gaugePct(current,{{ $gaugeMax }}))">
        </div>
    </div>
    <div class="gauge-labels">
        @if($sensorType === 'temperature')
            <span>0°C</span><span>15°C</span><span>30°C</span><span>45°C</span><span>60°C</span>
        @elseif($sensorType === 'humidity')
            <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
        @else
            <span>0</span><span>250</span><span>500</span><span>750</span><span>1000 PPM</span>
        @endif
    </div>
</div>

{{-- ── Range Filter + Trend Chart ── --}}
<div class="chart-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
        <div>
            <h3 class="card-heading">{{ $sensorLabel }} Trend</h3>
            <p class="card-subheading" style="margin-top:4px;">Historical readings for selected range</p>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;">
            <span style="width:10px;height:10px;border-radius:50%;background:{{ $sensorColor }};display:inline-block;"></span>
            <span style="color:var(--text-dim);font-weight:500;">{{ $sensorLabel }} ({{ $sensorUnit }})</span>
        </div>
    </div>

    @php $currRange = request()->query('range', '20'); @endphp
    <div role="group" aria-label="Time range filter" style="display:flex;background:var(--bg-muted);padding:4px;border-radius:8px;font-size:0.75rem;font-weight:600;align-items:center;gap:2px;width:fit-content;margin-bottom:20px;flex-wrap:wrap;">
        @foreach(['20'=>'20 Pts','50'=>'50 Pts','60m'=>'1 Hour','360m'=>'6 Hours','1440m'=>'24 Hours','10080m'=>'1 Wk','43200m'=>'1 Mo','all'=>'Max'] as $val => $rangeLabel)
        <a href="?range={{ $val }}"
           aria-current="{{ $currRange == $val ? 'true' : 'false' }}"
           style="padding:4px 12px;border-radius:6px;text-decoration:none;white-space:nowrap;{{ $currRange == $val ? 'background:var(--bg-surface2);color:var(--text);' : 'color:var(--text-dim);' }}transition:all 0.2s;">{{ $rangeLabel }}</a>
        @endforeach
    </div>

    <div style="height:320px;position:relative;">
        <canvas id="sensorChart"></canvas>
    </div>
</div>

</div>{{-- end x-data --}}

<style>
@media(max-width:640px) {
    .sensor-top-grid { grid-template-columns: 1fr !important; }
}
</style>
@endsection
