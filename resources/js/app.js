import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import { applyDarkDefaults } from './charts/theme.js';
import { homePoller, sensorDetailPoller } from './alpine/dashboard-components.js';

// ── Alpine setup ────────────────────────────────────────────────
Alpine.plugin(persist);

// Register named components so Blade can use x-data="homePoller(...)"
Alpine.data('homePoller', homePoller);
Alpine.data('sensorDetailPoller', sensorDetailPoller);

window.Alpine = Alpine;
Alpine.start();

// ── Chart.js global dark defaults ──────────────────────────────
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
applyDarkDefaults(Chart);
window.Chart = Chart;
