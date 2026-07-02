/**
 * Shared Chart.js dark-mode defaults for the IoT dashboard.
 */

export const darkTooltip = {
    backgroundColor: 'rgba(15,23,42,0.96)',
    titleColor: '#f8fafc',
    bodyColor: '#cbd5e1',
    padding: 14,
    cornerRadius: 10,
    displayColors: true,
    borderColor: 'rgba(255,255,255,0.08)',
    borderWidth: 1,
};

export const darkGrid = {
    color: 'rgba(255,255,255,0.06)',
};

export const darkTicks = {
    color: '#94a3b8',
    font: { size: 11, family: "'Fira Code', monospace" },
};

export const darkXTicks = {
    color: '#64748b',
    font: { size: 11 },
};

/**
 * Apply shared defaults to Chart.js globally.
 * Call once after Chart is imported.
 */
export function applyDarkDefaults(Chart) {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Fira Sans', 'Inter', sans-serif";
    Chart.defaults.plugins.legend.display = false;
    Chart.defaults.plugins.tooltip = {
        ...Chart.defaults.plugins.tooltip,
        ...darkTooltip,
    };
}
