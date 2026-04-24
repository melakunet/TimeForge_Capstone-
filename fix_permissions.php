<?php
// One-time permission fixer — delete this file after running it once.
// Visit: http://localhost/TimeForge_Capstone/fix_permissions.php

$dirs = [
    __DIR__ . '/images',
    __DIR__ . '/images/logos',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "Created: $dir<br>";
        } else {
            echo "FAILED to create: $dir<br>";
        }
    }
    if (chmod($dir, 0777)) {
        echo "chmod 777 OK: $dir<br>";
    } else {
        echo "chmod FAILED: $dir (owner mismatch?)<br>";
    }
    echo "Writable: " . (is_writable($dir) ? '<b style="color:green">YES ✅</b>' : '<b style="color:red">NO ❌</b>') . " — $dir<br><hr>";
}

echo "<br><b>PHP running as user:</b> " . get_current_user() . "<br>";
echo "<b>Process user (posix):</b> ";
if (function_exists('posix_getpwuid')) {
    $u = posix_getpwuid(posix_geteuid());
    echo $u['name'];
} else {
    echo "posix not available";
}
echo "<br><br><b style='color:red'>Delete fix_permissions.php after use!</b>";
