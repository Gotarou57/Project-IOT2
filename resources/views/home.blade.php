@extends('layouts.app')

@section('title', 'Home')
@section('page-title', 'Home — Overview')

{{-- Auto-refresh meta --}}
@push('scripts')
<script>
    // Auto-refresh based on settings delay
    setTimeout(() => location.reload(), {{ ($settings->refresh_delay ?? 30) * 1000 }});
</script>
@endpush

@section('content')

{{-- ── Welcome Banner ── --}}
<div style="background:linear-gradient(135deg,#6366f1 0%,#8b5cf6 50%,#06b6d4 100%);border-radius:20px;padding:32px;margin-bottom:28px;position:relative;overflow:hidden;">
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
    <div class="stat-card" style="{{ !$settings->temperature_enabled ? 'opacity:0.55;' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#fef2f2,#fee2e2);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/>
                </svg>
            </div>
            @if($settings->temperature_enabled)
            <a href="{{ route('suhu') }}" style="font-size:0.75rem;color:#6366f1;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px;">
                Detail
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            @else
            <span style="font-size:0.7rem;background:#fef2f2;color:#ef4444;padding:2px 10px;border-radius:99px;font-weight:700;border:1px solid #fecaca;">Disabled</span>
            @endif
        </div>
        <p style="font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 6px;">Temperature</p>
        <div style="display:flex;align-items:baseline;gap:4px;">
            <span style="font-size:2.6rem;font-weight:900;color:#0f172a;line-height:1;">
                @if($settings->temperature_enabled) {{ $temperature }} @else — @endif
            </span>
            @if($settings->temperature_enabled)<span style="font-size:1.1rem;font-weight:500;color:#94a3b8;">°C</span>@endif
        </div>
        <div style="margin-top:10px;height:4px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ ($settings->temperature_enabled && is_numeric($temperature)) ? min(100, ($temperature/60)*100) : 0 }}%;background:linear-gradient(90deg,#f97316,#ef4444);border-radius:99px;transition:width 0.6s ease;"></div>
        </div>
    </div>

    {{-- Humidity Card --}}
    <div class="stat-card" style="{{ !$settings->humidity_enabled ? 'opacity:0.55;' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/>
                </svg>
            </div>
            @if($settings->humidity_enabled)
            <a href="{{ route('humidity') }}" style="font-size:0.75rem;color:#6366f1;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px;">
                Detail
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            @else
            <span style="font-size:0.7rem;background:#eff6ff;color:#3b82f6;padding:2px 10px;border-radius:99px;font-weight:700;border:1px solid #bfdbfe;">Disabled</span>
            @endif
        </div>
        <p style="font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 6px;">Humidity</p>
        <div style="display:flex;align-items:baseline;gap:4px;">
            <span style="font-size:2.6rem;font-weight:900;color:#0f172a;line-height:1;">
                @if($settings->humidity_enabled) {{ $humidity }} @else — @endif
            </span>
            @if($settings->humidity_enabled)<span style="font-size:1.1rem;font-weight:500;color:#94a3b8;">%</span>@endif
        </div>
        <div style="margin-top:10px;height:4px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ ($settings->humidity_enabled && is_numeric($humidity)) ? min(100, $humidity) : 0 }}%;background:linear-gradient(90deg,#60a5fa,#3b82f6);border-radius:99px;transition:width 0.6s ease;"></div>
        </div>
    </div>

    {{-- Air Quality Card --}}
    <div class="stat-card" style="{{ !$settings->air_quality_enabled ? 'opacity:0.55;' : '' }}">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#faf5ff,#f3e8ff);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#a855f7" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                </svg>
            </div>
            @if($settings->air_quality_enabled)
            <a href="{{ route('air-quality') }}" style="font-size:0.75rem;color:#6366f1;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:3px;">
                Detail
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
            @else
            <span style="font-size:0.7rem;background:#faf5ff;color:#a855f7;padding:2px 10px;border-radius:99px;font-weight:700;border:1px solid #e9d5ff;">Disabled</span>
            @endif
        </div>
        <p style="font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 6px;">Air Quality</p>
        <div style="display:flex;align-items:baseline;gap:4px;">
            <span style="font-size:2.6rem;font-weight:900;color:#0f172a;line-height:1;">
                @if($settings->air_quality_enabled) {{ $airQuality }} @else — @endif
            </span>
            @if($settings->air_quality_enabled)<span style="font-size:1.1rem;font-weight:500;color:#94a3b8;">PPM</span>@endif
        </div>
        <div style="margin-top:10px;height:4px;background:#f1f5f9;border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ ($settings->air_quality_enabled && is_numeric($airQuality)) ? min(100, ($airQuality/1000)*100) : 0 }}%;background:linear-gradient(90deg,#d8b4fe,#a855f7);border-radius:99px;transition:width 0.6s ease;"></div>
        </div>
    </div>

    {{-- Status Card --}}
    <div class="stat-card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span style="font-size:0.72rem;background:#f0fdf4;color:#16a34a;padding:3px 10px;border-radius:99px;font-weight:700;border:1px solid #bbf7d0;">Online</span>
        </div>
        <p style="font-size:0.75rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 6px;">System Status</p>
        <div style="font-size:1.5rem;font-weight:800;color:#0f172a;">Connected</div>
        <p style="font-size:0.78rem;color:#94a3b8;margin:6px 0 0;">ThingSpeak IoT Channel Active</p>
    </div>
