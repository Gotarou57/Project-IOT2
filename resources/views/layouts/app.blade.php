<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'IoT Monitor') — EnvMonitor</title>
    <meta name="description" content="Real-time IoT sensor dashboard monitoring temperature, humidity, and air quality from ThingSpeak.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;600;700&family=Fira+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        /* ── CSS Custom Properties (maps to Tailwind @theme tokens) ── */
        :root {
            --bg:            #0f172a;
            --bg-surface:    #1e293b;
            --bg-surface2:   #273549;
            --bg-muted:      #334155;
            --text:          #f8fafc;
            --text-muted:    #94a3b8;
            --text-dim:      #7c8fa5;
            --border:        rgba(255,255,255,0.08);
            --border-solid:  #334155;
            --accent:        #22c55e;
            --accent-glow:   rgba(34,197,94,0.15);
            --shadow:        0 1px 3px rgba(0,0,0,0.35);
            --shadow-md:     0 8px 25px rgba(0,0,0,0.3);
            --radius:        16px;
        }

        *, *::before, *::after {
            font-family: 'Fira Sans', 'Inter', sans-serif;
            box-sizing: border-box;
        }

        .sensor-value { font-family: 'Fira Code', monospace !important; }

        /* ── Skip Navigation ── */
        .skip-link {
            position: absolute; top: -999px; left: 8px;
            background: var(--accent); color: #fff;
            padding: 8px 18px; z-index: 9999;
            font-size: 0.875rem; font-weight: 600;
            border-radius: 0 0 10px 10px;
            text-decoration: none;
            transition: top 0.1s;
        }
        .skip-link:focus { top: 0; }

        /* ── Refresh Toast ── */
        .page-refreshing {
            position: fixed; bottom: 28px; right: 28px;
            background: var(--bg-surface);
            border: 1px solid var(--border-solid);
            border-radius: 12px;
            padding: 10px 18px;
            display: flex; align-items: center; gap: 10px;
            font-size: 0.82rem; color: var(--text-muted);
            box-shadow: var(--shadow-md);
            z-index: 9999;
            opacity: 0; transform: translateY(10px);
            transition: opacity 0.3s ease, transform 0.3s ease;
            pointer-events: none;
        }
        .page-refreshing.show { opacity: 1; transform: translateY(0); }
        .refresh-spinner {
            width: 16px; height: 16px;
            border: 2px solid var(--border-solid);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
            flex-shrink: 0;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0a1020 0%, #0f172a 50%, #1a2540 100%);
            border-right: 1px solid rgba(255,255,255,0.06);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: width 0.3s ease, transform 0.3s ease;
            overflow: hidden;
            white-space: nowrap;
        }
        .sidebar-nav {
            flex: 1; padding: 24px 12px 20px;
            display: flex; flex-direction: column; gap: 4px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .nav-label {
            font-size: 0.65rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: #475569; padding: 8px 12px 4px; margin-top: 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 8px 14px;
            border-radius: 10px; color: var(--text-muted);
            text-decoration: none; font-size: 0.875rem; font-weight: 500;
            transition: all 0.18s ease; position: relative; cursor: pointer;
            min-height: 52px;
        }
        .nav-item:hover { background: rgba(255,255,255,0.07); color: #e2e8f0; }
        .nav-item:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        /* Unified green accent for active state */
        .nav-item.active {
            background: linear-gradient(135deg, rgba(34,197,94,0.15), rgba(34,197,94,0.08));
            color: #4ade80; font-weight: 600;
            border: 1px solid rgba(34,197,94,0.25);
        }
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 20%; bottom: 20%;
            width: 3px; background: var(--accent);
            border-radius: 0 4px 4px 0;
        }
        /* LIVE badge on active items */
        .nav-live-badge {
            margin-left: auto;
            font-size: 0.6rem; font-weight: 800; letter-spacing: 0.08em;
            text-transform: uppercase;
            background: rgba(34,197,94,0.18);
            color: #4ade80;
            padding: 2px 7px; border-radius: 99px;
            border: 1px solid rgba(34,197,94,0.3);
            display: flex; align-items: center; gap: 4px;
        }
        .nav-live-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--accent);
            animation: pulse-dot 1.5s infinite;
        }
        .nav-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: all 0.2s ease-in-out;
        }
        .nav-item.active .nav-icon  { background: rgba(34,197,94,0.18); }
        .nav-item:not(.active) .nav-icon { background: rgba(255,255,255,0.05); }
        
        /* Uiverse-style hover interaction */
        .nav-item:hover .nav-icon {
            transform: scale(1.1);
            background: var(--accent) !important;
            color: #ffffff;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
        }
        
        /* Sensor-specific hover identities */
        #nav-temperature:hover .nav-icon { background: #ef4444 !important; }
        #nav-humidity:hover .nav-icon { background: #3b82f6 !important; }
        #nav-air-quality:hover .nav-icon { background: #a855f7 !important; }

        /* Unit badges on sensor nav items */
        .nav-unit-badge {
            margin-left: auto;
            font-size: 0.7rem; padding: 2px 8px;
            border-radius: 20px; font-weight: 600;
        }

        .status-badge { display: flex; align-items: center; gap: 8px; font-size: 0.75rem; color: var(--text-dim); }
        .status-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--accent); box-shadow: 0 0 6px rgba(34,197,94,0.5);
            animation: pulse-dot 2s infinite; flex-shrink: 0;
        }
        .status-dot.error {
            background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.5);
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.85); }
        }

        /* ── Layout ── */
        body { margin: 0; overflow-x: hidden; }
        .layout-wrapper { display: flex; width: 100%; max-width: 100vw; overflow-x: hidden; }
        .main-content {
            margin-left: 260px; min-height: 100vh; flex: 1;
            background: var(--bg);
            transition: margin-left 0.3s ease;
            min-width: 0; /* Let flex item shrink instead of overflowing */
        }
        .page-header {
            background: rgba(15,23,42,0.75);
            border-bottom: 1px solid var(--border);
            padding: 16px 32px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 40;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
        }
        .page-header h1 {
            font-size: 1.25rem; font-weight: 700;
            color: var(--text); letter-spacing: -0.02em; margin: 0;
        }
        .page-body { padding: 32px; }

        .timestamp-badge {
            display: flex; align-items: center; gap: 8px;
            font-size: 0.8rem; color: var(--text-muted);
            background: var(--bg-muted); border-radius: 999px;
            padding: 6px 14px; border: 1px solid var(--border);
        }

        .sidebar-toggle {
            display: flex; background: none; border: none;
            cursor: pointer; padding: 8px; color: var(--text);
            border-radius: 8px; align-items: center;
            transition: background 0.18s;
        }
        .sidebar-toggle:hover { background: rgba(255,255,255,0.06); }
        .sidebar-toggle:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }

        /* Desktop minimized state */
        body.sidebar-mini .sidebar { width: 72px; }
        body.sidebar-mini .main-content { margin-left: 72px; }
        
        /* Hide all text/labels */
        body.sidebar-mini .nav-label,
        body.sidebar-mini .nav-item > span,
        body.sidebar-mini .nav-live-badge,
        body.sidebar-mini .nav-unit-badge {
            display: none !important;
        }

        /* Center nav icons and fix active highlight sizing */
        body.sidebar-mini .nav-item { padding: 0; justify-content: center; gap: 0; min-height: 48px; }
        body.sidebar-mini .nav-item.active .nav-icon { background: transparent; }

        /* Mobile state */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: 260px !important; }
            body.sidebar-open .sidebar { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
            .sidebar-overlay {
                display: none; position: fixed; inset: 0;
                background: rgba(0,0,0,0.65); z-index: 40;
                backdrop-filter: blur(2px);
            }
            body.sidebar-open .sidebar-overlay { display: block; }
            .page-body { padding: 20px 16px; }
        }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--bg-surface);
            border-radius: var(--radius); padding: 24px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }
        .chart-card {
            background: var(--bg-surface);
            border-radius: var(--radius); padding: 28px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
        }

        /* ── Typography helpers ── */
        .card-heading {
            font-size: 1rem; font-weight: 700;
            color: var(--text); margin: 0;
        }
        .card-subheading { font-size: 0.78rem; color: var(--text-muted); margin: 0; }
        .stat-label {
            font-size: 0.72rem; font-weight: 700;
            color: var(--text-muted); text-transform: uppercase;
            letter-spacing: 0.07em; margin: 0 0 8px;
        }

        /* ── Gauge bar ── */
        .gauge-track {
            height: 16px; background: var(--bg-muted);
            border-radius: 99px; overflow: hidden; margin-bottom: 8px;
        }
        .gauge-fill {
            height: 100%; border-radius: 99px;
            transition: width 0.8s ease;
        }
        .gauge-labels {
            display: flex; justify-content: space-between;
            font-size: 0.7rem; color: var(--text-dim); font-weight: 500;
        }

        /* ── Skeleton shimmer (polling loading state) ── */
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position:  200% center; }
        }
        .skeleton {
            background: linear-gradient(90deg, var(--bg-surface) 25%, var(--bg-surface2) 50%, var(--bg-surface) 75%);
            background-size: 200% auto;
            animation: shimmer 1.5s linear infinite;
            border-radius: 8px;
            color: transparent !important;
        }

        /* ── Glow on freshly updated sensor value ── */
        @keyframes value-glow {
            0%   { text-shadow: 0 0 0px transparent; }
            30%  { text-shadow: 0 0 14px rgba(34,197,94,0.7); }
            100% { text-shadow: 0 0 0px transparent; }
        }
        .just-updated { animation: value-glow 1.6s ease-out forwards; }

        /* ── prefers-reduced-motion ── */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            .status-dot      { animation: none; }
            .refresh-spinner { animation: none; }
            .page-refreshing { transition: none; }
            .stat-card       { transition: none; }
            .gauge-fill      { transition: none; }
            .skeleton        { animation: none; }
            .just-updated    { animation: none; }
        }
    </style>
