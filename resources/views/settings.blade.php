@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

{{-- Success Toast --}}
@if (session('success'))
<div id="toast-success" style="position:fixed;top:24px;right:24px;z-index:9999;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;padding:14px 22px;border-radius:14px;box-shadow:0 8px 30px rgba(34,197,94,0.35);display:flex;align-items:center;gap:10px;font-weight:600;font-size:0.9rem;animation:slideInToast 0.4s ease;">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- TalkBack Connection Status Banner --}}
@if($talkbackOk)
<div style="display:flex;align-items:center;gap:12px;background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:14px;padding:14px 20px;margin-bottom:24px;">
    <div style="width:36px;height:36px;background:#22c55e;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
    </div>
    <div>
        <div style="font-size:0.88rem;font-weight:700;color:#15803d;">TalkBack Connected — Commands will be sent to the IoT device</div>
        <div style="font-size:0.76rem;color:#16a34a;margin-top:2px;">Saving settings queues TEMP_ON/OFF, HUM_ON/OFF, DELAY_N commands via ThingSpeak TalkBack</div>
    </div>
</div>
@else
<div style="display:flex;align-items:center;gap:12px;background:linear-gradient(135deg,#fffbeb,#fef3c7);border:1px solid #fde68a;border-radius:14px;padding:14px 20px;margin-bottom:24px;">
    <div style="width:36px;height:36px;background:#f59e0b;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:0.88rem;font-weight:700;color:#92400e;">TalkBack Not Configured — Settings saved to dashboard only</div>
        <div style="font-size:0.76rem;color:#b45309;margin-top:2px;">
            Go to <strong>ThingSpeak → Apps → TalkBack</strong> → create a new app, then add <code style="background:#fde68a;padding:1px 5px;border-radius:4px;">TALKBACK_APP_ID</code> and <code style="background:#fde68a;padding:1px 5px;border-radius:4px;">TALKBACK_API_KEY</code> to your <code style="background:#fde68a;padding:1px 5px;border-radius:4px;">.env</code> file.
        </div>
    </div>
</div>
@endif

<style>
@keyframes slideInToast {
    from { opacity:0; transform:translateX(60px); }
    to   { opacity:1; transform:translateX(0); }
}

/* ─── Settings Page Layout ─── */
.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 28px;
}
@media (max-width: 900px) {
    .settings-grid { grid-template-columns: 1fr; }
}

.settings-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    overflow: hidden;
}
.settings-card-header {
    padding: 22px 28px 18px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 14px;
}
.settings-card-icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.settings-card-body {
    padding: 24px 28px;
}
.settings-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: #94a3b8;
    margin-bottom: 6px;
}
.settings-value {
    font-size: 1.05rem;
    font-weight: 700;
    color: #0f172a;
}

/* ─── Toggle Switch ─── */
.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 0;
    border-bottom: 1px solid #f8fafc;
}
.toggle-row:last-child { border-bottom: none; }
.toggle-info { flex: 1; }
.toggle-info strong {
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 3px;
}
.toggle-info small {
    font-size: 0.78rem;
    color: #94a3b8;
}
.toggle-switch {
    position: relative;
    width: 52px; height: 28px;
    flex-shrink: 0;
    margin-left: 16px;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: #e2e8f0;
    border-radius: 999px;
    transition: background 0.3s ease;
}
.toggle-slider::before {
    content: '';
    position: absolute;
    width: 22px; height: 22px;
    left: 3px; top: 3px;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.3s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.18);
}
.toggle-switch input:checked + .toggle-slider { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(24px); }

/* ─── Range Slider ─── */
.range-wrapper {
    padding: 8px 0 4px;
}
.range-track {
    position: relative;
    padding: 12px 0;
}
input[type=range] {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    border-radius: 99px;
    background: #e2e8f0;
    outline: none;
    cursor: pointer;
}
input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 22px; height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(99,102,241,0.4);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
input[type=range]::-webkit-slider-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 4px 14px rgba(99,102,241,0.5);
}
input[type=range]::-moz-range-thumb {
    width: 22px; height: 22px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(99,102,241,0.4);
}
.delay-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 0.73rem;
    color: #94a3b8;
    font-weight: 500;
}
.delay-display {
    text-align: center;
    margin-top: 16px;
}
.delay-display .big-num {
    font-size: 2.8rem;
    font-weight: 900;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}
.delay-display .unit {
    font-size: 0.85rem;
    color: #94a3b8;
    font-weight: 600;
    margin-top: 4px;
}
.preset-btns {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 18px;
}
.preset-btn {
    flex: 1;
    min-width: 60px;
    padding: 7px 12px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #f8fafc;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s ease;
    text-align: center;
}
.preset-btn:hover, .preset-btn.active {
    background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(139,92,246,0.08));
    border-color: rgba(99,102,241,0.4);
    color: #6366f1;
}

