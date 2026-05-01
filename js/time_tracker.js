/**
 * TimeForge Advanced Time Tracker — Phase 6 + Phase 9 Upgrade
 *
 * Phase 6:
 *  - Task 6.1: Idle Popup Modal — pauses timer, asks user what to do with idle time
 *  - Task 6.2: Stale Session Resume Guard — blocks silent 21-hour ghost resumes
 *  - Task 6.3: Activity Score Heartbeat — mouse + keyboard events sent every pulse
 *
 * Phase 9:
 *  - Task 9.1: DOM Screenshot Capture — silently captures page via html2canvas
 *              at a random 5–15 min interval and uploads to api/upload_screenshot.php
 */

class TimeTracker {
    constructor() {
        this.timerInterval     = null;
        this.heartbeatInterval = null;
        this.idleCheckInterval = null;

        this.startTime   = null;
        this.projectId   = null;
        this.taskId      = null;   // Phase 11: optional task association
        this.entryId     = null;   // Phase 6: server-assigned DB row id
        this.description = null;

        this.lastActivity    = Date.now();
        this.lastHeartbeat   = Date.now();
        this.isIdle          = false;
        this.idleSince       = null;  // timestamp when idle started

        // Accumulated idle data for current session
        this.totalIdleSeconds     = 0;
        this.discardedIdleSeconds = 0;

        // Phase 6.3: Activity counters, reset each heartbeat cycle
        this.mouseEvents = 0;
        this.keyEvents   = 0;

        // Configuration
        this.HEARTBEAT_MS      = 60000;   // 1 minute
        this.IDLE_THRESHOLD_MS = 600000;  // 10 minutes — show idle modal
        this.STALE_GAP_MS      = 1800000; // 30 minutes — stale session threshold

        // Phase 9: Screenshot
        this.screenshotTimeout    = null;   // single random-delay timeout
        this.screenshotCount      = 0;      // shown in widget badge
        this.screenshotsEnabled   = false;  // set from server on timer start
        this.screenshotMinMs      = 5  * 60000; // default 5 min (overridden by server)
        this.screenshotMaxMs      = 15 * 60000; // default 15 min (overridden by server)

        this.elements = {
            widget:          null,
            timeDisplay:     null,
            projectLabel:    null,
            toggleBtn:       null,
            stopBtn:         null,
            screenshotBadge: null,
        };

        this.pendingStaleState = null;

        this.init();
    }

    // ── Init ──────────────────────────────────────────────────────────────────

    init() {
        this.createWidget();
        this.createIdleModal();
        this.createStaleModal();
        this.setupEventListeners();
        this.restoreState();       // Task 6.2 guard is inside here
        this.startIdleDetection();
    }

    // ── Widget HTML ───────────────────────────────────────────────────────────

    createWidget() {
        const html = `
        <div id="tf-timer-widget" class="tf-timer-hidden">
            <div class="tf-timer-header">
                <span class="tf-timer-status-dot"></span>
                <span id="tf-timer-project">No Project</span>
                <button id="tf-timer-toggle" class="tf-btn-icon" title="Minimise">_</button>
            </div>
            <div class="tf-timer-body">
                <div id="tf-timer-display">00:00:00</div>
                <div id="tf-timer-idle-badge" class="tf-idle-badge tf-hidden">&#9888; Idle time excluded</div>
                <div id="tf-screenshot-badge" class="tf-screenshot-badge tf-hidden" title="Screenshots active">&#128247; <span id="tf-screenshot-count">0</span> <span id="tf-screenshot-interval" style="font-size:.7em; opacity:.75;"></span></div>
                <div class="tf-timer-controls">
                    <button id="tf-timer-stop" class="tf-btn-stop">Stop Timer</button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);

        this.elements.widget          = document.getElementById('tf-timer-widget');
        this.elements.timeDisplay     = document.getElementById('tf-timer-display');
        this.elements.projectLabel    = document.getElementById('tf-timer-project');
        this.elements.toggleBtn       = document.getElementById('tf-timer-toggle');
        this.elements.stopBtn         = document.getElementById('tf-timer-stop');
        this.elements.screenshotBadge = document.getElementById('tf-screenshot-badge');
    }

    // ── Task 6.1: Idle Modal ───────────────────────────────────────────────────

    createIdleModal() {
        const html = `
        <div id="tf-idle-modal" class="tf-modal-overlay tf-hidden" role="dialog" aria-modal="true">
            <div class="tf-modal-box">
                <div class="tf-modal-icon">&#9646;&#9646;</div>
                <h2 class="tf-modal-title">Are you still working?</h2>
                <p class="tf-modal-body">
                    You have been inactive for <strong id="tf-idle-minutes">10</strong> minutes.<br>
                    The timer has been paused. What do you want to do with this time?
                </p>
                <div class="tf-modal-actions">
                    <button id="tf-idle-keep"    class="tf-btn-modal tf-btn-success">&#10003; I was working &mdash; keep all time</button>
                    <button id="tf-idle-discard" class="tf-btn-modal tf-btn-warning">&#9202; Discard idle time</button>
                    <button id="tf-idle-stop"    class="tf-btn-modal tf-btn-danger">&#9632; Stop the timer</button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
    }