</head>
<body style="background: var(--bg); margin: 0;">

<a href="#main-content" class="skip-link">Skip to main content</a>

<div class="page-refreshing" id="refreshToast" role="status" aria-live="polite" aria-label="Refreshing data">
    <div class="refresh-spinner" aria-hidden="true"></div>
    <span>Refreshing data…</span>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="layout-wrapper">
    {{-- ── Sidebar ── --}}
    <aside class="sidebar" id="sidebar" aria-label="Main navigation">
        <nav class="sidebar-nav" aria-label="Sensor pages">
            <span class="nav-label" aria-hidden="true">Menu</span>

            <a href="{{ route('home') }}" id="nav-home"
               class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">
                <div class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span>Home</span>
                @if(request()->routeIs('home'))
                <span class="nav-live-badge" aria-label="Live data">
                    <span class="nav-live-dot" aria-hidden="true"></span>LIVE
                </span>
                @endif
            </a>

            <span class="nav-label" aria-hidden="true">Sensor Data</span>

            <a href="{{ route('suhu') }}" id="nav-temperature"
               class="nav-item {{ request()->routeIs('suhu') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('suhu') ? 'page' : 'false' }}">
                <div class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/>
                    </svg>
                </div>
                <span>Temperature</span>
                @if(!request()->routeIs('suhu'))
                <span class="nav-unit-badge" style="background:rgba(239,68,68,0.15);color:#f87171;" aria-hidden="true">°C</span>
                @else
                <span class="nav-live-badge" aria-label="Live data">
                    <span class="nav-live-dot" aria-hidden="true"></span>LIVE
                </span>
                @endif
            </a>

            <a href="{{ route('humidity') }}" id="nav-humidity"
               class="nav-item {{ request()->routeIs('humidity') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('humidity') ? 'page' : 'false' }}">
                <div class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/>
                    </svg>
                </div>
                <span>Humidity</span>
                @if(!request()->routeIs('humidity'))
                <span class="nav-unit-badge" style="background:rgba(59,130,246,0.15);color:#60a5fa;" aria-hidden="true">%</span>
                @else
                <span class="nav-live-badge" aria-label="Live data">
                    <span class="nav-live-dot" aria-hidden="true"></span>LIVE
                </span>
                @endif
            </a>

            <a href="{{ route('air-quality') }}" id="nav-air-quality"
               class="nav-item {{ request()->routeIs('air-quality') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('air-quality') ? 'page' : 'false' }}">
                <div class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3H6a3 3 0 00-3 3v12a3 3 0 003 3h12a3 3 0 003-3V9.75M9.75 3v6.75H16.5M9.75 3L16.5 9.75"/>
                    </svg>
                </div>
                <span>Air Quality</span>
                @if(!request()->routeIs('air-quality'))
                <span class="nav-unit-badge" style="background:rgba(168,85,247,0.15);color:#a855f7;" aria-hidden="true">PPM</span>
                @else
                <span class="nav-live-badge" aria-label="Live data">
                    <span class="nav-live-dot" aria-hidden="true"></span>LIVE
                </span>
                @endif
            </a>

            <span class="nav-label" style="margin-top:16px;" aria-hidden="true">System</span>

            <a href="{{ route('settings') }}" id="nav-settings"
               class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('settings') ? 'page' : 'false' }}">
                <div class="nav-icon" aria-hidden="true">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    {{-- ── Main Content ── --}}
    <main class="main-content" id="main-content">
        <div class="page-header">
            <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                <button class="sidebar-toggle" onclick="toggleSidebar()"
                        aria-label="Toggle navigation sidebar"
                        aria-expanded="false"
                        aria-controls="sidebar"
                        id="sidebarToggleBtn"
                        style="flex-shrink:0;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1 style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="timestamp-badge" aria-live="polite" id="headerTimestamp" style="flex-shrink:0;margin-left:12px;">
                <span class="status-dot {{ ($apiError ?? false) ? 'error' : '' }}" style="width:7px;height:7px;" aria-hidden="true" id="headerStatusDot"></span>
                <span id="headerTimestampText">{{ ($apiError ?? false) ? 'Unreachable' : 'Connected' }} • {{ $timestamp ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="page-body">
            @yield('content')
        </div>
    </main>
</div>

<script>
    // On page load, restore sidebar state before rendering to prevent flash
    if (window.innerWidth > 768 && localStorage.getItem('sidebar-mini') === 'true') {
        document.body.classList.add('sidebar-mini');
    }

    function toggleSidebar() {
        if (window.innerWidth <= 768) {
            const isOpen = document.body.classList.toggle('sidebar-open');
            document.getElementById('sidebarToggleBtn').setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        } else {
            const isMini = document.body.classList.toggle('sidebar-mini');
            localStorage.setItem('sidebar-mini', isMini);
        }
    }

    // Global: update header timestamp from polling
    window.updateDashboardTimestamp = function(ts, hasError) {
        const headerText = document.getElementById('headerTimestampText');
        const headerStatusDot = document.getElementById('headerStatusDot');

        if (headerText) {
            headerText.textContent = (hasError ? 'Unreachable • ' : 'Connected • ') + ts;
        }

        if (hasError) {
            headerStatusDot?.classList.add('error');
        } else {
            headerStatusDot?.classList.remove('error');
        }
    };

    // Global: trigger glow effect on a value element
    window.flashGlow = function(el) {
        if (!el) return;
        el.classList.remove('just-updated');
        void el.offsetWidth; // reflow
        el.classList.add('just-updated');
    };
</script>

@stack('scripts')

</body>
</html>
