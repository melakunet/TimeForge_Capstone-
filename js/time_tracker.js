/**
 * TimeForge Advanced Time Tracker
 * Handles client-side timer logic, persistence, and idle detection.
 */

class TimeTracker {
    constructor() {
        this.timerInterval = null;
        this.heartbeatInterval = null;
        this.idleCheckInterval = null;
        this.startTime = null;
        this.projectId = null;
        this.description = null;
        this.lastActivity = Date.now();
        this.isIdle = false;
        
        // Configuration
        this.HEARTBEAT_MS = 60000; // 1 minute
        this.IDLE_THRESHOLD_MS = 300000; // 5 minutes
        
        this.elements = {
            widget: null,
            timeDisplay: null,
            projectLabel: null,
            toggleBtn: null,
            stopBtn: null
        };

        this.init();
    }

    init() {
        this.createWidget();
        this.restoreState();
        this.setupEventListeners();
        this.startIdleDetection();
    }

    createWidget() {
        const widgetHTML = `
            <div id="tf-timer-widget" class="tf-timer-hidden">
                <div class="tf-timer-header">
                    <span class="tf-timer-status-dot"></span>
                    <span id="tf-timer-project">No Project</span>
                    <button id="tf-timer-toggle" class="tf-btn-icon">_</button>
                </div>
                <div class="tf-timer-body">
                    <div id="tf-timer-display">00:00:00</div>
                    <div class="tf-timer-controls">
                        <button id="tf-timer-stop" class="tf-btn-stop">Stop Timer</button>
                    </div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', widgetHTML);

        this.elements.widget = document.getElementById('tf-timer-widget');
        this.elements.timeDisplay = document.getElementById('tf-timer-display');
        this.elements.projectLabel = document.getElementById('tf-timer-project');
        this.elements.toggleBtn = document.getElementById('tf-timer-toggle');
        this.elements.stopBtn = document.getElementById('tf-timer-stop');
    }

    setupEventListeners() {
        this.elements.toggleBtn.addEventListener('click', () => {
            this.elements.widget.classList.toggle('tf-timer-collapsed');
            this.elements.toggleBtn.textContent = 
                this.elements.widget.classList.contains('tf-timer-collapsed') ? '□' : '_';
        });

        this.elements.stopBtn.addEventListener('click', () => this.stopTimer());

        // Activity listeners for idle detection
        ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => {
            document.addEventListener(evt, () => this.resetIdleTimer());
        });
    }

    resetIdleTimer() {
        this.lastActivity = Date.now();
        if (this.isIdle) {
            this.isIdle = false;
            this.elements.widget.classList.remove('tf-timer-idle');
            console.log('User active again');
            // Optionally notify server user is back
        }
    }

    startIdleDetection() {
        this.idleCheckInterval = setInterval(() => {
            if (!this.startTime) return; // Only check if timer is running

            const now = Date.now();
            if (now - this.lastActivity > this.IDLE_THRESHOLD_MS) {
                if (!this.isIdle) {
                    this.isIdle = true;
                    this.elements.widget.classList.add('tf-timer-idle');
                    console.warn('User is idle');
                    // In a real app, we might pause the timer or show a modal here
                }
            }
        }, 10000); // Check every 10 seconds
    }

    restoreState() {
        const stored = localStorage.getItem('tf_timer_state');
        if (stored) {
            const state = JSON.parse(stored);
            if (state.running) {
                this.startTimer(state.projectId, state.description, state.startTime);
            }
        }
    }

    /**
     * Start the timer
     * @param {number} projectId 
     * @param {string} description 
     * @param {number|null} explicitStartTime - timestamp if restoring from storage
     */
    async startTimer(projectId, description, explicitStartTime = null) {
        if (!projectId) return;

        this.projectId = projectId;
        this.description = description;
        this.startTime = explicitStartTime || Date.now();

        // Save state
        this.saveState();

        // specific logic for new timer (not restored)
        if (!explicitStartTime) {
            try {
                const response = await this.sendHeartbeat('start');
                if (!response.success) throw new Error(response.message);
                console.log('Timer started on server');
            } catch (err) {
                console.error('Failed to start timer on server:', err);
                alert('Could not start timer. Please check your connection.');
                this.stopLocalTimer();
                return;
            }
        }

        // Show widget
        this.elements.projectLabel.textContent = `Project #${projectId}`; 
        this.elements.widget.classList.remove('tf-timer-hidden');

        // Start ticking
        if (this.timerInterval) clearInterval(this.timerInterval);
        this.timerInterval = setInterval(() => this.updateDisplay(), 1000);
        this.updateDisplay();

        // Start heartbeat
        if (this.heartbeatInterval) clearInterval(this.heartbeatInterval);
        this.heartbeatInterval = setInterval(() => this.sendHeartbeat('pulse'), this.HEARTBEAT_MS);
    }

    async stopTimer() {
        if (!confirm('Are you sure you want to stop the timer?')) return;

        try {
            await this.sendHeartbeat('stop');
        } catch (err) {
            console.error('Network error stopping timer:', err);
            // We stop locally anyway to prevent UI lock
        }

        this.stopLocalTimer();
    }

    stopLocalTimer() {
        clearInterval(this.timerInterval);
        clearInterval(this.heartbeatInterval);
        this.timerInterval = null;
        this.heartbeatInterval = null;
        this.startTime = null;
        this.projectId = null;
        
        localStorage.removeItem('tf_timer_state');
        this.elements.widget.classList.add('tf-timer-hidden');
    }

    saveState() {
        const state = {
            running: true,
            projectId: this.projectId,
            description: this.description,
            startTime: this.startTime
        };
        localStorage.setItem('tf_timer_state', JSON.stringify(state));
    }

    updateDisplay() {
        if (!this.startTime) return;
        
        const now = Date.now();
        const diff = now - this.startTime;
        
        const hours = Math.floor(diff / 3600000);
        const minutes = Math.floor((diff % 3600000) / 60000);
        const seconds = Math.floor((diff % 60000) / 1000);

        this.elements.timeDisplay.textContent = 
            `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        document.title = `(${this.elements.timeDisplay.textContent}) TimeForge`;
    }

    async sendHeartbeat(action) {
        // This connects to the PHP backend we will build next
        const data = {
            action: action,
            project_id: this.projectId,
            description: this.description,
            timestamp: Date.now()
        };

        const formData = new FormData();
        for (const key in data) {
            formData.append(key, data[key]);
        }

        // We assume an API endpoint exists (Step 3)
        const response = await fetch('api/time_tracking.php', {
            method: 'POST',
            body: formData
        });
        
        return await response.json();
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    window.timeTracker = new TimeTracker();
});
