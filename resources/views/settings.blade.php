@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')

{{-- ── Success Toast ── --}}
@if (session('success'))
<div id="toast-success" style="position:fixed;top:24px;right:24px;z-index:9999;background:linear-gradient(135deg,#22c55e,#16a34a);color:#fff;padding:14px 22px;border-radius:14px;box-shadow:0 8px 30px rgba(34,197,94,0.35);display:flex;align-items:center;gap:10px;font-weight:600;font-size:0.9rem;" role="alert" aria-live="assertive">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- ── TalkBack Status Banner ── --}}
@if($talkbackOk)
<div style="display:flex;align-items:center;gap:12px;background:rgba(34,197,94,0.10);border:1px solid rgba(34,197,94,0.28);border-radius:14px;padding:14px 20px;margin-bottom:24px;">
    <div style="width:36px;height:36px;background:rgba(34,197,94,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="18" height="18" fill="none" stroke="#4ade80" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
    </div>
    <div>
        <div style="font-size:0.88rem;font-weight:700;color:#4ade80;">TalkBack Connected — Commands will be sent to the IoT device</div>
        <div style="font-size:0.76rem;color:#86efac;margin-top:2px;">Saving settings queues TEMP_ON/OFF, HUM_ON/OFF, DELAY_N commands via ThingSpeak TalkBack</div>
    </div>
</div>
@else
<div style="display:flex;align-items:center;gap:12px;background:rgba(245,158,11,0.10);border:1px solid rgba(245,158,11,0.28);border-radius:14px;padding:14px 20px;margin-bottom:24px;">
    <div style="width:36px;height:36px;background:rgba(245,158,11,0.18);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="18" height="18" fill="none" stroke="#fbbf24" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
    </div>
    <div style="flex:1;">
        <div style="font-size:0.88rem;font-weight:700;color:#fbbf24;">TalkBack Not Configured — Settings saved to dashboard only</div>
        <div style="font-size:0.76rem;color:#fcd34d;margin-top:2px;">
            Go to <strong>ThingSpeak → Apps → TalkBack</strong> → create a new app, then add
            <code style="background:rgba(245,158,11,0.15);padding:1px 5px;border-radius:4px;font-size:0.85em;">TALKBACK_APP_ID</code> and
            <code style="background:rgba(245,158,11,0.15);padding:1px 5px;border-radius:4px;font-size:0.85em;">TALKBACK_API_KEY</code>
            to your <code style="background:rgba(245,158,11,0.15);padding:1px 5px;border-radius:4px;font-size:0.85em;">.env</code> file.
        </div>
    </div>
</div>
@endif

<style>
@keyframes slideInToast {
    from { opacity:0; transform:translateX(60px); }
    to   { opacity:1; transform:translateX(0); }
}
#toast-success { animation: slideInToast 0.4s ease; }

/* ─── Grid ─── */
.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 28px;
}
@media (max-width: 900px) {
    .settings-grid { grid-template-columns: 1fr; }
}

/* ─── Settings Cards (dark) ─── */
.settings-card {
    background: var(--bg-surface);
    border-radius: 20px;
    border: 1px solid var(--border);
    box-shadow: var(--shadow);
    overflow: hidden;
}
.settings-card-header {
    padding: 22px 28px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
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

/* ─── Toggle Row ─── */
.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 0;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.toggle-row:last-child { border-bottom: none; }
.toggle-info { flex: 1; }
.toggle-info strong {
    display: block;
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 3px;
}
.toggle-info small {
    font-size: 0.78rem;
    color: var(--text-dim);
}

/* ─── Toggle Switch ─── */
.toggle-switch {
    position: relative;
    width: 52px; height: 28px;
    flex-shrink: 0;
    margin-left: 16px;
    cursor: pointer;
}
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: var(--bg-muted);
    border-radius: 999px;
    transition: background 0.3s ease;
}
.toggle-slider::before {
    content: '';
    position: absolute;
    width: 22px; height: 22px;
    left: 3px; top: 3px;
    background: var(--text-muted);
    border-radius: 50%;
    transition: transform 0.3s ease, background 0.3s ease;
    box-shadow: 0 1px 4px rgba(0,0,0,0.4);
}
.toggle-switch input:checked + .toggle-slider {
    background: linear-gradient(135deg, #16a34a, #22c55e);
}
.toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(24px);
    background: #fff;
}
.toggle-switch:focus-within .toggle-slider {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
}

