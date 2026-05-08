<?php
/**
 * tests/run_tests.php — TimeForge Issue Verification Test Runner
 * Covers the 3 confirmed known issues from DEVLOG (May 2026):
 *   Group A — Screenshot capture state persists across page navigation (JS)
 *   Group B — Screenshot upload directory permissions
 *   Group C — task_detail.php client nav correctness
 *
 * Run from repo root via CLI:
 *   php tests/run_tests.php
 */

declare(strict_types=1);

// ── ANSI colour helpers ──────────────────────────────────────────────────────
$isCli  = PHP_SAPI === 'cli';
$green  = $isCli ? "\033[32m" : '';
$red    = $isCli ? "\033[31m" : '';
$yellow = $isCli ? "\033[33m" : '';
$cyan   = $isCli ? "\033[36m" : '';
$bold   = $isCli ? "\033[1m"  : '';
$reset  = $isCli ? "\033[0m"  : '';

$pass  = 0;
$fail  = 0;
$tests = [];

// ── Test helpers ─────────────────────────────────────────────────────────────

function ok(string $name, bool $result, string $detail = ''): void {
    global $pass, $fail, $tests, $green, $red, $reset, $bold;
    if ($result) {
        $pass++;
        $status = "{$green}PASS{$reset}";
    } else {
        $fail++;
        $status = "{$red}FAIL{$reset}";
    }
    $tests[] = ['name' => $name, 'pass' => $result, 'detail' => $detail];
    $pad = str_pad($name, 72, '.');
    echo "  {$status}  {$pad} {$detail}\n";
}

function assertFileContains(string $file, string $needle): bool {
    if (!file_exists($file)) return false;
    return str_contains(file_get_contents($file), $needle);
}

function assertFileNotContains(string $file, string $needle): bool {
    if (!file_exists($file)) return false;
    return !str_contains(file_get_contents($file), $needle);
}

// ── Paths ────────────────────────────────────────────────────────────────────
$root     = dirname(__DIR__);
$jsFile   = "{$root}/js/time_tracker.js";
$uploadApi= "{$root}/api/upload_screenshot.php";
$taskDetail = "{$root}/task_detail.php";
$headerPart = "{$root}/includes/header_partial.php";
$uploadDir  = "{$root}/uploads";
$ssDir      = "{$root}/uploads/screenshots";

// ═════════════════════════════════════════════════════════════════════════════
echo "\n{$bold}{$cyan}══════════════════════════════════════════════════════════════════{$reset}\n";
echo "{$bold}{$cyan}  TimeForge — Known Issue Test Runner  (May 2026){$reset}\n";
echo "{$bold}{$cyan}══════════════════════════════════════════════════════════════════{$reset}\n\n";

// ─────────────────────────────────────────────────────────────────────────────
echo "{$bold}GROUP A — Screenshot: JS state persistence across navigation{$reset}\n";
// ─────────────────────────────────────────────────────────────────────────────

ok(
    'A01 time_tracker.js exists',
    file_exists($jsFile)
);

ok(
    'A02 saveState() persists screenshotsEnabled',
    assertFileContains($jsFile, "screenshotsEnabled: this.screenshotsEnabled")
);

ok(
    'A03 saveState() persists screenshotMinMs',
    assertFileContains($jsFile, "screenshotMinMs:    this.screenshotMinMs")
);

ok(
    'A04 saveState() persists screenshotMaxMs',
    assertFileContains($jsFile, "screenshotMaxMs:    this.screenshotMaxMs")
);

ok(
    'A05 saveState() persists screenshotCount',
    assertFileContains($jsFile, "screenshotCount:    this.screenshotCount")
);

ok(
    'A06 _doRestore() reads state.screenshotsEnabled',
    assertFileContains($jsFile, "state.screenshotsEnabled")
);

ok(
    'A07 _doRestore() reads state.screenshotMinMs',
    assertFileContains($jsFile, "state.screenshotMinMs")
);

ok(
    'A08 _doRestore() reads state.screenshotMaxMs',
    assertFileContains($jsFile, "state.screenshotMaxMs")
);

ok(
    'A09 _doRestore() reads state.screenshotCount',
    assertFileContains($jsFile, "state.screenshotCount")
);

ok(
    'A10 _doRestore() calls scheduleNextScreenshot()',
    assertFileContains($jsFile, 'this.scheduleNextScreenshot();')
);

ok(
    'A11 scheduleNextScreenshot() uses screenshotMinMs',
    assertFileContains($jsFile, 'this.screenshotMinMs')
);

ok(
    'A12 scheduleNextScreenshot() uses screenshotMaxMs',
    assertFileContains($jsFile, 'this.screenshotMaxMs')
);

ok(
    'A13 captureScreenshot() re-schedules on success',
    assertFileContains($jsFile, "if (this.startTime) this.scheduleNextScreenshot()")
);