    // ── Task 6.2: Stale Session Modal ─────────────────────────────────────────

    createStaleModal() {
        const html = `
        <div id="tf-stale-modal" class="tf-modal-overlay tf-hidden" role="dialog" aria-modal="true">
            <div class="tf-modal-box">
                <div class="tf-modal-icon">&#128336;</div>
                <h2 class="tf-modal-title">Timer found from a previous session</h2>
                <p class="tf-modal-body">
                    You have a timer for <strong id="tf-stale-project">a project</strong> that started
                    <strong id="tf-stale-hours">?</strong> ago.<br>
                    The computer may have been closed or left unattended. What do you want to do?
                </p>
                <div class="tf-modal-actions">
                    <button id="tf-stale-resume"  class="tf-btn-modal tf-btn-success">&#9654; Resume from now (discard gap)</button>
                    <button id="tf-stale-discard" class="tf-btn-modal tf-btn-warning">&#128465; Discard those hours entirely</button>
                    <button id="tf-stale-keep"    class="tf-btn-modal tf-btn-neutral">&#128203; Keep all hours (manual override)</button>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', html);
    }

    // ── Event Listeners ───────────────────────────────────────────────────────

    setupEventListeners() {
        // Widget controls
        this.elements.toggleBtn.addEventListener('click', () => {
            this.elements.widget.classList.toggle('tf-timer-collapsed');
            this.elements.toggleBtn.textContent =
                this.elements.widget.classList.contains('tf-timer-collapsed') ? '\u25A1' : '_';
        });
        this.elements.stopBtn.addEventListener('click', () => this.stopTimer());

        // Activity detection — Task 6.3 counters + idle reset
        document.addEventListener('mousemove', () => { this.mouseEvents++; this.resetIdleTimer(); });
        document.addEventListener('click',     () => { this.mouseEvents++; this.resetIdleTimer(); });
        document.addEventListener('keydown',   () => { this.keyEvents++;   this.resetIdleTimer(); });
        document.addEventListener('scroll',    () => { this.resetIdleTimer(); });

        // Phase 6.8: Tab focus / visibility guard
        document.addEventListener('visibilitychange', () => this.handleVisibilityChange());

        // Idle modal buttons — Task 6.1
        document.getElementById('tf-idle-keep').addEventListener('click',    () => this.resolveIdle('keep'));
        document.getElementById('tf-idle-discard').addEventListener('click', () => this.resolveIdle('discard'));
        document.getElementById('tf-idle-stop').addEventListener('click',    () => this.resolveIdle('stop'));

        // Stale modal buttons — Task 6.2
        document.getElementById('tf-stale-resume').addEventListener('click',  () => this.resolveStale('resume'));
        document.getElementById('tf-stale-discard').addEventListener('click', () => this.resolveStale('discard'));
        document.getElementById('tf-stale-keep').addEventListener('click',    () => this.resolveStale('keep'));
    }

    // ── Task 6.3: Activity counter reset ──────────────────────────────────────

    resetIdleTimer() {
        this.lastActivity = Date.now();
        if (this.isIdle) {
            // User came back while modal was not yet triggered (edge case)
            this.isIdle    = false;
            this.idleSince = null;
            this.elements.widget.classList.remove('tf-timer-idle');
        }
    }

    // ── Phase 6.8: Tab Visibility Guard ───────────────────────────────────────
    handleVisibilityChange() {
        if (!this.startTime) return; // only when timer is running

        if (document.hidden) {
            this._tabHiddenAt = Date.now();
            this.elements.widget.classList.add('tf-timer-unfocused');
        } else {
            this.elements.widget.classList.remove('tf-timer-unfocused');
            if (this._tabHiddenAt) {
                const awayMs = Date.now() - this._tabHiddenAt;
                this._tabHiddenAt = null;
                // If away long enough, trigger the existing idle modal
                if (awayMs >= this.IDLE_THRESHOLD_MS) {
                    this.triggerIdleModal(awayMs);
                }
            }
        }
    }

    // ── Task 6.1: Idle Detection Loop ─────────────────────────────────────────

    startIdleDetection() {
        if (this.idleCheckInterval) clearInterval(this.idleCheckInterval);
        this.idleCheckInterval = setInterval(() => {
            if (!this.startTime || this.isIdle) return;
            const gap = Date.now() - this.lastActivity;
            if (gap >= this.IDLE_THRESHOLD_MS) {
                this.triggerIdleModal(gap);
            }
        }, 10000); // check every 10 seconds
    }

    triggerIdleModal(gapMs) {
        if (this.isIdle) return;
        this.isIdle    = true;
        this.idleSince = this.lastActivity; // mark when idle started

        // Pause the visual counter (startTime is preserved)
        clearInterval(this.timerInterval);
        this.timerInterval = null;
        this.elements.widget.classList.add('tf-timer-idle');

        const idleMinutes = Math.floor(gapMs / 60000);
        document.getElementById('tf-idle-minutes').textContent = idleMinutes;
        document.getElementById('tf-idle-modal').classList.remove('tf-hidden');
    }

    resolveIdle(choice) {
        document.getElementById('tf-idle-modal').classList.add('tf-hidden');

        const idleMs      = Date.now() - (this.idleSince || Date.now());
        const idleSeconds = Math.floor(idleMs / 1000);

        if (choice === 'keep') {
            // Keep all time — resume counting from original startTime
            this.totalIdleSeconds += idleSeconds;
            this._notifyServer('idle_resolved', idleSeconds, 0);

        } else if (choice === 'discard') {
            // Advance startTime by idle gap so the display skips it
            this.startTime            += idleMs;
            this.totalIdleSeconds     += idleSeconds;
            this.discardedIdleSeconds += idleSeconds;
            this._notifyServer('idle_resolved', idleSeconds, idleSeconds);
            document.getElementById('tf-timer-idle-badge').classList.remove('tf-hidden');

        } else if (choice === 'stop') {
            this._notifyServer('idle_resolved', idleSeconds, idleSeconds);
            this.stopTimer();
            return;
        }

        // Resume ticking
        this.isIdle    = false;
        this.idleSince = null;
        this.elements.widget.classList.remove('tf-timer-idle');
        this.lastActivity  = Date.now();
        this.timerInterval = setInterval(() => this.updateDisplay(), 1000);
        this.saveState();
    }

    // ── Task 6.2: State Restore with Stale Guard ──────────────────────────────

    restoreState() {
        const raw = localStorage.getItem('tf_timer_state');
        if (!raw) return;

        let state;
        try { state = JSON.parse(raw); } catch (e) { localStorage.removeItem('tf_timer_state'); return; }
        if (!state || !state.running) return;

        const now           = Date.now();
        const lastHeartbeat = state.lastHeartbeat || state.startTime;
        const gapMs         = now - lastHeartbeat;

        if (gapMs > this.STALE_GAP_MS) {
            // Gap too large — show stale modal instead of silently resuming
            this._showStaleModal(state, gapMs);
        } else {
            // Gap acceptable — restore normally
            this._doRestore(state);
        }
    }

    _showStaleModal(state, gapMs) {
        this.pendingStaleState = state;
        const hours   = Math.floor(gapMs / 3600000);
        const minutes = Math.floor((gapMs % 3600000) / 60000);
        const label   = hours > 0 ? `${hours}h ${minutes}m` : `${minutes} minutes`;
        document.getElementById('tf-stale-project').textContent = `Project #${state.projectId}`;
        document.getElementById('tf-stale-hours').textContent   = label;
        document.getElementById('tf-stale-modal').classList.remove('tf-hidden');
    }