/* ─── Range Slider ─── */
.range-wrapper { padding: 8px 0 4px; }
.range-track   { position: relative; padding: 12px 0; }

input[type=range] {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 6px;
    border-radius: 99px;
    background: var(--bg-muted);
    outline: none;
    cursor: pointer;
}
input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 22px; height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(34,197,94,0.4);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
input[type=range]::-webkit-slider-thumb:hover {
    transform: scale(1.15);
    box-shadow: 0 4px 14px rgba(34,197,94,0.55);
}
input[type=range]::-moz-range-thumb {
    width: 22px; height: 22px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(34,197,94,0.4);
}

.delay-labels {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 0.73rem;
    color: var(--text-dim);
    font-weight: 500;
}
.delay-display { text-align: center; margin-top: 16px; }
.delay-display .big-num {
    font-family: 'Fira Code', monospace;
    font-size: 2.8rem;
    font-weight: 900;
    color: var(--accent);
    line-height: 1;
    text-shadow: 0 0 20px rgba(34,197,94,0.3);
}
.delay-display .unit {
    font-size: 0.85rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 4px;
}

/* ─── Preset Buttons ─── */
.preset-btns {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 18px;
}
.preset-btn {
    flex: 1;
    min-width: 60px;
    padding: 8px 12px;
    border-radius: 10px;
    border: 1.5px solid var(--border-solid);
    background: var(--bg-muted);
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.18s ease;
    text-align: center;
    font-family: 'Fira Code', monospace;
}
.preset-btn:hover, .preset-btn.active {
    background: rgba(34,197,94,0.12);
    border-color: rgba(34,197,94,0.4);
    color: var(--accent);
}
.preset-btn:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

