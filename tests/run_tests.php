<?php
/**
 * tests/run_tests.php — TimeForge Full Test Runner
 * Groups:
 *   A  — Screenshot JS state persistence
 *   B  — Screenshot upload directory permissions
 *   C  — task_detail.php client nav                [FIXED]
 *   D  — Security: session fixation fix
 *   E  — Security: CSRF helpers in Auth.php
 *   F  — Security: CSRF token in all critical POST forms
 *   G  — Security: CSRF validation in all POST handlers
 *   H  — Security: login rate limiting
 *   I  — Cleanup: utility files deleted
 *   J  — UX: budget overage warning
 *
 * Run: /Applications/XAMPP/xamppfiles/bin/php tests/run_tests.php
 */
declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
$green = $isCli ? "\033[32m" : ''; $red  = $isCli ? "\033[31m" : '';
$cyan  = $isCli ? "\033[36m" : ''; $bold = $isCli ? "\033[1m"  : '';
$reset = $isCli ? "\033[0m"  : '';
$pass = 0; $fail = 0; $tests = [];

function ok(string $name, bool $result, string $detail = ''): void {
    global $pass, $fail, $tests, $green, $red, $reset;
    if ($result) { $pass++; } else { $fail++; }
    $tests[] = ['name' => $name, 'pass' => $result, 'detail' => $detail];
    $status = $result ? "{$green}PASS{$reset}" : "{$red}FAIL{$reset}";
    echo "  {$status}  " . str_pad($name, 66, '.') . " {$detail}\n";
}
function has(string $file, string $needle): bool {
    return file_exists($file) && str_contains(file_get_contents($file), $needle);
}
function hasNot(string $file, string $needle): bool {
    return file_exists($file) && !str_contains(file_get_contents($file), $needle);
}

$r    = dirname(__DIR__);
$js   = "$r/js/time_tracker.js";
$ssA  = "$r/api/upload_screenshot.php";
$ssD  = "$r/uploads/screenshots";
$upD  = "$r/uploads";
$det  = "$r/task_detail.php";
$hdr  = "$r/includes/header_partial.php";
$auth = "$r/src/Core/Auth.php";
$cli  = "$r/src/Controllers/ClientController.php";
$pro  = "$r/src/Controllers/ProjectController.php";
$tim  = "$r/src/Controllers/TimeController.php";
$pd   = "$r/project_details.php";
$W    = 70;

echo "\n" . str_repeat('═',$W) . "\n";
echo "  TimeForge — Full Test Runner  (May 2026)\n";
echo str_repeat('═',$W) . "\n\n";

// ── A: Screenshot JS ─────────────────────────────────────────────────────────
echo "{$bold}GROUP A — Screenshot: JS state persistence{$reset}\n";
ok('A01 time_tracker.js exists',                              file_exists($js));
ok('A02 saveState() persists screenshotsEnabled',             has($js,'screenshotsEnabled: this.screenshotsEnabled'));
ok('A03 saveState() persists screenshotMinMs',                has($js,'screenshotMinMs:    this.screenshotMinMs'));
ok('A04 saveState() persists screenshotMaxMs',                has($js,'screenshotMaxMs:    this.screenshotMaxMs'));
ok('A05 saveState() persists screenshotCount',                has($js,'screenshotCount:    this.screenshotCount'));
ok('A06 _doRestore() reads state.screenshotsEnabled',         has($js,'state.screenshotsEnabled'));
ok('A07 _doRestore() reads state.screenshotMinMs',            has($js,'state.screenshotMinMs'));
ok('A08 _doRestore() reads state.screenshotMaxMs',            has($js,'state.screenshotMaxMs'));
ok('A09 _doRestore() reads state.screenshotCount',            has($js,'state.screenshotCount'));
ok('A10 _doRestore() calls scheduleNextScreenshot()',         has($js,'this.scheduleNextScreenshot();'));
ok('A11 scheduleNextScreenshot() uses screenshotMinMs',       has($js,'this.screenshotMinMs'));
ok('A12 scheduleNextScreenshot() uses screenshotMaxMs',       has($js,'this.screenshotMaxMs'));
ok('A13 captureScreenshot() re-schedules on success',         has($js,'if (this.startTime) this.scheduleNextScreenshot()'));
ok('A14 No duplicate scheduleNextScreenshot in _doRestore',   (function() use ($js): bool {
    $src = file_get_contents($js);
    if (!preg_match('/_doRestore\(state\)\s*\{(.+?)^\s+\}/ms', $src, $m)) return false;
    return substr_count($m[1], 'scheduleNextScreenshot') === 1;
})());
echo "\n";

