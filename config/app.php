<?php
/**
 * Application-level constants.
 *
 * APP_BASE  – the URL prefix for this project (no trailing slash).
 *             Change this once when moving between environments.
 * APP_URL   – full origin + base path (useful for absolute email links).
 */
if (!defined('APP_BASE')) {
    define('APP_BASE', '/TimeForge_Capstone');
}
if (!defined('APP_URL')) {
    define('APP_URL', 'http://localhost' . APP_BASE);
}

/**
 * Returns an app-root-relative URL.
 *
 * Usage:  href="<?= base_url('login.php') ?>"
 *         href="<?= base_url('css/style.css') ?>"
 *
 * @param  string $path  Path relative to the project root (leading slash optional).
 * @return string
 */
if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $path = ltrim($path, '/');
        return APP_BASE . ($path !== '' ? '/' . $path : '');
    }
}
