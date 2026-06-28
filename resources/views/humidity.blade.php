@extends('layouts.app')

@section('title', 'Humidity')
@section('page-title', 'Humidity Monitor')

@section('content')

{{-- ── Hero Card ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;" class="hum-top">

    {{-- Big Reading --}}
    <div style="background:linear-gradient(135deg,#1e3a5f 0%,#1d4ed8 60%,#06b6d4 100%);border-radius:20px;padding:36px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-50px;left:-20px;width:180px;height:180px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/>
                    </svg>
                </div>
                <span style="color:rgba(255,255,255,0.8);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;">Current Humidity</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:8px;">
                <span style="font-size:5rem;font-weight:900;color:#fff;line-height:1;letter-spacing:-0.04em;">{{ $humidity }}</span>
                <span style="font-size:2rem;font-weight:500;color:rgba(255,255,255,0.7);">%</span>
            </div>
            <p style="color:rgba(255,255,255,0.6);font-size:0.8rem;margin:12px 0 0;">Updated: {{ $timestamp }}</p>
        </div>
    </div>

    {{-- Stats Panel --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="stat-card" style="flex:1;">
            <p style="font-size:0.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 8px;">Max (Session)</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span style="font-size:1.8rem;font-weight:800;color:#3b82f6;">{{ is_array($humidities) && count($humidities) ? max($humidities) : 'N/A' }}</span>
                <span style="color:#94a3b8;font-weight:500;">%</span>
            </div>
        </div>
        <div class="stat-card" style="flex:1;">
            <p style="font-size:0.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 8px;">Min (Session)</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span style="font-size:1.8rem;font-weight:800;color:#06b6d4;">{{ is_array($humidities) && count($humidities) ? min($humidities) : 'N/A' }}</span>
                <span style="color:#94a3b8;font-weight:500;">%</span>
            </div>
        </div>
        <div class="stat-card" style="flex:1;">
            <p style="font-size:0.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 8px;">Average</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span style="font-size:1.8rem;font-weight:800;color:#38bdf8;">
                    @if(is_array($humidities) && count($humidities))
                        {{ number_format(array_sum($humidities) / count($humidities), 1) }}
                    @else
                        N/A
                    @endif
                </span>
                <span style="color:#94a3b8;font-weight:500;">%</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Gauge Bar ── --}}
<div class="chart-card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h3 style="font-size:0.95rem;font-weight:700;color:#0f172a;margin:0;">Humidity Level</h3>
        @php
            $hum = is_numeric($humidity) ? (float)$humidity : 0;
            $level = $hum < 30 ? 'Dry' : ($hum < 60 ? 'Comfortable' : ($hum < 80 ? 'Humid' : 'Very Humid'));
            $levelColor = $hum < 30 ? '#f97316' : ($hum < 60 ? '#22c55e' : ($hum < 80 ? '#3b82f6' : '#6366f1'));
            $pct = min(100, max(0, $hum));
        @endphp
        <span style="font-size:0.8rem;font-weight:700;color:{{ $levelColor }};">{{ $level }}</span>
    </div>
    <div style="height:16px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:8px;">
        <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#f97316,#22c55e,#3b82f6,#6366f1);border-radius:99px;transition:width 0.8s ease;"></div>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:0.7rem;color:#94a3b8;font-weight:500;">
        <span>0%</span><span>25%</span><span>50%</span><span>75%</span><span>100%</span>
    </div>
</div>

{{-- ── Humidity Chart ── --}}
<div class="chart-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 3px;">Humidity Trend</h3>
            <p style="font-size:0.78rem;color:#94a3b8;margin:0;">Historical readings over last 20 data points</p>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;">
            <span style="width:10px;height:10px;border-radius:50%;background:#3b82f6;display:inline-block;"></span>
            <span style="color:#64748b;font-weight:500;">Humidity (%)</span>
        </div>
    </div>
    <div style="height:320px;position:relative;">
        <canvas id="humidityChart"></canvas>
    </div>
</div>

<style>
    @media(max-width:640px) {
        .hum-top { grid-template-columns: 1fr !important; }
    }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('humidityChart').getContext('2d');
    const labels  = @json($timestamps);
    const humData = @json($humidities);

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(59,130,246,0.25)');
    gradient.addColorStop(1, 'rgba(59,130,246,0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Humidity (%)',
                data: humData,
                borderColor: '#3b82f6',
                backgroundColor: gradient,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#3b82f6',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.92)',
                    titleColor: '#f8fafc',
                    bodyColor: '#93c5fd',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => ` ${ctx.raw} %`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 11 } }
                },
                y: {
                    min: 0, max: 100,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { color: '#3b82f6', font: { weight: '600' }, callback: v => v + '%' },
                    title: { display: true, text: 'Humidity (%)', color: '#3b82f6', font: { weight: '700' } }
                }
            }
        }
    });
});
</script>
@endpush
