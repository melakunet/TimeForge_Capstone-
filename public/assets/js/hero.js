// Animated analog clock for homepage
(function () {
  function updateClock() {
    var now = new Date();
    var h = now.getHours() % 12;
    var m = now.getMinutes();
    var s = now.getSeconds();

    // Calculate angles
    var hourAngle = h * 30 + m * 0.5 + s * (0.5 / 60); // 30 deg per hour + minute/second contribution
    var minuteAngle = m * 6 + s * 0.1; // 6 deg per minute + second contribution
    var secondAngle = s * 6; // 6 deg per second

    // Apply transforms
    var hourHand = document.getElementById('clockHour');
    var minuteHand = document.getElementById('clockMinute');
    var secondHand = document.getElementById('clockSecond');

    if (hourHand && minuteHand && secondHand) {
      // Anchor the base (left edge) of each hand at center, then rotate
      hourHand.style.transform = 'translate(0, -50%) rotate(' + hourAngle + 'deg)';
      minuteHand.style.transform = 'translate(0, -50%) rotate(' + minuteAngle + 'deg)';
      secondHand.style.transform = 'translate(0, -50%) rotate(' + secondAngle + 'deg)';
    }
  }

  // Pad helper
  function pad(n) { return n < 10 ? '0' + n : '' + n; }

  // Demo counters (time + earnings)
  var startTime = Date.now();
  var cashRatePerSecond = 0.25; // demo rate: $0.25 per second

  function updateStats() {
    var now = new Date();
    var h = now.getHours();
    var m = now.getMinutes();
    var s = now.getSeconds();

    var timeEl = document.getElementById('statTime');
    var timeFill = document.getElementById('statTimeFill');
    var cashEl = document.getElementById('statCash');
    var cashFill = document.getElementById('statCashFill');

    if (timeEl) {
      timeEl.textContent = pad(h) + ':' + pad(m) + ':' + pad(s);
    }

    if (timeFill) {
      var pct = (s / 60) * 100; // seconds progress in current minute
      timeFill.style.width = pct + '%';
    }

    if (cashEl) {
      var elapsedSec = (Date.now() - startTime) / 1000;
      var dollars = elapsedSec * cashRatePerSecond;
      cashEl.textContent = '$' + dollars.toFixed(2);
    }

    if (cashFill) {
      var pct2 = ((Date.now() / 1000) % 5) / 5 * 100; // loop every 5s
      cashFill.style.width = pct2 + '%';
    }
  }

  function init() {
    updateClock();
    updateStats();
    // Tick every second
    setInterval(function () {
      updateClock();
      updateStats();
    }, 1000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
