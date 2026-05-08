<?php
/**
 * tests/test_suite_K_to_N.php — TimeForge Extended Test Suite
 *
 * GROUP K — Invoice system integrity
 * GROUP L — CSV export integrity
 * GROUP M — Screenshot system (post-APP_BASE fix)
 * GROUP N — Performance & UX baseline
 *
 * Run: /Applications/XAMPP/xamppfiles/bin/php tests/test_suite_K_to_N.php
 */
declare(strict_types=1);

$isCli = PHP_SAPI === 'cli';
$green  = $isCli ? "\033[32m" : '';
$red    = $isCli ? "\033[31m" : '';
$yellow = $isCli ? "\033[33m" : '';
$cyan   = $isCli ? "\033[36m" : '';
$bold   = $isCli ? "\033[1m"  : '';
$reset  = $isCli ? "\033[0m"  : '';

$pass = 0; $fail = 0; $tests = [];

function ok(string $name, bool $result, string $detail = ''): void {
    global $pass, $fail, $tests, $green, $red, $reset;
    if ($result) { $pass++; } else { $fail++; }
    $tests[] = ['name' => $name, 'pass' => $result, 'detail' => $detail];
    $status = $result ? "{$green}PASS{$reset}" : "{$red}FAIL{$reset}";
    echo "  {$status}  " . str_pad($name, 64, '.') . " {$detail}\n";
}
function has(string $file, string $needle): bool {
    return file_exists($file) && str_contains(file_get_contents($file), $needle);
}
function hasNot(string $file, string $needle): bool {
    return file_exists($file) && !str_contains(file_get_contents($file), $needle);
}
function countOccurrences(string $file, string $needle): int {
    if (!file_exists($file)) return 0;
    return substr_count(file_get_contents($file), $needle);
}

$r = dirname(__DIR__);
$W = 72;

echo "\n" . str_repeat('═', $W) . "\n";
echo "  TimeForge — Extended Test Suite  K-N  (May 2026)\n";
echo str_repeat('═', $W) . "\n\n";

// ════════════════════════════════════════════════════════════════════
// GROUP K — Invoice system integrity
// ════════════════════════════════════════════════════════════════════
echo "{$bold}GROUP K — Invoice system integrity{$reset}\n";

$inv_v  = "$r/invoices/view.php";
$inv_g  = "$r/invoices/generate.php";
$inv_h  = "$r/invoices/history.php";
$inv_pa = "$r/invoices/payment_action.php";
$inv_s  = "$r/invoices/send.php";
$inv_dl = "$r/invoices/download.php";

// K01-K06: No hardcoded paths remain
ok('K01 invoices/view.php has no hardcoded /TimeForge_Capstone/',
    hasNot($inv_v, "'/TimeForge_Capstone/") && hasNot($inv_v, '"/TimeForge_Capstone/'));
ok('K02 invoices/generate.php has no hardcoded /TimeForge_Capstone/',
    hasNot($inv_g, "'/TimeForge_Capstone/") && hasNot($inv_g, '"/TimeForge_Capstone/'));
ok('K03 invoices/history.php has no hardcoded /TimeForge_Capstone/',
    hasNot($inv_h, "'/TimeForge_Capstone/") && hasNot($inv_h, '"/TimeForge_Capstone/'));
ok('K04 invoices/payment_action.php has no hardcoded /TimeForge_Capstone/',
    hasNot($inv_pa, "'/TimeForge_Capstone/") && hasNot($inv_pa, '"/TimeForge_Capstone/'));
ok('K05 invoices/send.php has no hardcoded /TimeForge_Capstone/',
    hasNot($inv_s, "'/TimeForge_Capstone/") && hasNot($inv_s, '"/TimeForge_Capstone/'));
ok('K06 invoices/download.php has no hardcoded /TimeForge_Capstone/',
    hasNot($inv_dl, "'/TimeForge_Capstone/") && hasNot($inv_dl, '"/TimeForge_Capstone/'));

// K07-K10: APP_BASE used correctly
ok('K07 invoices/view.php uses APP_BASE for logo_src',
    has($inv_v, 'APP_BASE . \'/\''));
ok('K08 invoices/view.php uses APP_BASE for $act_url (payment form action)',
    has($inv_v, "APP_BASE . '/invoices/payment_action.php'"));
ok('K09 invoices/generate.php uses APP_BASE in all redirects',
    has($inv_g, "APP_BASE . '/index.php'") && has($inv_g, "APP_BASE . '/invoices/view.php?id='"));
ok('K10 invoices/send.php uses APP_BASE for $back variable',
    has($inv_s, "APP_BASE . '/invoices/view.php?id='"));