    resolveStale(choice) {
        document.getElementById('tf-stale-modal').classList.add('tf-hidden');
        const state = this.pendingStaleState;
        this.pendingStaleState = null;

        if (choice === 'resume') {
            // Fresh start from now, discard gap
            this._doRestore({ ...state, startTime: Date.now(), lastHeartbeat: Date.now() });

        } else if (choice === 'discard') {
            // Tell server to abandon old entry then clear storage
            if (state.entryId) {
                fetch('/TimeForge_Capstone/api/time_tracking.php', {
                    method: 'POST',
                    body: new URLSearchParams({
                        action: 'stop', project_id: state.projectId,
                        entry_id: state.entryId, idle_seconds: 0, discarded_idle_seconds: 0
                    })
                });
            }
            localStorage.removeItem('tf_timer_state');

        } else if (choice === 'keep') {
            // Trust user — restore with original startTime
            this._doRestore(state);
        }
    }

    _doRestore(state) {
        this.projectId   = state.projectId;
        this.taskId      = state.taskId || null;
        this.description = state.description;
        this.entryId     = state.entryId || null;
        this.startTime   = state.startTime;

        this.elements.projectLabel.textContent = `Project #${this.projectId}`;
        this.elements.widget.classList.remove('tf-timer-hidden');

        if (this.timerInterval) clearInterval(this.timerInterval);
        this.timerInterval = setInterval(() => this.updateDisplay(), 1000);
        this.updateDisplay();

        if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);
        this.heartbeatInterval = setInterval(() => this.sendHeartbeat('pulse'), this.HEARTBEAT_MS);
    }

    // ── Start / Stop ──────────────────────────────────────────────────────────

    async startTimer(projectId, description, explicitStartTime = null, taskId = null) {
        if (!projectId) return;

        this.projectId   = projectId;
        this.taskId      = taskId || null;
        this.description = description;
        this.startTime   = explicitStartTime || Date.now();
        this.mouseEvents = 0;
        this.keyEvents   = 0;

        if (!explicitStartTime) {
            try {
                const response = await this.sendHeartbeat('start');
                if (!response.success) throw new Error(response.message);
                this.entryId             = response.entry_id || null;
                this.screenshotsEnabled  = response.screenshots_enabled === true || response.screenshots_enabled === 1;
                // Phase 9b: use server-configured interval (in minutes → ms)
                if (response.screenshot_min_interval) {
                    this.screenshotMinMs = Math.max(1, parseInt(response.screenshot_min_interval)) * 60000;
                }
                if (response.screenshot_max_interval) {
                    this.screenshotMaxMs = Math.max(this.screenshotMinMs, parseInt(response.screenshot_max_interval) * 60000);
                }
                const intervalDesc = this.screenshotMinMs === this.screenshotMaxMs
                    ? `fixed ${this.screenshotMinMs / 60000}min`
                    : `random ${this.screenshotMinMs / 60000}–${this.screenshotMaxMs / 60000}min`;
                console.log('Timer started, entry_id:', this.entryId, '| screenshots:', this.screenshotsEnabled, '| interval:', intervalDesc);
            } catch (err) {
                console.error('Failed to start timer:', err);
                alert('Could not start timer. Please check your connection.');
                this.stopLocalTimer();
                return;
            }
        }

        this.saveState();
        this.elements.projectLabel.textContent = `Project #${projectId}`;
        this.elements.widget.classList.remove('tf-timer-hidden');

        if (this.timerInterval) clearInterval(this.timerInterval);
        this.timerInterval = setInterval(() => this.updateDisplay(), 1000);
        this.updateDisplay();

        if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);
        this.heartbeatInterval = setInterval(() => this.sendHeartbeat('pulse'), this.HEARTBEAT_MS);

        // Phase 9 — start screenshot capture if enabled for this project
        if (this.screenshotsEnabled) {
            this.screenshotCount = 0;
            this.scheduleNextScreenshot();
            this.elements.screenshotBadge.classList.remove('tf-hidden');
            // Show interval label in widget badge
            const mn = this.screenshotMinMs / 60000;
            const mx = this.screenshotMaxMs / 60000;
            const lbl = (mn === mx) ? `every ${mn}min` : `every ${mn}–${mx}min`;
            const intervalEl = document.getElementById('tf-screenshot-interval');
            if (intervalEl) intervalEl.textContent = lbl;
        }
    }

    async stopTimer() {
        if (!confirm('Are you sure you want to stop the timer?')) return;
        try {
            await this.sendHeartbeat('stop');
        } catch (err) {
            console.error('Network error stopping timer:', err);
        }
        this.stopLocalTimer();
    }

    stopLocalTimer() {
        clearInterval(this.timerInterval);
        clearInterval(this.heartbeatInterval);
        this.timerInterval     = null;
        this.heartbeatInterval = null;
        this.startTime         = null;
        this.projectId         = null;
        this.entryId           = null;
        this.totalIdleSeconds     = 0;
        this.discardedIdleSeconds = 0;
        this.mouseEvents = 0;
        this.keyEvents   = 0;

        // Phase 9 — stop screenshot interval
        if (this.screenshotTimeout) {
            clearTimeout(this.screenshotTimeout);
            this.screenshotTimeout = null;
        }
        this.screenshotCount    = 0;
        this.screenshotsEnabled = false;
        this.elements.screenshotBadge.classList.add('tf-hidden');
        document.getElementById('tf-screenshot-count').textContent = '0';

        localStorage.removeItem('tf_timer_state');
        this.elements.widget.classList.add('tf-timer-hidden');
        document.getElementById('tf-timer-idle-badge').classList.add('tf-hidden');
        document.title = 'TimeForge';
    }

    // ── Heartbeat / API ───────────────────────────────────────────────────────

    async sendHeartbeat(action) {
        this.lastHeartbeat = Date.now();
        this.saveState();

        const body = new URLSearchParams({
            action,
            project_id:             this.projectId   || '',
            task_id:                this.taskId       || '',
            description:            this.description || '',
            entry_id:               this.entryId     || '',
            mouse_events:           this.mouseEvents,
            key_events:             this.keyEvents,
            activity_score:         this.mouseEvents + this.keyEvents,
            idle_seconds:           this.totalIdleSeconds,
            discarded_idle_seconds: this.discardedIdleSeconds
        });

        // Reset per-minute activity counters after each pulse
        this.mouseEvents = 0;
        this.keyEvents   = 0;

        const resp = await fetch('/TimeForge_Capstone/api/time_tracking.php', { method: 'POST', body });
        const json = await resp.json();

        if (action === 'start' && json.entry_id) {
            this.entryId = json.entry_id;
            this.saveState();
        }
        return json;
    }

    // Lightweight fire-and-forget for idle notifications
    _notifyServer(action, idleSecs, discardedSecs) {
        if (!this.entryId) return;
        navigator.sendBeacon(
            '/TimeForge_Capstone/api/time_tracking.php',
            new URLSearchParams({
                action,
                project_id:             this.projectId,
                entry_id:               this.entryId,
                idle_seconds:           idleSecs,
                discarded_idle_seconds: discardedSecs
            })
        );
    }

    // ── State Persistence ─────────────────────────────────────────────────────

    saveState() {
        localStorage.setItem('tf_timer_state', JSON.stringify({
            running:       true,
            projectId:     this.projectId,
            taskId:        this.taskId,
            description:   this.description,
            startTime:     this.startTime,
            entryId:       this.entryId,
            lastHeartbeat: this.lastHeartbeat
        }));
    }

    // ── Phase 9: Screenshot Capture ───────────────────────────────────────────

    // Schedule next capture using the project-configured min/max interval
    scheduleNextScreenshot() {
        if (this.screenshotTimeout) clearTimeout(this.screenshotTimeout);
        const minMs = this.screenshotMinMs || 5  * 60000;
        const maxMs = this.screenshotMaxMs || 15 * 60000;
        // If min === max it's a fixed interval; otherwise pick a random value in range
        const delay = (minMs === maxMs)
            ? minMs
            : Math.floor(Math.random() * (maxMs - minMs + 1)) + minMs;
        const delayMin = Math.round(delay / 60000 * 10) / 10;
        console.log(`Phase 9: next screenshot in ${delayMin} min`);
        this.screenshotTimeout = setTimeout(() => this.captureScreenshot(), delay);
    }

    async captureScreenshot() {
        // Only capture if the timer is still running
        if (!this.startTime || !this.entryId) return;

        // html2canvas must be loaded — if not, skip silently
        if (typeof html2canvas !== 'function') {
            console.warn('Phase 9: html2canvas not loaded — skipping screenshot');
            this.scheduleNextScreenshot();
            return;
        }

        try {
            const canvas    = await html2canvas(document.body, {
                scale:           0.4,        // small file size
                useCORS:         true,
                allowTaint:      true,
                backgroundColor: '#1a1a2e',  // TimeForge dark background fallback
                logging:         false,
            });
            const imageData = canvas.toDataURL('image/jpeg', 0.7);

            const body = new FormData();
            body.append('entry_id',       this.entryId);
            body.append('project_id',     this.projectId);
            body.append('activity_score', this.mouseEvents + this.keyEvents);
            body.append('image',          imageData);

            const resp = await fetch('/TimeForge_Capstone/api/upload_screenshot.php', {
                method: 'POST',
                body,
            });
            const json = await resp.json();

            if (json.success) {
                this.screenshotCount++;
                document.getElementById('tf-screenshot-count').textContent = this.screenshotCount;
                console.log(`Phase 9: Screenshot #${this.screenshotCount} saved — ${json.size_kb} KB`);
            } else {
                console.warn('Phase 9: Screenshot upload failed —', json.message);
            }
        } catch (err) {
            console.warn('Phase 9: Screenshot capture error —', err);
        }

        // Schedule the next one regardless of success/failure
        if (this.startTime) this.scheduleNextScreenshot();
    }

    /**
     * testScreenshot() — DEV HELPER
     * Call from the browser console: window.timeTracker.testScreenshot()
     * Fires an immediate capture without waiting for the 5-15 min scheduler.
     */
    async testScreenshot() {
        if (!this.startTime || !this.entryId) {
            console.warn('Phase 9 TEST: Timer is not running — start a timer first.');
            return;
        }
        console.log('Phase 9 TEST: Forcing immediate screenshot…');
        // Cancel any pending scheduled shot so it doesn't double-fire
        if (this.screenshotTimeout) {
            clearTimeout(this.screenshotTimeout);
            this.screenshotTimeout = null;
        }
        await this.captureScreenshot();
        console.log('Phase 9 TEST: Done. Check admin/screenshots.php to verify.');
    }

    // ── Display ───────────────────────────────────────────────────────────────

    updateDisplay() {
        if (!this.startTime) return;
        const diff    = Date.now() - this.startTime;
        const hours   = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);
        const hms     = `${String(hours).padStart(2,'0')}:${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
        this.elements.timeDisplay.textContent = hms;
        document.title = `(${hms}) TimeForge`;
    }
}

// ── Bootstrap ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    window.timeTracker = new TimeTracker();
});