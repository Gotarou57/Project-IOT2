import { createHomeChart, createSensorChart, updateChartData } from '../charts/sensor-chart.js';

/**
 * Alpine.js component for the Home overview page.
 * Registered as Alpine.data('homePoller', homePoller)
 */
export function homePoller(initialData) {
    return {
        temp: initialData.temperature,
        hum:  initialData.humidity,
        aq:   initialData.airQuality,
        ts:   initialData.timestamp,
        loading: false,
        error: initialData.apiError,
        delay: initialData.delay,
        chartViewPref: null,
        _homeChart: null,
        _tempChart: null,
        _humChart:  null,
        _aqChart:   null,
        _timer: null,

        init() {
            // Restore chart view preference
            this.chartViewPref = localStorage.getItem('chartViewPref') || 'combined';

            this.$nextTick(() => {
                this._buildCharts(
                    initialData.labels,
                    initialData.temperatures,
                    initialData.humidities,
                    initialData.airQualities
                );
                this.applyChartView();
            });
            this._schedule();
        },

        async poll() {
            if (this.loading) return;
            this.loading = true;
            try {
                const range = new URLSearchParams(location.search).get('range') || '20';
                const res = await fetch('/api/sensors/overview?range=' + range, {
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok) throw new Error('HTTP ' + res.status);
                const json = await res.json();

                if (json.apiError) {
                    this.error = true;
                } else {
                    this.error = false;
                    this._updateValues(json);
                    this._updateCharts(json.series);
                    window.updateDashboardTimestamp?.(json.timestamp, false);
                }
            } catch {
                this.error = true;
                window.updateDashboardTimestamp?.(this.ts, true);
            } finally {
                this.loading = false;
                this._schedule();
            }
        },

        _schedule() {
            clearTimeout(this._timer);
            this._timer = setTimeout(() => this.poll(), this.delay * 1000);
        },

        _updateValues(json) {
            if (json.temperature !== this.temp) {
                this.temp = json.temperature;
                window.flashGlow(document.getElementById('val-temp'));
            }
            if (json.humidity !== this.hum) {
                this.hum = json.humidity;
                window.flashGlow(document.getElementById('val-hum'));
            }
            if (json.airQuality !== this.aq) {
                this.aq = json.airQuality;
                window.flashGlow(document.getElementById('val-aq'));
            }
            this.ts = json.timestamp;
        },

        _buildCharts(labels, temps, hums, aqs) {
            const hc  = document.getElementById('homeChart');
            const tc  = document.getElementById('tempChart');
            const hmc = document.getElementById('humChart');
            const ac  = document.getElementById('aqChart');
            if (hc)  this._homeChart = createHomeChart(hc, labels, temps, hums, aqs);
            if (tc)  this._tempChart = createSensorChart(tc, labels, temps, 'Temperature (°C)', '#ef4444', '°C');
            if (hmc) this._humChart  = createSensorChart(hmc, labels, hums,  'Humidity (%)',    '#3b82f6', '%');
            if (ac)  this._aqChart   = createSensorChart(ac,  labels, aqs,   'Air Quality (PPM)', '#a855f7', 'PPM');
        },

        _updateCharts(series) {
            if (!series) return;
            if (this._homeChart) {
                this._homeChart.data.labels = series.labels;
                this._homeChart.data.datasets[0].data = series.temperatures;
                this._homeChart.data.datasets[1].data = series.humidities;
                this._homeChart.data.datasets[2].data = series.airQualities;
                this._homeChart.update('active');
            }
            if (this._tempChart) updateChartData(this._tempChart, series.labels, series.temperatures);
            if (this._humChart)  updateChartData(this._humChart,  series.labels, series.humidities);
            if (this._aqChart)   updateChartData(this._aqChart,   series.labels, series.airQualities);
        },

        applyChartView() {
            const combined  = document.getElementById('combined-chart-container');
            const separated = document.getElementById('separated-charts-container');
            const legend    = document.getElementById('chart-legend');
            if (!combined || !separated) return;
            if (this.chartViewPref === 'separated') {
                combined.style.display  = 'none';
                separated.style.display = 'flex';
                if (legend) legend.style.display = 'none';
            } else {
                combined.style.display  = 'block';
                separated.style.display = 'none';
                if (legend) legend.style.display = 'flex';
            }
        },

        toggleChartView() {
            this.chartViewPref = this.chartViewPref === 'separated' ? 'combined' : 'separated';
            localStorage.setItem('chartViewPref', this.chartViewPref);
            this.applyChartView();
        },

        gaugePercent(value, max) {
            if (!value || value === 'N/A') return 0;
            return Math.min(100, Math.max(0, (parseFloat(value) / max) * 100));
        },

        gaugeColor(percent) {
            if (percent >= 80) return 'linear-gradient(90deg,#f97316,#ef4444)';
            if (percent >= 60) return 'linear-gradient(90deg,#f59e0b,#f97316)';
            return 'linear-gradient(90deg,#22c55e,#16a34a)';
        },
    };
}

/**
 * Alpine.js component for single-sensor detail pages.
 * Registered as Alpine.data('sensorPoller', sensorPoller)
 */
export function sensorDetailPoller(initialData) {
    return {
        current: initialData.current,
        ts:      initialData.timestamp,
        min:     initialData.min,
        max:     initialData.max,
        avg:     initialData.avg,
        loading: false,
        error:   initialData.apiError,
        delay:   initialData.delay,
        _chart: null,
        _timer: null,

        init() {
            this.$nextTick(() => {
                const canvas = document.getElementById('sensorChart');
                if (canvas) {
                    this._chart = createSensorChart(
                        canvas,
                        initialData.labels,
                        initialData.values,
                        initialData.label,
                        initialData.color,
                        initialData.unit
                    );
                }
            });
            this._schedule();
        },

        async poll() {
            if (this.loading) return;
            this.loading = true;
            try {
                const range = new URLSearchParams(location.search).get('range') || '20';
                const res = await fetch(initialData.apiRoute + '?range=' + range, {
                    headers: { Accept: 'application/json' },
                });
                if (!res.ok) throw new Error();
                const json = await res.json();

                if (json.apiError) {
                    this.error = true;
                } else {
                    this.error = false;
                    if (json.current !== this.current) {
                        this.current = json.current;
                        window.flashGlow(document.getElementById('val-current'));
                    }
                    this.ts  = json.timestamp;
                    this.min = json.min;
                    this.max = json.max;
                    this.avg = json.avg;
                    if (this._chart && json.series) {
                        updateChartData(this._chart, json.series.labels, json.series.values);
                    }
                    window.updateDashboardTimestamp?.(json.timestamp, false);
                }
            } catch {
                this.error = true;
                window.updateDashboardTimestamp?.(this.ts, true);
            } finally {
                this.loading = false;
                this._schedule();
            }
        },

        _schedule() {
            clearTimeout(this._timer);
            this._timer = setTimeout(() => this.poll(), this.delay * 1000);
        },

        gaugePct(val, max) {
            if (!val || val === 'N/A') return 0;
            return Math.min(100, Math.max(0, (parseFloat(val) / max) * 100));
        },

        gaugeColor(pct) {
            if (pct >= 80) return 'linear-gradient(90deg,#f97316,#ef4444)';
            if (pct >= 60) return 'linear-gradient(90deg,#f59e0b,#f97316)';
            return 'linear-gradient(90deg,#22c55e,#16a34a)';
        },
    };
}