/* ─── Status Preview Card ─── */
.status-preview {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 20px;
    padding: 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.status-preview::before {
    content: '';
    position: absolute;
    top: -50%; right: -20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%);
    pointer-events: none;
}
.sensor-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 99px;
    font-size: 0.82rem;
    font-weight: 600;
    margin: 6px 6px 6px 0;
    border: 1px solid;
}
.sensor-pill.on {
    background: rgba(34,197,94,0.15);
    border-color: rgba(34,197,94,0.3);
    color: #4ade80;
}
.sensor-pill.off {
    background: rgba(239,68,68,0.1);
    border-color: rgba(239,68,68,0.25);
    color: #f87171;
}
.sensor-pill .pill-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
}
.sensor-pill.on .pill-dot { background: #4ade80; box-shadow: 0 0 6px rgba(74,222,128,0.6); }
.sensor-pill.off .pill-dot { background: #f87171; }

/* ─── Save Button ─── */
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 32px;
    border-radius: 14px;
    border: none;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(99,102,241,0.35);
    transition: all 0.22s ease;
    letter-spacing: 0.01em;
}
.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(99,102,241,0.5);
}
.btn-save:active { transform: translateY(0); }
</style>

<form method="POST" action="{{ route('settings.update') }}" id="settings-form">
    @csrf

    {{-- ── Page Intro ── --}}
    <div style="margin-bottom:28px;">
        <h2 style="font-size:1.6rem;font-weight:800;color:#0f172a;margin:0 0 6px;letter-spacing:-0.03em;">Sensor Configuration</h2>
        <p style="color:#64748b;font-size:0.9rem;margin:0;">
            Control which sensors are active and how often the IoT device uploads data.
            @if($talkbackOk)
                Changes are <strong style="color:#16a34a;">sent directly to your ESP32</strong> via TalkBack.
            @else
                Add TalkBack credentials to <code style="background:#f1f5f9;padding:1px 6px;border-radius:4px;font-size:0.85em;">.env</code> to also control the physical device.
            @endif
        </p>
    </div>

    <div class="settings-grid">

        {{-- ── Sensor Toggles ── --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                    <svg width="22" height="22" fill="none" stroke="#22c55e" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;color:#0f172a;">Sensor Control</div>
                    <div style="font-size:0.78rem;color:#94a3b8;margin-top:2px;">Enable or disable individual sensors</div>
                </div>
            </div>
            <div class="settings-card-body">

                {{-- Temperature Toggle --}}
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px;">
                            <div style="width:32px;height:32px;background:linear-gradient(135deg,#fef2f2,#fee2e2);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/>
                                </svg>
                            </div>
                            <strong>Temperature Sensor</strong>
                        </div>
                        <small style="margin-left:42px;">Field 1 · DHT Thermistor · °C</small>
                    </div>
                    <label class="toggle-switch" for="temperature_enabled">
                        <input type="checkbox" id="temperature_enabled" name="temperature_enabled"
                            value="1" {{ $settings->temperature_enabled ? 'checked' : '' }}
                            onchange="updatePreview()">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                {{-- Humidity Toggle --}}
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px;">
                            <div style="width:32px;height:32px;background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" fill="none" stroke="#3b82f6" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/>
                                </svg>
                            </div>
                            <strong>Humidity Sensor</strong>
                        </div>
                        <small style="margin-left:42px;">Field 2 · DHT Hygrometer · %</small>
                    </div>
                    <label class="toggle-switch" for="humidity_enabled">
                        <input type="checkbox" id="humidity_enabled" name="humidity_enabled"
                            value="1" {{ $settings->humidity_enabled ? 'checked' : '' }}
                            onchange="updatePreview()">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                {{-- Air Quality Toggle --}}
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px;">
                            <div style="width:32px;height:32px;background:linear-gradient(135deg,#faf5ff,#f3e8ff);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" fill="none" stroke="#a855f7" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 14c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/>
                                </svg>
                            </div>
                            <strong>Air Quality Sensor</strong>
                        </div>
                        <small style="margin-left:42px;">Field 3 · MQ135 · PPM</small>
                    </div>
                    <label class="toggle-switch" for="air_quality_enabled">
                        <input type="checkbox" id="air_quality_enabled" name="air_quality_enabled"
                            value="1" {{ $settings->air_quality_enabled ? 'checked' : '' }}
                            onchange="updatePreview()">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

            </div>
        </div>

        {{-- ── Refresh Delay ── --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" style="background:linear-gradient(135deg,#faf5ff,#ede9fe);">
                    <svg width="22" height="22" fill="none" stroke="#8b5cf6" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;color:#0f172a;">Refresh Delay</div>
                    <div style="font-size:0.78rem;color:#94a3b8;margin-top:2px;">Auto-refresh interval for live data</div>
                </div>
            </div>
            <div class="settings-card-body">

                <div class="delay-display">
                    <div class="big-num" id="delay-display">{{ $settings->refresh_delay }}</div>
                    <div class="unit">seconds between uploads</div>
                </div>

                <div class="range-wrapper">
                    <div class="range-track">
                        <input type="range" id="refresh_delay" name="refresh_delay"
                            min="15" max="300" step="5"
                            value="{{ max(15, $settings->refresh_delay) }}"
                            oninput="updateDelay(this.value)">
                    </div>
                    <div class="delay-labels">
                        <span>15s</span>
                        <span>1min</span>
                        <span>2min</span>
                        <span>5min</span>
                    </div>
                </div>
                <p style="font-size:0.72rem;color:#94a3b8;margin:6px 0 0;text-align:center;">
                    ⚠️ ThingSpeak requires ≥ 15s between uploads — device enforces this automatically.
                </p>

                <div class="preset-btns">
                    <button type="button" class="preset-btn" onclick="setDelay(10)"  id="preset-10">10s</button>
                    <button type="button" class="preset-btn" onclick="setDelay(30)"  id="preset-30">30s</button>
                    <button type="button" class="preset-btn" onclick="setDelay(60)"  id="preset-60">1 min</button>
                    <button type="button" class="preset-btn" onclick="setDelay(120)" id="preset-120">2 min</button>
                    <button type="button" class="preset-btn" onclick="setDelay(300)" id="preset-300">5 min</button>
                </div>

            </div>
        </div>

    </div>

    {{-- ── Status Preview ── --}}
    <div class="status-preview" style="margin-bottom:28px;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <p style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.45);margin:0 0 8px;">Current Configuration Preview</p>
                <h3 style="font-size:1.2rem;font-weight:800;color:#fff;margin:0 0 14px;letter-spacing:-0.02em;">Active Sensors</h3>
                <div id="preview-pills">
                    <span class="sensor-pill {{ $settings->temperature_enabled ? 'on' : 'off' }}" id="pill-temp">
                        <span class="pill-dot"></span>
                        Temperature
                    </span>
                    <span class="sensor-pill {{ $settings->humidity_enabled ? 'on' : 'off' }}" id="pill-hum">
                        <span class="pill-dot"></span>
                        Humidity
                    </span>
                    <span class="sensor-pill {{ $settings->air_quality_enabled ? 'on' : 'off' }}" id="pill-air">
                        <span class="pill-dot"></span>
                        Air Quality
                    </span>
                </div>
            </div>
            <div style="text-align:right;">
                <p style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.45);margin:0 0 6px;">Auto-Refresh Every</p>
                <div style="font-size:2.4rem;font-weight:900;color:#fff;line-height:1;" id="preview-delay">{{ $settings->refresh_delay }}</div>
                <div style="font-size:0.82rem;color:rgba(255,255,255,0.5);margin-top:4px;">seconds</div>
            </div>
        </div>
    </div>

    {{-- ── Save Button ── --}}
    <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="btn-save" id="save-btn">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Save Settings
        </button>
    </div>

</form>

@endsection

@push('scripts')
<script>
function updateDelay(val) {
    val = parseInt(val);
    document.getElementById('delay-display').textContent  = val;
    document.getElementById('preview-delay').textContent  = val;

    // Highlight matching preset
    [10,30,60,120,300].forEach(p => {
        const el = document.getElementById('preset-' + p);
        if (el) el.classList.toggle('active', p === val);
    });

    // Update range track fill color
    const slider = document.getElementById('refresh_delay');
    const pct    = ((val - 5) / (300 - 5)) * 100;
    slider.style.background = `linear-gradient(to right, #6366f1 ${pct}%, #e2e8f0 ${pct}%)`;
}

function setDelay(val) {
    document.getElementById('refresh_delay').value = val;
    updateDelay(val);
}

function updatePreview() {
    const tempOn = document.getElementById('temperature_enabled').checked;
    const humOn  = document.getElementById('humidity_enabled').checked;
    const airOn  = document.getElementById('air_quality_enabled').checked;

    const tempPill = document.getElementById('pill-temp');
    const humPill  = document.getElementById('pill-hum');
    const airPill  = document.getElementById('pill-air');

    tempPill.className = 'sensor-pill ' + (tempOn ? 'on' : 'off');
    humPill.className  = 'sensor-pill ' + (humOn  ? 'on' : 'off');
    airPill.className  = 'sensor-pill ' + (airOn  ? 'on' : 'off');
}

// Initialise on load
document.addEventListener('DOMContentLoaded', function () {
    updateDelay(parseInt(document.getElementById('refresh_delay').value));

    // Auto-dismiss success toast
    const toast = document.getElementById('toast-success');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s ease';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }
});
</script>
@endpush