ok(
    'A14 No duplicate scheduleNextScreenshot calls inside _doRestore',
    (function() use ($jsFile): bool {
        $src = file_get_contents($jsFile);
        // Extract only the _doRestore method block (from definition to next method)
        if (!preg_match('/_doRestore\(state\)\s*\{(.+?)^\s+\}/ms', $src, $m)) return false;
        return substr_count($m[1], 'scheduleNextScreenshot') === 1;
    })()
);

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
echo "{$bold}GROUP B — Screenshot: upload directory permissions{$reset}\n";
// ─────────────────────────────────────────────────────────────────────────────

ok(
    'B01 uploads/ directory exists',
    is_dir($uploadDir)
);

ok(
    'B02 uploads/ directory is writable',
    is_writable($uploadDir)
);

ok(
    'B03 uploads/screenshots/ directory exists',
    is_dir($ssDir)
);

ok(
    'B04 uploads/screenshots/ directory is writable',
    is_writable($ssDir)
);

ok(
    'B05 upload_screenshot.php exists',
    file_exists($uploadApi)
);

ok(
    'B06 upload_screenshot.php creates directory with 0775',
    assertFileContains($uploadApi, 'mkdir($save_dir, 0775, true)')
);

ok(
    'B07 upload_screenshot.php validates entry ownership before write',
    assertFileContains($uploadApi, 'WHERE id = :eid AND user_id = :uid AND project_id = :pid')
);

ok(
    'B08 upload_screenshot.php has 50-screenshot cap guard',
    assertFileContains($uploadApi, '>= 50')
);

// Writable temp-file smoke test
ok(
    'B09 uploads/screenshots/ can actually create a file (smoke test)',
    (function() use ($ssDir): bool {
        $tmp = "{$ssDir}/.write_test_" . uniqid();
        $ok  = @file_put_contents($tmp, 'ok') !== false;
        if ($ok) @unlink($tmp);
        return $ok;
    })()
);

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
echo "{$bold}GROUP C — Client nav: task_detail.php correctness{$reset}\n";
// ─────────────────────────────────────────────────────────────────────────────

ok(
    'C01 task_detail.php exists',
    file_exists($taskDetail)
);

ok(
    'C02 header_partial.php exists',
    file_exists($headerPart)
);

ok(
    'C03 task_detail.php includes header_partial.php',
    assertFileContains($taskDetail, "includes/header_partial.php")
);

ok(
    'C04 task_detail.php includes footer_partial.php',
    assertFileContains($taskDetail, "includes/footer_partial.php")
);

ok(
    'C05 header_partial.php has client role nav branch',
    assertFileContains($headerPart, "hasRole('client')")
);

ok(
    'C06 header_partial.php client branch links to client/dashboard.php',
    assertFileContains($headerPart, 'client/dashboard.php')
);

ok(
    'C07 header_partial.php client branch links to client/projects.php',
    assertFileContains($headerPart, 'client/projects.php')
);

ok(
    'C08 task_detail.php loads client-portal.css for client role',
    assertFileContains($taskDetail, 'client-portal.css')
);

ok(
    'C09 task_detail.php body does not hardcode dark background unconditionally',
    // Must NOT have the unconditioned inline dark bg on <body>
    assertFileNotContains($taskDetail, '<body style="background:#0f172a')
);

ok(
    'C10 task_detail.php uses role-conditional body class',
    assertFileContains($taskDetail, 'task-detail-page')
);

ok(
    'C11 task_detail.php does not use undefined --color-text-secondary without fallback',
    // var(--color-text-secondary) without a hardcoded fallback is broken — must be absent
    assertFileNotContains($taskDetail, "var(--color-text-secondary)")
);

ok(
    'C12 task_detail.php client-portal.css loaded conditionally (only for client role)',
    assertFileContains($taskDetail, "\$role === 'client'")
);

echo "\n";

// ─────────────────────────────────────────────────────────────────────────────
// Summary
// ─────────────────────────────────────────────────────────────────────────────
$total = $pass + $fail;
$bar   = str_repeat('█', (int)($pass / max($total, 1) * 40))
       . str_repeat('░', 40 - (int)($pass / max($total, 1) * 40));

echo "{$bold}{$cyan}══════════════════════════════════════════════════════════════════{$reset}\n";
echo sprintf(
    "  Results: {$bold}%d / %d passed{$reset}  [{$bar}]\n",
    $pass, $total
);
if ($fail > 0) {
    echo "\n  {$red}{$bold}FAILING tests:{$reset}\n";
    foreach ($tests as $t) {
        if (!$t['pass']) {
            echo "    {$red}✗{$reset}  {$t['name']}" . ($t['detail'] ? "  ({$t['detail']})" : '') . "\n";
        }
    }
}
echo "{$bold}{$cyan}══════════════════════════════════════════════════════════════════{$reset}\n\n";

exit($fail > 0 ? 1 : 0);