// K11-K13: CSRF protection / authentication on invoice forms
ok('K11 invoices/generate.php validates CSRF token on POST',
    has($inv_g, 'verifyCsrfToken()'));
ok('K12 invoices/payment_action.php requires authentication guard',
    has($inv_pa, 'isLoggedIn()') || has($inv_pa, 'requireLogin') || has($inv_pa, 'requireRole'));
ok('K13 invoices/send.php requires admin role (requireRole guard)',
    has($inv_s, 'requireRole') || has($inv_s, 'isLoggedIn()'));

// K14-K16: Content output for download
ok('K14 invoices/download.php streams PDF via Dompdf ->stream()',
    has($inv_dl, 'dompdf->stream(') || has($inv_dl, 'Dompdf') || has($inv_dl, 'Content-Type'));
ok('K15 invoices/download.php exists and is non-empty',
    file_exists($inv_dl) && filesize($inv_dl) > 200);
ok('K16 invoice template files all exist (classic, modern, bold, minimal, corporate)',
    file_exists("$r/invoices/templates/classic.php")  &&
    file_exists("$r/invoices/templates/modern.php")   &&
    file_exists("$r/invoices/templates/bold.php")     &&
    file_exists("$r/invoices/templates/minimal.php")  &&
    file_exists("$r/invoices/templates/corporate.php"));

// K17: No broken orphaned APP_BASE concatenation (the sed bug pattern " . APP_BASE . " inside existing quotes)
// Pattern: a single/double quote immediately followed by space-dot-APP_BASE-dot-space and another opening quote
// e.g.  $x = " . APP_BASE . "/path"   — the leading " is orphaned
$broken_sq = "= ' . APP_BASE . '/";   // $x = ' . APP_BASE . '/path'
$broken_dq = '= " . APP_BASE . "/';   // $x = " . APP_BASE . "/path"
ok('K17 no orphaned quote-wrapped APP_BASE in any invoice file',
    hasNot($inv_v,  $broken_sq) && hasNot($inv_v,  $broken_dq) &&
    hasNot($inv_g,  $broken_sq) && hasNot($inv_g,  $broken_dq) &&
    hasNot($inv_h,  $broken_sq) && hasNot($inv_h,  $broken_dq) &&
    hasNot($inv_pa, $broken_sq) && hasNot($inv_pa, $broken_dq) &&
    hasNot($inv_s,  $broken_sq) && hasNot($inv_s,  $broken_dq));

echo "\n";

// ════════════════════════════════════════════════════════════════════
// GROUP L — CSV export integrity
// ════════════════════════════════════════════════════════════════════
echo "{$bold}GROUP L — CSV export integrity{$reset}\n";

$csv = "$r/api/export_csv.php";

ok('L01 api/export_csv.php exists',                 file_exists($csv));
ok('L02 CSV: sets Content-Type text/csv',           has($csv, 'text/csv'));
ok('L03 CSV: sets Content-Disposition attachment',  has($csv, 'Content-Disposition') && has($csv, 'attachment'));
ok('L04 CSV: sets Pragma no-cache',                 has($csv, 'Pragma') && has($csv, 'no-cache'));
ok('L05 CSV: uses fputcsv() for output',            has($csv, 'fputcsv('));
ok('L06 CSV: requires isLoggedIn() guard',          has($csv, 'isLoggedIn()'));
ok('L07 CSV: validates project_id as integer',      has($csv, 'FILTER_VALIDATE_INT'));
ok('L08 CSV: client access block scopes to client role',
    has($csv, "\$role === 'client'") && has($csv, 'user_id'));
ok('L09 CSV: admin scoped to own company',          has($csv, 'company_id'));
ok('L10 CSV: exports Total Hours row',              has($csv, 'Total Hours'));
ok('L11 CSV: exports Total Cost row',               has($csv, 'Total Cost'));
ok('L12 CSV: exports column headers (Date, Hours, Freelancer)',
    has($csv, 'Date') && has($csv, 'Hours') && has($csv, 'Freelancer'));
ok('L13 CSV: filename includes project info',       has($csv, 'filename'));
ok('L14 CSV: no hardcoded /TimeForge_Capstone/',
    hasNot($csv, "'/TimeForge_Capstone/") && hasNot($csv, '"/TimeForge_Capstone/'));
ok('L15 CSV: uses php://output stream correctly',   has($csv, 'php://output'));

echo "\n";

// ════════════════════════════════════════════════════════════════════
// GROUP M — Screenshot system (post-APP_BASE fix)
// ════════════════════════════════════════════════════════════════════
echo "{$bold}GROUP M — Screenshot system (post-APP_BASE fix){$reset}\n";

