import { Chart, registerables } from 'chart.js';
import { darkTooltip, darkGrid, darkTicks, darkXTicks } from './theme.js';

Chart.register(...registerables);

/**
 * Get padded min/max bounds for a numeric dataset.
 */
export function getScaleBounds(values) {
    const valid = values.filter(v => v !== null && v !== undefined);
    if (!valid.length) return { min: 0, max: 100 };
    const max = Math.max(...valid);
    const padding = max === 0 ? 1 : Math.abs(max * 0.1);
    return { min: 0, max: Math.ceil(max + padding) };
}

/**
 * Build a canvas gradient fill for a line chart.
 */
export function buildGradient(ctx, hexColor, alphaTop = 0.25, alphaBottom = 0.01) {
    const gradient = ctx.createLinearGradient(0, 0, 0, ctx.canvas.height || 320);
    gradient.addColorStop(0, hexColor.replace(')', `,${alphaTop})`).replace('rgb', 'rgba'));
    gradient.addColorStop(1, hexColor.replace(')', `,${alphaBottom})`).replace('rgb', 'rgba'));
    return gradient;
}

/**
 * Helper to convert a hex color to rgba fill string.
 */
function hexToRgba(hex, alpha) {
    const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    if (!result) return `rgba(128,128,128,${alpha})`;
    return `rgba(${parseInt(result[1],16)},${parseInt(result[2],16)},${parseInt(result[3],16)},${alpha})`;
}

/**
 * Create a single-sensor line chart.
 *
 * @param {HTMLCanvasElement} canvas
 * @param {string[]} labels
 * @param {number[]} values
 * @param {string} label
 * @param {string} color  hex color e.g. '#ef4444'
 * @param {string} unit   e.g. '°C'
 * @returns {Chart}
 */
export function createSensorChart(canvas, labels, values, label, color, unit) {
    const ctx = canvas.getContext('2d');
    const bounds = getScaleBounds(values);

    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, hexToRgba(color, 0.22));
    gradient.addColorStop(1, hexToRgba(color, 0.01));

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label,
                data: values,
                borderColor: color,
                backgroundColor: gradient,
                borderWidth: 2.5,
                pointRadius: 0,
                tension: 0.4,
                fill: true,
                spanGaps: true,
            }],
        },
        options: {
            animation: { duration: 400 },
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...darkTooltip,
                    callbacks: {
                        label: (c) => ` ${c.raw} ${unit}`,
                    },
                },
            },
            scales: {
                x: { grid: { display: false }, ticks: darkXTicks },
                y: {
                    min: bounds.min,
                    max: bounds.max,
                    grid: darkGrid,
                    ticks: { ...darkTicks, color, font: { ...darkTicks.font, weight: '600' }, callback: v => `${v}${unit}` },
                    title: { display: true, text: label, color, font: { weight: '600' } },
                },
            },
        },
    });
}

/**
 * Update an existing chart's data and re-render.
 */
export function updateChartData(chart, labels, values) {
    chart.data.labels = labels;
    chart.data.datasets[0].data = values;
    const bounds = getScaleBounds(values.filter(v => v !== null));
    if (chart.options.scales?.y) {
        chart.options.scales.y.min = bounds.min;
        chart.options.scales.y.max = bounds.max;
    }
    chart.update('active');
}

/**
 * Create the combined multi-axis home chart.
 */
export function createHomeChart(canvas, labels, tempData, humData, aqData) {
    const ctx = canvas.getContext('2d');

    const tempBounds = getScaleBounds(tempData.filter(v => v !== null));
    const humBounds  = getScaleBounds(humData.filter(v => v !== null));
    const aqBounds   = getScaleBounds(aqData.filter(v => v !== null));

    const tempGrad = ctx.createLinearGradient(0, 0, 0, 340);
    tempGrad.addColorStop(0, 'rgba(239,68,68,0.18)');
    tempGrad.addColorStop(1, 'rgba(239,68,68,0.01)');

    const humGrad = ctx.createLinearGradient(0, 0, 0, 340);
    humGrad.addColorStop(0, 'rgba(59,130,246,0.12)');
    humGrad.addColorStop(1, 'rgba(59,130,246,0.01)');

    const aqGrad = ctx.createLinearGradient(0, 0, 0, 340);
    aqGrad.addColorStop(0, 'rgba(168,85,247,0.12)');
    aqGrad.addColorStop(1, 'rgba(168,85,247,0.01)');

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Temperature (°C)',
                    data: tempData,
                    borderColor: '#ef4444',
                    backgroundColor: tempGrad,
                    borderWidth: 2.5,
                    pointRadius: 0,
                    tension: 0.4,
                    yAxisID: 'y-temp',
                    fill: true,
                    spanGaps: true,
                },
                {
                    label: 'Humidity (%)',
                    data: humData,
                    borderColor: '#3b82f6',
                    backgroundColor: humGrad,
                    borderWidth: 2.5,
                    borderDash: [6, 4],
                    pointRadius: 0,
                    tension: 0.4,
                    yAxisID: 'y-hum',
                    fill: true,
                    spanGaps: true,
                },
                {
                    label: 'Air Quality (PPM)',
                    data: aqData,
                    borderColor: '#a855f7',
                    backgroundColor: aqGrad,
                    borderWidth: 2.5,
                    borderDash: [2, 5],
                    pointRadius: 0,
                    tension: 0.4,
                    yAxisID: 'y-aq',
                    fill: true,
                    spanGaps: true,
                },
            ],
        },
        options: {
            animation: { duration: 400 },
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: { legend: { display: false }, tooltip: darkTooltip },
            scales: {
                x: { grid: { display: false }, ticks: darkXTicks },
                'y-temp': {
                    type: 'linear', position: 'left',
                    min: tempBounds.min, max: tempBounds.max,
                    grid: darkGrid,
                    ticks: { color: '#f87171', font: { weight: '600' } },
                    title: { display: true, text: 'Temp (°C)', color: '#f87171', font: { weight: '600' } },
                },
                'y-hum': {
                    type: 'linear', position: 'right',
                    min: humBounds.min, max: humBounds.max,
                    grid: { display: false },
                    ticks: { color: '#60a5fa', font: { weight: '600' } },
                    title: { display: true, text: 'Humidity (%)', color: '#60a5fa', font: { weight: '600' } },
                },
                'y-aq': {
                    type: 'linear', position: 'right',
                    min: aqBounds.min, max: aqBounds.max,
                    grid: { display: false },
                    ticks: { color: '#c084fc', font: { weight: '600' } },
                    title: { display: true, text: 'Air Quality (PPM)', color: '#c084fc', font: { weight: '600' } },
                },
            },
        },
    });
}
