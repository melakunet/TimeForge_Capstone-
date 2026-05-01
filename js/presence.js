/**
 * js/presence.js — Phase 6.7
 * Live Freelancer Presence Panel — polls api/presence.php every 30 seconds.
 * No WebSocket, no paid service — pure PHP short-polling.
 */

(function () {
    const POLL_INTERVAL = 10000; // 10 seconds
    const panel   = document.getElementById('presence-panel');
    const updated = document.getElementById('presence-updated');

    if (!panel) return; // Only runs on pages that have the presence panel

    function statusDot(status, label) {
        const colors = { active: '#2ecc71', idle: '#f39c12', offline: '#555' };
        const color  = colors[status] || '#555';
        // Pulse animation only for active users
        const pulse = status === 'active'
            ? `box-shadow: 0 0 0 3px ${color}33, 0 0 8px ${color};`
            : '';
        return `<span style="
            display: inline-block;
            width: 10px; height: 10px;
            border-radius: 50%;
            background: ${color};
            margin-right: 8px;
            ${pulse}
            vertical-align: middle;
        "></span>`;
    }

    function renderPanel(data) {
        if (!data.success || !data.users.length) {
            panel.innerHTML = '<p style="color:#888;">No freelancers found.</p>';
            return;
        }

        const rows = data.users.map(u => {
            const dot     = statusDot(u.status, u.label);
            const project = u.project_name
                ? `<span style="color:var(--color-accent); font-size:0.85rem;">▶ ${u.project_name}${u.elapsed ? ' — ' + u.elapsed : ''}</span>`
                : '';
            // Show green "Online" badge for active users not on a timer
            const statusBadge = u.status === 'active' && !u.project_name
                ? `<span style="color:#2ecc71; font-size:0.82rem; font-weight:600;">● Online</span>`
                : `<span style="color:#888; font-size:0.82rem;">${u.label}</span>`;
            const label = statusBadge;

            return `
            <div style="
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.65rem 0;
                border-bottom: 1px solid rgba(255,255,255,0.06);
            ">
                <div>
                    ${dot}
                    <strong>${u.name}</strong>
                    <span style="font-size:0.75rem; color:#666; margin-left:6px;">[${u.role}]</span>
                </div>
                <div style="text-align:right;">
                    ${project}
                    <br>${label}
                </div>
            </div>`;
        }).join('');

        panel.innerHTML = rows;
        updated.textContent = `Last updated: ${data.ts}`;
    }

    function poll() {
        fetch('/TimeForge_Capstone/api/presence.php')
            .then(r => r.json())
            .then(data => renderPanel(data))
            .catch(() => {
                updated.textContent = 'Connection error — retrying…';
            });
    }

    // First load immediately, then every 30s
    poll();
    setInterval(poll, POLL_INTERVAL);
})();