// ── B: Permissions ───────────────────────────────────────────────────────────
echo "{$bold}GROUP B — Screenshot: upload directory permissions{$reset}\n";
ok('B01 uploads/ directory exists',                           is_dir($upD));
ok('B02 uploads/ directory is writable',                      is_writable($upD));
ok('B03 uploads/screenshots/ directory exists',               is_dir($ssD));
ok('B04 uploads/screenshots/ directory is writable',          is_writable($ssD));
ok('B05 upload_screenshot.php exists',                        file_exists($ssA));
ok('B06 upload_screenshot.php creates dir with 0775',         has($ssA,'mkdir($save_dir, 0775, true)'));
ok('B07 upload_screenshot.php validates entry ownership',     has($ssA,'WHERE id = :eid AND user_id = :uid AND project_id = :pid'));
ok('B08 upload_screenshot.php has 50-screenshot cap',         has($ssA,'>= 50'));
ok('B09 uploads/screenshots/ write smoke test',               (function() use ($ssD): bool {
    $t = "$ssD/.wt_".uniqid(); $ok = @file_put_contents($t,'ok')!==false; if($ok)@unlink($t); return $ok;
})());
echo "\n";

// ── C: Client nav ────────────────────────────────────────────────────────────
echo "{$bold}GROUP C — Client nav: task_detail.php{$reset}\n";
ok('C01 task_detail.php exists',                              file_exists($det));
ok('C02 header_partial.php exists',                           file_exists($hdr));
ok('C03 task_detail.php includes header_partial.php',         has($det,'includes/header_partial.php'));
ok('C04 task_detail.php includes footer_partial.php',         has($det,'includes/footer_partial.php'));
ok('C05 header_partial.php has client role nav branch',       has($hdr,"hasRole('client')"));
ok('C06 header_partial.php client branch → client/dashboard', has($hdr,'client/dashboard.php'));
ok('C07 header_partial.php client branch → client/projects',  has($hdr,'client/projects.php'));
ok('C08 task_detail.php loads client-portal.css for client',  has($det,'client-portal.css'));
ok('C09 task_detail.php body not hardcoded dark bg',          hasNot($det,'<body style="background:#0f172a'));
ok('C10 task_detail.php uses role-conditional body class',    has($det,'task-detail-page'));
ok('C11 task_detail.php no undefined --color-text-secondary', hasNot($det,'var(--color-text-secondary)'));
ok('C12 client-portal.css loaded conditionally for client',   has($det,"\$role === 'client'"));
echo "\n";

// ── D: Session fixation ───────────────────────────────────────────────────────
echo "{$bold}GROUP D — Security: session fixation fix{$reset}\n";
ok('D01 startSession() calls session_regenerate_id(true)',    has($auth,'session_regenerate_id(true)'));
ok('D02 regenerate_id called BEFORE session data is written', (function() use ($auth): bool {
    $s = file_get_contents($auth);
    $r = strpos($s,'session_regenerate_id(true)');
    $p = strpos($s,"\$_SESSION['user_id']");
    return $r !== false && $p !== false && $r < $p;
})());
echo "\n";

// ── E: CSRF helpers ──────────────────────────────────────────────────────────
echo "{$bold}GROUP E — Security: CSRF helper functions{$reset}\n";
ok('E01 generateCsrfToken() exists in Auth.php',              has($auth,'function generateCsrfToken()'));
ok('E02 verifyCsrfToken() exists in Auth.php',                has($auth,'function verifyCsrfToken()'));
ok('E03 csrf_field() helper exists in Auth.php',              has($auth,'function csrf_field()'));
ok('E04 csrf_field() outputs name="csrf_token"',              has($auth,'name="csrf_token"'));
ok('E05 verifyCsrfToken() uses hash_equals (timing-safe)',    has($auth,'hash_equals('));
ok('E06 generateCsrfToken() uses random_bytes for entropy',   has($auth,'random_bytes('));
ok('E07 verifyCsrfToken() checks for empty token',            has($auth,'empty($token)'));
echo "\n";

// ── F: CSRF in forms ─────────────────────────────────────────────────────────
echo "{$bold}GROUP F — CSRF token in all critical POST forms{$reset}\n";
$formFiles = [
    'F01' => ["$r/add_client.php",             'add client form'],
    'F02' => ["$r/edit_client.php",            'edit client form'],
    'F03' => ["$r/add_project.php",            'add project form'],
    'F04' => ["$r/edit_project.php",           'edit project form'],
    'F05' => ["$r/edit_task.php",              'edit task form'],
    'F06' => ["$r/edit_time_entry.php",        'edit time entry form'],
    'F07' => ["$r/profile.php",               'profile form'],
    'F08' => ["$r/admin/system_settings.php",  'system settings form'],
    'F09' => ["$r/task_detail.php",            'task comment form'],
    'F10' => ["$r/tasks.php",                  'task action forms'],
    'F11' => ["$r/project_details.php",        'project action forms'],
    'F12' => ["$r/invoices/generate.php",      'invoice generate form'],
];
foreach ($formFiles as $id => [$file, $label]) {
    ok("$id csrf_token in $label", has($file,'csrf_token'));
}
echo "\n";

