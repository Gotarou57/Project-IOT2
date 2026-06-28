<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Sensor Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-300">

    <div class="max-w-6xl mx-auto px-4 py-12">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Environment <span class="text-blue-600">Monitor</span>
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Real-time data from ThingSpeak IoT Channel</p>
            </div>
            <div class="flex items-center gap-3 px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-sm">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                    Last Update: {{ $timestamp }}
                </span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Temperature Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-transform hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Temperature</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black text-gray-900 dark:text-white">{{ $temperature }}</span>
                    <span class="text-xl font-medium text-gray-500">&deg;C</span>
                </div>
            </div>

            <!-- Humidity Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-transform hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13a1 1 0 00-1-1 7 7 0 00-7-7 7 7 0 00-7 7 1 1 0 00-1 1v1a1 1 0 001 1h14a1 1 0 001-1v-1z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Humidity</span>
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-4xl font-black text-gray-900 dark:text-white">{{ $humidity }}</span>
                    <span class="text-xl font-medium text-gray-500">%</span>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 transition-transform hover:scale-[1.02]">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">System Status</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">Connected</span>
                    <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-400 rounded-full">Online</span>
                </div>
            </div>
        </div>

        <!-- Graph Section -->
        <div class="bg-white dark:bg-gray-800 p-8 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Environmental Trends</h3>
                <div class="flex gap-4 text-xs">
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-gray-500">Temperature</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-gray-500">Humidity</span>
                    </div>
                </div>
            </div>
            <div class="h-[400px] w-full">
                <canvas id="sensorChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('sensorChart').getContext('2d');

            const labels = @json($timestamps);
            const tempData = @json($temperatures);
            const humData = @json($humidities);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Temperature (°C)',
                            data: tempData,
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            pointRadius: 3,
                            pointBackgroundColor: '#ef4444',
                            tension: 0.4,
                            yAxisID: 'y-temp',
                            fill: true
                        },
                        {
                            label: 'Humidity (%)',
                            data: humData,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            pointRadius: 3,
                            pointBackgroundColor: '#3b82f6',
                            tension: 0.4,
                            yAxisID: 'y-hum',
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#ccc',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: true
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#9ca3af', font: { size: 11 } }
                        },
                        'y-temp': {
                            type: 'linear',
                            position: 'left',
                            grid: { color: 'rgba(156, 163, 175, 0.1)' },
                            ticks: { color: '#ef4444', font: { weight: 'bold' } },
                            title: { display: true, text: 'Temp (°C)', color: '#ef4444' }
                        },
                        'y-hum': {
                            type: 'linear',
                            position: 'right',
                            grid: { display: false },
                            ticks: { color: '#3b82f6', font: { weight: 'bold' } },
                            title: { display: true, text: 'Humidity (%)', color: '#3b82f6' }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