</div>

{{-- ── Combined Chart ── --}}
<div class="chart-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;flex-direction:column;gap:8px;">
            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin:0;">Environmental Trends</h3>
            <div style="display:flex; background:#f1f5f9; padding:4px; border-radius:8px; font-size:0.75rem; font-weight:600; color:#64748b; align-items:center; gap:2px; width:fit-content;">
                @php $currRange = request()->query('range', '20'); @endphp
                <a href="?range=20" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == '20' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == '20' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == '20' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">20 Pts</a>
                <a href="?range=50" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == '50' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == '50' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == '50' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">50 Pts</a>
                <a href="?range=60m" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == '60m' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == '60m' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == '60m' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">1 Hour</a>
                <a href="?range=360m" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == '360m' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == '360m' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == '360m' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">6 Hours</a>
                <a href="?range=1440m" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == '1440m' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == '1440m' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == '1440m' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">24 Hours</a>
                <a href="?range=10080m" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == '10080m' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == '10080m' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == '10080m' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">1 Wk</a>
                <a href="?range=43200m" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == '43200m' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == '43200m' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == '43200m' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">1 Mo</a>
                <a href="?range=all" style="padding:4px 12px; border-radius:6px; text-decoration:none; color:{{ $currRange == 'all' ? '#0f172a' : '#64748b' }}; background:{{ $currRange == 'all' ? '#fff' : 'transparent' }}; box-shadow:{{ $currRange == 'all' ? '0 1px 2px rgba(0,0,0,0.05)' : 'none' }}; transition:all 0.2s;">Max</a>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:16px;font-size:0.78rem;">
            <button id="toggle-chart-view" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);border:1px solid #e2e8f0;padding:6px 12px;border-radius:8px;color:#475569;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:all 0.2s ease;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg>
                Separate Views
            </button>
            <div id="chart-legend" style="display:flex;gap:16px;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="width:12px;height:12px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                    <span style="color:#64748b;font-weight:500;">Temperature (°C)</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="width:12px;height:12px;border-radius:50%;background:#3b82f6;display:inline-block;"></span>
                    <span style="color:#64748b;font-weight:500;">Humidity (%)</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="width:12px;height:12px;border-radius:50%;background:#a855f7;display:inline-block;"></span>
                    <span style="color:#64748b;font-weight:500;">Air Quality (PPM)</span>
                </div>
            </div>
        </div>
    </div>
    <div id="combined-chart-container" style="height:340px;position:relative;">
        <canvas id="homeChart"></canvas>
    </div>
    <div id="separated-charts-container" style="display:none;flex-direction:column;gap:20px;">
        <div style="height:200px;position:relative;">
            <canvas id="tempChart"></canvas>
        </div>
        <div style="height:200px;position:relative;">
            <canvas id="humChart"></canvas>
        </div>
        <div style="height:200px;position:relative;">
            <canvas id="aqChart"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('homeChart').getContext('2d');
    const labels  = @json($timestamps);
    const tempData = @json($temperatures);
    const humData  = @json($humidities);
    const aqData   = @json($airQualities);

    const validTemp = tempData.filter(v => v !== null);
    const validHum  = humData.filter(v => v !== null);
    const validAq   = aqData.filter(v => v !== null);
    
    function getScaleBounds(validData) {
        if (!validData.length) return { min: 0, max: 100 };
        const max = Math.max(...validData);
        const min = Math.min(...validData);
        const diff = max - min;
        const padding = diff === 0 ? (max === 0 ? 1 : Math.abs(max * 0.1)) : diff * 0.1;
        return {
            min: Math.floor(min - padding),
            max: Math.ceil(max + padding)
        };
    }

    const tempBounds = getScaleBounds(validTemp);
    const humBounds = getScaleBounds(validHum);
    const aqBounds = getScaleBounds(validAq);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Temperature (°C)',
                    data: tempData,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239,68,68,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointBackgroundColor: '#ef4444',
                    tension: 0.4,
                    yAxisID: 'y-temp',
                    fill: true,
                    spanGaps: true
                },
                {
                    label: 'Humidity (%)',
                    data: humData,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59,130,246,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointBackgroundColor: '#3b82f6',
                    tension: 0.4,
                    yAxisID: 'y-hum',
                    fill: true,
                    spanGaps: true
                },
                {
                    label: 'Air Quality (PPM)',
                    data: aqData,
                    borderColor: '#a855f7',
                    backgroundColor: 'rgba(168,85,247,0.08)',
                    borderWidth: 2.5,
                    pointRadius: 0,
                    pointBackgroundColor: '#a855f7',
                    tension: 0.4,
                    yAxisID: 'y-aq',
                    fill: true,
                    spanGaps: true
                }
            ]
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
                    bodyColor: '#cbd5e1',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: true
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#94a3b8', font: { size: 11 } }
                },
                'y-temp': {
                    type: 'linear', position: 'left',
                    min: tempBounds.min, max: tempBounds.max,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { color: '#ef4444', font: { weight: '600' } },
                    title: { display: true, text: 'Temp (°C)', color: '#ef4444', font: { weight: '600' } }
                },
                'y-hum': {
                    type: 'linear', position: 'right',
                    min: humBounds.min, max: humBounds.max,
                    grid: { display: false },
                    ticks: { color: '#3b82f6', font: { weight: '600' } },
                    title: { display: true, text: 'Humidity (%)', color: '#3b82f6', font: { weight: '600' } }
                },
                'y-aq': {
                    type: 'linear', position: 'right',
                    min: aqBounds.min, max: aqBounds.max,
                    grid: { display: false },
                    ticks: { color: '#a855f7', font: { weight: '600' } },
                    title: { display: true, text: 'Air Quality (PPM)', color: '#a855f7', font: { weight: '600' } }
                }
            }
        }
    });

    // Separated charts
    const commonOptions = {
        animation: false,
        responsive: true,
        maintainAspectRatio: false,
        interaction: { intersect: false, mode: 'index' },
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(15,23,42,0.92)',
                titleColor: '#f8fafc',
                bodyColor: '#cbd5e1',
                padding: 12,
                cornerRadius: 10,
                displayColors: true
            }
        },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
        }
    };

    new Chart(document.getElementById('tempChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Temperature (°C)',
                data: tempData,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.08)',
                borderWidth: 2.5, pointRadius: 0, pointBackgroundColor: '#ef4444', tension: 0.4, fill: true, spanGaps: true
            }]
        },
        options: {
            ...commonOptions,
            scales: { ...commonOptions.scales, y: { min: tempBounds.min, max: tempBounds.max, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#ef4444', font: { weight: '600' } }, title: { display: true, text: 'Temp (°C)', color: '#ef4444', font: { weight: '600' } } } }
        }
    });

    new Chart(document.getElementById('humChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Humidity (%)',
                data: humData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,0.08)',
                borderWidth: 2.5, pointRadius: 0, pointBackgroundColor: '#3b82f6', tension: 0.4, fill: true, spanGaps: true
            }]
        },
        options: {
            ...commonOptions,
            scales: { ...commonOptions.scales, y: { min: humBounds.min, max: humBounds.max, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#3b82f6', font: { weight: '600' } }, title: { display: true, text: 'Humidity (%)', color: '#3b82f6', font: { weight: '600' } } } }
        }
    });

    new Chart(document.getElementById('aqChart').getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Air Quality (PPM)',
                data: aqData,
                borderColor: '#a855f7',
                backgroundColor: 'rgba(168,85,247,0.08)',
                borderWidth: 2.5, pointRadius: 0, pointBackgroundColor: '#a855f7', tension: 0.4, fill: true, spanGaps: true
            }]
        },
        options: {
            ...commonOptions,
            scales: { ...commonOptions.scales, y: { min: aqBounds.min, max: aqBounds.max, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { color: '#a855f7', font: { weight: '600' } }, title: { display: true, text: 'Air Quality (PPM)', color: '#a855f7', font: { weight: '600' } } } }
        }
    });

    // Toggle logic
    const toggleBtn = document.getElementById('toggle-chart-view');
    const combinedContainer = document.getElementById('combined-chart-container');
    const separatedContainer = document.getElementById('separated-charts-container');
    const chartLegend = document.getElementById('chart-legend');
    
    // Load saved state from localStorage
    let isSeparated = localStorage.getItem('chartViewPref') === 'separated';
    
    function applyView() {
        if (isSeparated) {
            combinedContainer.style.display = 'none';
            separatedContainer.style.display = 'flex';
            chartLegend.style.display = 'none';
            toggleBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16"/></svg> Combined View';
        } else {
            combinedContainer.style.display = 'block';
            separatedContainer.style.display = 'none';
            chartLegend.style.display = 'flex';
            toggleBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7"/></svg> Separate Views';
        }
    }
    
    // Apply initial view
    applyView();

    toggleBtn.addEventListener('click', () => {
        isSeparated = !isSeparated;
        localStorage.setItem('chartViewPref', isSeparated ? 'separated' : 'combined');
        applyView();
    });
});
</script>
@endpush
