@extends('layouts.app')

@section('title', 'Air Quality')
@section('page-title', 'Air Quality Monitor')

@section('content')

{{-- ── Hero Card ── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:28px;" class="temp-top">

    {{-- Big Reading --}}
    <div style="background:linear-gradient(135deg,#7e22ce 0%,#a855f7 60%,#d8b4fe 100%);border-radius:20px;padding:36px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;background:rgba(255,255,255,0.06);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-50px;left:-20px;width:180px;height:180px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;">
                <div style="width:40px;height:40px;background:rgba(255,255,255,0.15);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                    </svg>
                </div>
                <span style="color:rgba(255,255,255,0.8);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.07em;">Current Air Quality</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:8px;">
                <span style="font-size:5rem;font-weight:900;color:#fff;line-height:1;letter-spacing:-0.04em;">{{ $airQuality }}</span>
                <span style="font-size:2rem;font-weight:500;color:rgba(255,255,255,0.7);">PPM</span>
            </div>
            <p style="color:rgba(255,255,255,0.6);font-size:0.8rem;margin:12px 0 0;">Updated: {{ $timestamp }}</p>
        </div>
    </div>

    {{-- Stats Panel --}}
    <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="stat-card" style="flex:1;">
            <p style="font-size:0.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 8px;">Max (Session)</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span style="font-size:1.8rem;font-weight:800;color:#9333ea;">{{ is_array($airQualities) && count($airQualities) ? max($airQualities) : 'N/A' }}</span>
                <span style="color:#94a3b8;font-weight:500;">PPM</span>
            </div>
        </div>
        <div class="stat-card" style="flex:1;">
            <p style="font-size:0.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 8px;">Min (Session)</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span style="font-size:1.8rem;font-weight:800;color:#c084fc;">{{ is_array($airQualities) && count($airQualities) ? min($airQualities) : 'N/A' }}</span>
                <span style="color:#94a3b8;font-weight:500;">PPM</span>
            </div>
        </div>
        <div class="stat-card" style="flex:1;">
            <p style="font-size:0.72rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.07em;margin:0 0 8px;">Average</p>
            <div style="display:flex;align-items:baseline;gap:4px;">
                <span style="font-size:1.8rem;font-weight:800;color:#a855f7;">
                    @if(is_array($airQualities) && count($airQualities))
                        {{ number_format(array_sum($airQualities) / count($airQualities), 1) }}
                    @else
                        N/A
                    @endif
                </span>
                <span style="color:#94a3b8;font-weight:500;">PPM</span>
            </div>
        </div>
    </div>
</div>

{{-- ── Gauge Bar ── --}}
<div class="chart-card" style="margin-bottom:24px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
        <h3 style="font-size:0.95rem;font-weight:700;color:#0f172a;margin:0;">Air Quality Level</h3>
        @php
            $aq = is_numeric($airQuality) ? (float)$airQuality : 0;
            $level = $aq < 400 ? 'Excellent' : ($aq < 1000 ? 'Good' : ($aq < 2000 ? 'Moderate' : 'Poor'));
            $levelColor = $aq < 400 ? '#22c55e' : ($aq < 1000 ? '#3b82f6' : ($aq < 2000 ? '#f97316' : '#ef4444'));
            $pct = min(100, max(0, ($aq / 3000) * 100));
        @endphp
        <span style="font-size:0.8rem;font-weight:700;color:{{ $levelColor }};">{{ $level }}</span>
    </div>
    <div style="height:16px;background:#f1f5f9;border-radius:99px;overflow:hidden;margin-bottom:8px;">
        <div style="height:100%;width:{{ $pct }}%;background:linear-gradient(90deg,#22c55e,#3b82f6,#f97316,#ef4444);border-radius:99px;transition:width 0.8s ease;"></div>
    </div>
    <div style="display:flex;justify-content:space-between;font-size:0.7rem;color:#94a3b8;font-weight:500;">
        <span>0 PPM</span><span>750 PPM</span><span>1500 PPM</span><span>2250 PPM</span><span>3000 PPM</span>
    </div>
</div>

{{-- ── Air Quality Chart ── --}}
<div class="chart-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0 0 3px;">Air Quality Trend</h3>
            <p style="font-size:0.78rem;color:#94a3b8;margin:0;">Historical readings over last 20 data points</p>
        </div>
        <div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;">
            <span style="width:10px;height:10px;border-radius:50%;background:#a855f7;display:inline-block;"></span>
            <span style="color:#64748b;font-weight:500;">Air Quality (PPM)</span>
        </div>
    </div>
    <div style="height:320px;position:relative;">
        <canvas id="aqChart"></canvas>
    </div>
</div>

<style>
    @media(max-width:640px) {
        .temp-top { grid-template-columns: 1fr !important; }
    }
</style>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('aqChart').getContext('2d');
    const labels   = @json($timestamps);
    const aqData = @json($airQualities);

    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(168,85,247,0.25)');
    gradient.addColorStop(1, 'rgba(168,85,247,0.01)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Air Quality (PPM)',
                data: aqData,
                borderColor: '#a855f7',
                backgroundColor: gradient,
                borderWidth: 3,
                pointRadius: 0,
                pointBackgroundColor: '#a855f7',
                pointBorderColor: '#fff',
                pointBorderWidth: 0,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.92)',
                    titleColor: '#f8fafc',
                    bodyColor: '#d8b4fe',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => ` ${ctx.raw} PPM`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { color: '#a855f7', font: { weight: '600' }, callback: v => v + ' PPM' },
                    title: { display: true, text: 'Air Quality (PPM)', color: '#a855f7', font: { weight: '700' } }
                }
            }
        }
    });
});
</script>
@endpush