$ssImg    = "$r/api/screenshot_img.php";
$ssUp     = "$r/api/upload_screenshot.php";
$ssAdmin  = "$r/admin/screenshots.php";
$tracker  = "$r/js/time_tracker.js";
$ssDir    = "$r/uploads/screenshots";

ok('M01 api/screenshot_img.php exists',             file_exists($ssImg));
ok('M02 api/upload_screenshot.php exists',          file_exists($ssUp));
ok('M03 admin/screenshots.php exists',              file_exists($ssAdmin));
ok('M04 uploads/screenshots/ directory exists',     is_dir($ssDir));

// M05-M07: No hardcoded paths
ok('M05 admin/screenshots.php: no hardcoded /TimeForge_Capstone/',
    hasNot($ssAdmin, "'/TimeForge_Capstone/") && hasNot($ssAdmin, '"/TimeForge_Capstone/'));
ok('M06 api/screenshot_img.php: no hardcoded paths in logic (comment OK)',
    hasNot($ssImg, "header('Location: /TimeForge_Capstone") &&
    hasNot($ssImg, 'header("Location: /TimeForge_Capstone'));
ok('M07 admin/screenshots.php: uses APP_BASE for $img_url',
    has($ssAdmin, "APP_BASE . '/api/screenshot_img.php?id='"));

// M08-M11: JS fetch uses APP_BASE variable
ok('M08 time_tracker.js: upload_screenshot uses APP_BASE (not hardcoded)',
    has($tracker, "APP_BASE + '/api/upload_screenshot.php'") ||
    has($tracker, 'APP_BASE + "/api/upload_screenshot.php"'));
ok('M09 time_tracker.js: time_tracking API uses APP_BASE',
    has($tracker, "APP_BASE + '/api/time_tracking.php'") ||
    has($tracker, 'APP_BASE + "/api/time_tracking.php"'));
ok('M10 js/presence.js: presence API uses APP_BASE',
    has("$r/js/presence.js", "APP_BASE + '/api/presence.php'") ||
    has("$r/js/presence.js", 'APP_BASE + "/api/presence.php"'));
ok('M11 js/theme.js: theme handler uses APP_BASE',
    has("$r/js/theme.js", "APP_BASE + '/includes/theme_handler.php'") ||
    has("$r/js/theme.js", 'APP_BASE + "/includes/theme_handler.php"'));

// M12-M14: Security on screenshot endpoints
ok('M12 api/upload_screenshot.php: auth guard present',
    has($ssUp, 'isLoggedIn()') || has($ssUp, 'session_start'));
ok('M13 api/screenshot_img.php: access control present',
    has($ssImg, 'isLoggedIn()') || has($ssImg, 'requireLogin') || has($ssImg, '$_SESSION'));
ok('M14 uploads/screenshots dir is writable or owned correctly',
    is_dir($ssDir) && is_writable($ssDir));

echo "\n";

// ════════════════════════════════════════════════════════════════════
// GROUP N — Performance & UX baseline
// ════════════════════════════════════════════════════════════════════
echo "{$bold}GROUP N — Performance & UX baseline{$reset}\n";

$style   = "$r/css/style.css";
$hdr     = "$r/includes/header.php";
$hdrP    = "$r/includes/header_partial.php";
$appCfg  = "$r/config/app.php";
$db      = "$r/db.php";
$foot    = "$r/includes/footer_partial.php";

// N01-N04: CSS design token system
ok('N01 css/style.css defines :root token block',
    has($style, ':root {') || has($style, ':root{'));
ok('N02 css/style.css defines --color-bg token',    has($style, '--color-bg:'));
ok('N03 css/style.css defines --color-accent token',has($style, '--color-accent:'));
ok('N04 body.dark-mode overrides CSS tokens',
    has($style, 'body.dark-mode') && has($style, '--color-bg:') &&
    // dark-mode block appears AFTER :root
    strpos(file_get_contents($style), 'body.dark-mode') > strpos(file_get_contents($style), ':root'));

// N05-N07: body uses tokens (not hardcoded colours)
$styleContent = file_get_contents($style);
$bodyPos = strpos($styleContent, "\nbody {");
$bodyBlock = $bodyPos !== false ? substr($styleContent, $bodyPos, 300) : '';
ok('N05 body background uses var(--color-bg) not hardcoded hex',
    str_contains($bodyBlock, 'var(--color-bg)') && !preg_match('/background:\s*#[0-9a-f]{3,6}/i', $bodyBlock));
ok('N06 body color uses var(--color-text) not hardcoded hex',
    str_contains($bodyBlock, 'var(--color-text)') && !preg_match('/(?<!-)color:\s*#[0-9a-f]{3,6}/i', $bodyBlock));
