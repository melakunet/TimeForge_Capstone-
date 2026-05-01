<?php
// Footer Partial (markup only) for instructor-style pages
?>
<footer>
  <p>&copy; <?php echo date('Y'); ?> TimeForge. All rights reserved.</p>
  <p>Professional Time Tracking & Project Management Solution</p>
  <p>Web Capstone Project by Etefworkie Melaku — triOS College, Mobile and Web App Development</p>
</footer>

<!-- Scripts -->
<script src="/TimeForge_Capstone/js/theme.js"></script>
<script src="/TimeForge_Capstone/js/animations.js"></script>
<!-- Phase 9: html2canvas — used by time_tracker.js for DOM screenshots -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<?php if (isset($_SESSION['user_id'])): ?>
<!-- Presence ping: keeps last_active_at fresh so admin can see who is online -->
<script>
(function () {
    function sendPing() {
        // Only ping if the timer is NOT already sending heartbeats
        if (window.timeTracker && window.timeTracker.startTime) return;
        const fd = new FormData();
        fd.append('action', 'ping');
        navigator.sendBeacon('/TimeForge_Capstone/api/time_tracking.php', fd);
    }
    // Ping immediately on page load, then every 60 seconds
    sendPing();
    setInterval(sendPing, 60000);
})();
</script>
<?php endif; ?>
