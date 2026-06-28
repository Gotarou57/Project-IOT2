<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IoT Monitor') — Environment Monitor</title>
    <meta name="description" content="Real-time IoT sensor dashboard monitoring temperature and humidity from ThingSpeak.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }

        /* ── Sidebar ── */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            border-right: 1px solid rgba(255,255,255,0.06);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transition: transform 0.3s ease;
        }
        .sidebar-brand {
            padding: 28px 24px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-brand h2 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
        }
        .sidebar-brand p {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 2px;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .sidebar-nav {
            flex: 1;
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nav-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #475569;
            padding: 8px 12px 4px;
            margin-top: 8px;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.18s ease;
            position: relative;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.07);
            color: #e2e8f0;
        }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(99,102,241,0.25), rgba(139,92,246,0.15));
            color: #a5b4fc;
            font-weight: 600;
            border: 1px solid rgba(99,102,241,0.2);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: linear-gradient(180deg, #6366f1, #8b5cf6);
            border-radius: 0 4px 4px 0;
        }
        .nav-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: all 0.18s ease;
        }
        .nav-item.active .nav-icon {
            background: rgba(99,102,241,0.2);
        }
        .nav-item:not(.active) .nav-icon {
            background: rgba(255,255,255,0.05);
        }
        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .status-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            color: #64748b;
        }
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 6px rgba(34,197,94,0.5);
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* ── Main Layout ── */
        .layout-wrapper {
            display: flex;
        }
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            flex: 1;
            background: #f8fafc;
        }
        .page-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .page-header h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .page-body {
            padding: 32px;
        }

        /* ── Mobile Toggle ── */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: #0f172a;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-toggle { display: flex; }
            .sidebar-overlay {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 40;
            }
            .sidebar-overlay.open { display: block; }
        }

        /* ── Stat Cards ── */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .chart-card {
            background: #fff;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
    </style>
</head>
<body style="background:#f8fafc; margin:0;">

<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<div class="layout-wrapper">
    <!-- ── Left Sidebar ── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                <div style="width:36px;height:36px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" fill="none" stroke="white" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h2>EnvMonitor</h2>
                    <p>IoT Dashboard</p>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-label">Menu</span>

            <a href="{{ route('home') }}" id="nav-home" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <div class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <span>Home</span>
            </a>

            <span class="nav-label">Sensor Data</span>

            <a href="{{ route('suhu') }}" id="nav-temperature" class="nav-item {{ request()->routeIs('suhu') ? 'active' : '' }}">
                <div class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 14.76V3.5a2.5 2.5 0 00-5 0v11.26a4.5 4.5 0 105 0z"/>
                    </svg>
                </div>
                <span>Temperature</span>
                <span style="margin-left:auto;font-size:0.7rem;background:rgba(239,68,68,0.15);color:#f87171;padding:2px 8px;border-radius:20px;font-weight:600;">°C</span>
            </a>

            <a href="{{ route('humidity') }}" id="nav-humidity" class="nav-item {{ request()->routeIs('humidity') ? 'active' : '' }}">
                <div class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C7 7 4 11 4 14a8 8 0 0016 0c0-3-3-7-8-12z"/>
                    </svg>
                </div>
                <span>Humidity</span>
                <span style="margin-left:auto;font-size:0.7rem;background:rgba(59,130,246,0.15);color:#60a5fa;padding:2px 8px;border-radius:20px;font-weight:600;">%</span>
            </a>

            <span class="nav-label" style="margin-top:16px;">System</span>

            <a href="{{ route('settings') }}" id="nav-settings" class="nav-item {{ request()->routeIs('settings') ? 'active' : '' }}">
                <div class="nav-icon">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span>Settings</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="status-badge">
                <div class="status-dot"></div>
                <span>ThingSpeak Connected</span>
            </div>
        </div>
    </aside>

    <!-- ── Main Content ── -->
    <main class="main-content">
        <div class="page-header">
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:0.8rem;color:#64748b;background:#f1f5f9;border-radius:999px;padding:6px 14px;border:1px solid #e2e8f0;">
                <span class="status-dot" style="width:7px;height:7px;"></span>
                <span>Last update: {{ $timestamp ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="page-body">
            @yield('content')
        </div>
    </main>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }
</script>

@stack('scripts')

</body>
</html>