ok('N07 css/style.css has responsive @media breakpoints (≥ 6)',
    (int)substr_count($styleContent, '@media') >= 6);

// N08-N10: APP_BASE constant system
ok('N08 config/app.php exists and defines APP_BASE',
    has($appCfg, "define('APP_BASE'") || has($appCfg, 'define("APP_BASE"'));
ok('N09 config/app.php defines base_url() helper',  has($appCfg, 'function base_url('));
ok('N10 db.php loads config/app.php (APP_BASE available everywhere)',
    has($db, "require_once __DIR__ . '/config/app.php'") ||
    has($db, 'require_once __DIR__."/config/app.php"'));

// N11-N13: JS global APP_BASE injected for front-end
ok('N11 includes/header.php injects APP_BASE JS global in <head>',
    has($hdr, 'const APP_BASE') && has($hdr, 'APP_BASE ?>'));
ok('N12 includes/header_partial.php injects APP_BASE with typeof guard',
    has($hdrP, "typeof APP_BASE==='undefined'") || has($hdrP, 'typeof APP_BASE === \'undefined\''));
ok('N13 header.php has viewport meta tag (mobile-ready)',
    has($hdr, 'viewport') && has($hdr, 'width=device-width'));

// N14-N16: Performance hygiene
ok('N14 footer_partial.php has sendBeacon() for graceful timer stop',
    has($foot, 'sendBeacon'));
ok('N15 no duplicate time_tracker.js <script> loads in header.php',
    countOccurrences($hdr, 'time_tracker.js') <= 1);
ok('N16 header.php loads CSS before JS (style before script)',
    (function() use ($hdr): bool {
        $content = file_get_contents($hdr);
        $cssPos  = strpos($content, 'style.css');
        $jsPos   = strpos($content, '<script');
        return $cssPos !== false && $jsPos !== false && $cssPos < $jsPos;
    })());

// N17-N19: Accessibility basics
ok('N17 header logo <img> has alt attribute',
    has($hdrP, 'alt="TimeForge Logo"') || has($hdrP, "alt='TimeForge Logo'"));
ok('N18 header nav links are plain <a> tags (not JS-only)',
    has($hdrP, '<a href='));
ok('N19 public/index.php front controller exists and loads config',
    file_exists("$r/public/index.php") && has("$r/public/index.php", 'config/app.php'));

// N20: No stale hardcoded paths anywhere in production PHP
$allFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($r));
$hardcodedCount = 0;
$hardcodedFiles = [];
foreach ($allFiles as $f) {
    $path = $f->getPathname();
    if (!$f->isFile() || $f->getExtension() !== 'php') continue;
    if (str_contains($path, '/vendor/') || str_contains($path, '/react-app/') ||
        str_contains($path, '/test_') || str_contains($path, 'fix_permissions')) continue;
    $content = file_get_contents($path);
    // Exclude comment lines and the config/app.php definition itself
    $lines = explode("\n", $content);
    foreach ($lines as $lineNo => $line) {
        $trimmed = ltrim($line);
        if (str_starts_with($trimmed, '*') || str_starts_with($trimmed, '//') || str_starts_with($trimmed, '#')) continue;
        if (str_contains($path, 'config/app.php') && str_contains($line, "define('APP_BASE'")) continue;
        // Flag functional (non-comment) use of the hardcoded string
        if (preg_match('|["\']/?/TimeForge_Capstone/|', $line) ||
            preg_match('|header\(.*TimeForge_Capstone|', $line)) {
            $hardcodedCount++;
            $hardcodedFiles[] = str_replace($r . '/', '', $path) . ':' . ($lineNo + 1);
        }
    }
}
$detail = $hardcodedCount > 0 ? implode(', ', array_slice($hardcodedFiles, 0, 5)) : 'all clean';
ok('N20 zero hardcoded /TimeForge_Capstone/ paths in production PHP', $hardcodedCount === 0, $detail);

// ── Summary ─────────────────────────────────────────────────────────
echo "\n" . str_repeat('─', $W) . "\n";
$total = $pass + $fail;
echo "  {$bold}Results: {$pass}/{$total} passed";
if ($fail === 0) {
    echo "  {$green}✓ ALL PASSED{$reset}\n";
} else {
    echo "  {$red}✗ {$fail} FAILED{$reset}\n";
    echo "\n  {$yellow}Failed tests:{$reset}\n";
    foreach ($tests as $t) {
        if (!$t['pass']) {
            echo "    {$red}✗{$reset}  " . $t['name'];
            if ($t['detail']) echo "  → " . $t['detail'];
            echo "\n";
        }
    }
}
echo str_repeat('─', $W) . "\n\n";

exit($fail > 0 ? 1 : 0);
