// Hero animations: clock, sheen, counters, and progress bars
(function () {
  'use strict';

  // === CLOCK HANDS ===
  function updateClock() {
    var now = new Date();
    var h = now.getHours() % 12;
    var m = now.getMinutes();
    var s = now.getSeconds();
    var ms = now.getMilliseconds();

    // Smooth second hand by including milliseconds
    var hourAngle = h * 30 + m * 0.5 + (s + ms / 1000) * (0.5 / 60);
    var minuteAngle = m * 6 + (s + ms / 1000) * 0.1;
    var secondAngle = (s + ms / 1000) * 6;

    var hourHand = document.getElementById('clockHour');
    var minuteHand = document.getElementById('clockMinute');
    var secondHand = document.getElementById('clockSecond');

    if (hourHand && minuteHand && secondHand) {
      hourHand.style.transform = 'translate(0, -50%) rotate(' + hourAngle + 'deg)';
      minuteHand.style.transform = 'translate(0, -50%) rotate(' + minuteAngle + 'deg)';
      secondHand.style.transform = 'translate(0, -50%) rotate(' + secondAngle + 'deg)';
    }
  }

  // === ROTATING SHEEN ===
  var sheenAngle = 0;
  function animateSheen() {
    var sheen = document.querySelector('.clock-sheen');
    if (sheen) {
      sheenAngle = (sheenAngle + 0.5) % 360; // rotate 0.5deg per frame (~30fps = 15s per rotation)
      sheen.style.transform = 'rotate(' + sheenAngle + 'deg)';
    }
  }

  // === COUNTERS (Time + Cash) ===
  var startTime = Date.now();
  var cashRatePerSecond = 0.25; // demo: $0.25/sec

  function pad(n) {
    return n < 10 ? '0' + n : '' + n;
  }

  function updateStats() {
    var now = new Date();
    var h = now.getHours();
    var m = now.getMinutes();
    var s = now.getSeconds();

    var timeEl = document.getElementById('statTime');
    var timeFill = document.getElementById('statTimeFill');
    var cashEl = document.getElementById('statCash');
    var cashFill = document.getElementById('statCashFill');

    // Display time
    if (timeEl) {
      timeEl.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
    }

    // Animate time bar: fill as current second progresses
    if (timeFill) {
      var pct = (s / 60) * 100;
      timeFill.style.width = pct + '%';
    }

    // Display earnings
    if (cashEl) {
      var elapsedSec = (Date.now() - startTime) / 1000;
      var dollars = elapsedSec * cashRatePerSecond;
      cashEl.textContent = '$' + dollars.toFixed(2);
    }

    // Animate cash bar: loop every 5s
    if (cashFill) {
      var pct2 = ((Date.now() / 1000) % 5) / 5 * 100;
      cashFill.style.width = pct2 + '%';
    }
  }

  // === PROGRESS BAR STRIPE FLOW ===
  var barOffset = 0;
  function animateProgressBars() {
    var fills = document.querySelectorAll('.stat-bar .fill');
    if (fills.length > 0) {
      barOffset = (barOffset + 1) % 40; // move by 1px per frame; cycle every 40px
      for (var i = 0; i < fills.length; i++) {
        fills[i].style.backgroundPosition = '0 0, ' + barOffset + 'px 0';
      }
    }
  }

  // === ANIMATION LOOP ===
  var lastUpdate = 0;
  function animate(timestamp) {
    if (timestamp - lastUpdate > 33) { // ~30fps
      updateClock();
      animateSheen();
      animateProgressBars();
      lastUpdate = timestamp;
    }
    requestAnimationFrame(animate);
  }

  // === INITIALIZATION ===
  function init() {
    updateClock();
    updateStats();
    // Update stats every second
    setInterval(updateStats, 1000);
    // Start smooth animation loop
    requestAnimationFrame(animate);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