// ── G: CSRF in handlers ──────────────────────────────────────────────────────
echo "{$bold}GROUP G — CSRF validation in POST handlers{$reset}\n";
ok('G01 ClientController.php calls verifyCsrfToken()',        has($cli,'verifyCsrfToken()'));
ok('G02 ProjectController.php calls verifyCsrfToken()',       has($pro,'verifyCsrfToken()'));
ok('G03 TimeController.php calls verifyCsrfToken()',          has($tim,'verifyCsrfToken()'));
ok('G04 task_comment.php calls verifyCsrfToken()',            has("$r/task_comment.php",'verifyCsrfToken()'));
ok('G05 task_action.php calls verifyCsrfToken()',             has("$r/task_action.php",'verifyCsrfToken()'));
ok('G06 edit_task.php calls verifyCsrfToken() on POST',       has("$r/edit_task.php",'verifyCsrfToken()'));
ok('G07 edit_time_entry.php calls verifyCsrfToken() on POST', has("$r/edit_time_entry.php",'verifyCsrfToken()'));
ok('G08 profile.php calls verifyCsrfToken() on POST',         has("$r/profile.php",'verifyCsrfToken()'));
ok('G09 admin/system_settings.php calls verifyCsrfToken()',   has("$r/admin/system_settings.php",'verifyCsrfToken()'));
ok('G10 invoices/generate.php calls verifyCsrfToken()',       has("$r/invoices/generate.php",'verifyCsrfToken()'));
echo "\n";

// ── H: Rate limiting ─────────────────────────────────────────────────────────
echo "{$bold}GROUP H — Security: login rate limiting{$reset}\n";
ok('H01 isLoginRateLimited() function exists in Auth.php',    has($auth,'function isLoginRateLimited('));
ok('H02 rate limit threshold is 5 attempts',                  has($auth,'>= 5'));
ok('H03 rate limit window is 15 minutes',                     has($auth,'INTERVAL 15 MINUTE'));
ok('H04 authenticateUser() calls isLoginRateLimited()',       has($auth,'isLoginRateLimited('));
ok('H05 rate limit check happens before password verify',     (function() use ($auth): bool {
    $s = file_get_contents($auth);
    $r = strpos($s,'isLoginRateLimited(');
    $p = strpos($s,'verifyPassword(');
    return $r !== false && $p !== false && $r < $p;
})());
ok('H06 blocked login logs login_rate_limited audit action',  has($auth,'login_rate_limited'));
echo "\n";

// ── I: Cleanup ───────────────────────────────────────────────────────────────
echo "{$bold}GROUP I — Cleanup: utility files deleted{$reset}\n";
ok('I01 fix_permissions.php deleted from repo',               !file_exists("$r/fix_permissions.php"));
ok('I02 test_screenshot.php deleted from repo',               !file_exists("$r/test_screenshot.php"));
echo "\n";

// ── J: Budget overage ────────────────────────────────────────────────────────
echo "{$bold}GROUP J — UX: budget overage warning{$reset}\n";
ok('J01 project_details.php has budget-overage-alert element', has($pd,'budget-overage-alert'));
ok('J02 overage banner shown only when budget_remaining < 0',  has($pd,'$budget_remaining < 0'));
ok('J03 banner references total_cost value',                   has($pd,'$total_cost'));
ok('J04 banner references budget value',                       has($pd,'$budget'));
echo "\n";

// ── Summary ───────────────────────────────────────────────────────────────────
$total = $pass + $fail;
$f     = (int)(($pass / max($total,1)) * 44);
$bar   = str_repeat('█',$f).str_repeat('░',44-$f);
echo str_repeat('═',$W)."\n";
printf("  Results: %s%d / %d passed%s  [%s]\n",$bold,$pass,$total,$reset,$bar);
if ($fail > 0) {
    echo "\n  {$red}{$bold}FAILING tests:{$reset}\n";
    foreach ($tests as $t) {
        if (!$t['pass']) echo "    {$red}✗{$reset}  {$t['name']}\n";
    }
}
echo str_repeat('═',$W)."\n\n";
exit($fail > 0 ? 1 : 0);