/* ─── Status Preview ─── */
.status-preview {
    background: linear-gradient(135deg, #0a1020 0%, #0f172a 50%, #1a2540 100%);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 28px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 28px;
}
.status-preview::before {
    content: '';
    position: absolute;
    top: -50%; right: -20%;
    width: 300px; height: 300px;
    background: radial-gradient(circle, rgba(34,197,94,0.12) 0%, transparent 70%);
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
    transition: all 0.2s ease;
}
.sensor-pill.on  { background: rgba(34,197,94,0.15);  border-color: rgba(34,197,94,0.3);  color: #4ade80; }
.sensor-pill.off { background: rgba(239,68,68,0.10); border-color: rgba(239,68,68,0.25); color: #f87171; }
.pill-dot { width: 7px; height: 7px; border-radius: 50%; }
.sensor-pill.on  .pill-dot { background: #4ade80; box-shadow: 0 0 6px rgba(74,222,128,0.6); }
.sensor-pill.off .pill-dot { background: #f87171; }

/* ─── Save Button ─── */
.btn-save {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 13px 32px;
    border-radius: 14px;
    border: none;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    color: #fff;
    font-size: 0.95rem;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(34,197,94,0.35);
    transition: all 0.22s ease;
    letter-spacing: 0.01em;
    font-family: 'Fira Sans', sans-serif;
}
.btn-save:hover  { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(34,197,94,0.5); }
.btn-save:active { transform: translateY(0); }
.btn-save:focus-visible { outline: 2px solid #fff; outline-offset: 3px; }
</style>

<form method="POST" action="{{ route('settings.update') }}" id="settings-form">
    @csrf

    {{-- ── Page Intro ── --}}
    <div style="margin-bottom:28px;">
        <h2 style="font-size:1.6rem;font-weight:800;color:var(--text);margin:0 0 6px;letter-spacing:-0.03em;">Sensor Configuration</h2>
        <p style="color:var(--text-muted);font-size:0.9rem;margin:0;">
            Control which sensors are active and how often the IoT device uploads data.
            @if($talkbackOk)
                Changes are <strong style="color:var(--accent);">sent directly to your ESP32</strong> via TalkBack.
            @else
                Add TalkBack credentials to <code style="background:var(--bg-muted);padding:1px 6px;border-radius:4px;font-size:0.85em;">.env</code> to also control the physical device.
            @endif
        </p>
    </div>

    <div class="settings-grid">

        {{-- ── Sensor Toggles ── --}}
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon" style="background:rgba(34,197,94,0.15);">
                    <svg width="22" height="22" fill="none" stroke="#4ade80" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;color:var(--text);">Sensor Control</div>
                    <div style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Enable or disable individual sensors</div>
                </div>
            </div>
            <div class="settings-card-body">

                {{-- Temperature --}}
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px;">
                            <div style="width:32px;height:32px;background:rgba(239,68,68,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" fill="none" stroke="#f87171" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/></svg>
                            </div>
                            <strong>Temperature Sensor</strong>
                        </div>
                        <small style="margin-left:42px;">Field 1 · DHT Thermistor · °C</small>
                    </div>
                    <label class="toggle-switch" for="temperature_enabled" aria-label="Enable temperature sensor">
                        <input type="checkbox" id="temperature_enabled" name="temperature_enabled"
                            value="1" {{ $settings->temperature_enabled ? 'checked' : '' }}
                            onchange="updatePreview()">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                {{-- Humidity --}}
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px;">
                            <div style="width:32px;height:32px;background:rgba(59,130,246,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" fill="none" stroke="#60a5fa" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/></svg>
                            </div>
                            <strong>Humidity Sensor</strong>
                        </div>
                        <small style="margin-left:42px;">Field 2 · DHT Hygrometer · %</small>
                    </div>
                    <label class="toggle-switch" for="humidity_enabled" aria-label="Enable humidity sensor">
                        <input type="checkbox" id="humidity_enabled" name="humidity_enabled"
                            value="1" {{ $settings->humidity_enabled ? 'checked' : '' }}
                            onchange="updatePreview()">
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                {{-- Air Quality --}}
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px;">
                            <div style="width:32px;height:32px;background:rgba(168,85,247,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="16" height="16" fill="none" stroke="#c084fc" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3H6a3 3 0 00-3 3v12a3 3 0 003 3h12a3 3 0 003-3V9.75M9.75 3v6.75H16.5M9.75 3L16.5 9.75"/></svg>
                            </div>
                            <strong>Air Quality Sensor</strong>
                        </div>
                        <small style="margin-left:42px;">Field 3 · MQ135 · PPM</small>
                    </div>
                    <label class="toggle-switch" for="air_quality_enabled" aria-label="Enable air quality sensor">
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
                <div class="settings-card-icon" style="background:rgba(34,197,94,0.12);">
                    <svg width="22" height="22" fill="none" stroke="#4ade80" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:1rem;font-weight:700;color:var(--text);">Refresh Delay</div>
                    <div style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;">Auto-refresh interval for live data</div>
                </div>
            </div>
            <div class="settings-card-body">

                <div class="delay-display">
                    <div class="big-num" id="delay-display" aria-live="polite" aria-label="Refresh delay in seconds">{{ max(15, $settings->refresh_delay) }}</div>
                    <div class="unit">seconds between uploads</div>
                </div>

                <div class="range-wrapper">
                    <div class="range-track">
                        <input type="range" id="refresh_delay" name="refresh_delay"
                            min="15" max="300" step="5"
                            value="{{ max(15, $settings->refresh_delay) }}"
                            oninput="updateDelay(this.value)"
                            aria-label="Refresh delay slider"
                            aria-valuemin="15" aria-valuemax="300">
                    </div>
                    <div class="delay-labels">
                        <span>15s</span>
                        <span>1 min</span>
                        <span>2 min</span>
                        <span>5 min</span>
                    </div>
                </div>
                <p style="font-size:0.72rem;color:var(--text-dim);margin:6px 0 0;text-align:center;">
                    ThingSpeak requires ≥ 15s between uploads — enforced automatically.
                </p>

                <div class="preset-btns" role="group" aria-label="Quick delay presets">
                    <button type="button" class="preset-btn" onclick="setDelay(15)"  id="preset-15"  aria-label="Set 15 seconds">15s</button>
                    <button type="button" class="preset-btn" onclick="setDelay(30)"  id="preset-30"  aria-label="Set 30 seconds">30s</button>
                    <button type="button" class="preset-btn" onclick="setDelay(60)"  id="preset-60"  aria-label="Set 1 minute">1 min</button>
                    <button type="button" class="preset-btn" onclick="setDelay(120)" id="preset-120" aria-label="Set 2 minutes">2 min</button>
                    <button type="button" class="preset-btn" onclick="setDelay(300)" id="preset-300" aria-label="Set 5 minutes">5 min</button>
                </div>

            </div>
        </div>

    </div>

    {{-- ── Status Preview ── --}}
    <div class="status-preview">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div>
                <p style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.45);margin:0 0 8px;">Current Configuration Preview</p>
                <h3 style="font-size:1.2rem;font-weight:800;color:#fff;margin:0 0 14px;letter-spacing:-0.02em;">Active Sensors</h3>
                <div id="preview-pills">
                    <span class="sensor-pill {{ $settings->temperature_enabled ? 'on' : 'off' }}" id="pill-temp">
                        <span class="pill-dot" aria-hidden="true"></span>Temperature
                    </span>
                    <span class="sensor-pill {{ $settings->humidity_enabled ? 'on' : 'off' }}" id="pill-hum">
                        <span class="pill-dot" aria-hidden="true"></span>Humidity
                    </span>
                    <span class="sensor-pill {{ $settings->air_quality_enabled ? 'on' : 'off' }}" id="pill-air">
                        <span class="pill-dot" aria-hidden="true"></span>Air Quality
                    </span>
                </div>
            </div>
            <div style="text-align:right;">
                <p style="font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:rgba(255,255,255,0.45);margin:0 0 6px;">Auto-Refresh Every</p>
                <div class="sensor-value" style="font-size:2.4rem;font-weight:900;color:#fff;line-height:1;" id="preview-delay" aria-live="polite">{{ max(15, $settings->refresh_delay) }}</div>
                <div style="font-size:0.82rem;color:rgba(255,255,255,0.5);margin-top:4px;">seconds</div>
            </div>
        </div>
    </div>

    {{-- ── Save Button ── --}}
    <div style="display:flex;justify-content:flex-end;">
        <button type="submit" class="btn-save" id="save-btn">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
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
    [15, 30, 60, 120, 300].forEach(p => {
        const el = document.getElementById('preset-' + p);
        if (el) el.classList.toggle('active', p === val);
    });

    // Update slider track fill
    const slider = document.getElementById('refresh_delay');
    const pct = ((val - 15) / (300 - 15)) * 100;
    slider.style.background = `linear-gradient(to right, #22c55e ${pct}%, #334155 ${pct}%)`;
}

function setDelay(val) {
    document.getElementById('refresh_delay').value = val;
    updateDelay(val);
}

function updatePreview() {
    const tempOn = document.getElementById('temperature_enabled').checked;
    const humOn  = document.getElementById('humidity_enabled').checked;
    const airOn  = document.getElementById('air_quality_enabled').checked;
    document.getElementById('pill-temp').className = 'sensor-pill ' + (tempOn ? 'on' : 'off');
    document.getElementById('pill-hum').className  = 'sensor-pill ' + (humOn  ? 'on' : 'off');
    document.getElementById('pill-air').className  = 'sensor-pill ' + (airOn  ? 'on' : 'off');
}

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
