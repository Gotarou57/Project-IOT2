/**
 * Alpine.js polling utility for sensor API endpoints.
 *
 * Usage in Blade:
 *   import { sensorPoller } from './polling/sensor-poller.js';
 *   Alpine.data('sensorPoller', sensorPoller);
 */

export function sensorPoller(endpoint, initialData, refreshDelay, onUpdate) {
    return {
        data: initialData,
        loading: false,
        error: false,
        lastUpdated: null,
        _timer: null,

        init() {
            this.lastUpdated = new Date();
            this._schedule();
        },

        destroy() {
            clearTimeout(this._timer);
        },

        async poll() {
            if (this.loading) return;
            this.loading = true;

            try {
                const res = await fetch(endpoint, { headers: { Accept: 'application/json' } });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();

                if (json.apiError) {
                    this.error = true;
                } else {
                    this.error = false;
                    this.data = json;
                    this.lastUpdated = new Date();
                    if (typeof onUpdate === 'function') {
                        onUpdate(json);
                    }
                }
            } catch {
                this.error = true;
            } finally {
                this.loading = false;
                this._schedule();
            }
        },

        _schedule() {
            this._timer = setTimeout(() => this.poll(), refreshDelay * 1000);
        },

        /** Relative time string e.g. "just now", "2 min ago" */
        relativeTime() {
            if (!this.lastUpdated) return 'N/A';
            const diff = Math.floor((Date.now() - this.lastUpdated) / 1000);
            if (diff < 10)  return 'just now';
            if (diff < 60)  return `${diff}s ago`;
            if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
            return `${Math.floor(diff / 3600)}h ago`;
        },
    };
}
