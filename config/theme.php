<?php
// TimeForge Theme Configuration
// Color palette and typography based on brand identity

define('THEME_PRIMARY_DARK', '#1a1a2e');      // Dark blue-black for dark mode background
define('THEME_PRIMARY_LIGHT', '#f8f9fa');     // Light gray for light mode background
define('THEME_ACCENT_BLUE', '#0066cc');       // Bright blue for CTAs and accents
define('THEME_ACCENT_ORANGE', '#ff6b35');     // Orange for highlights and alerts
define('THEME_TEXT_DARK', '#1a1a2e');         // Dark text for light backgrounds
define('THEME_TEXT_LIGHT', '#f8f9fa');        // Light text for dark backgrounds
define('THEME_BORDER_DARK', '#e0e0e0');       // Border color for light mode
define('THEME_BORDER_LIGHT', '#333333');      // Border color for dark mode
define('THEME_SUCCESS', '#28a745');           // Green for success
define('THEME_DANGER', '#dc3545');            // Red for errors
define('THEME_WARNING', '#ffc107');           // Yellow for warnings
define('THEME_INFO', '#17a2b8');              // Cyan for info

// Font families
define('FONT_PRIMARY', '"Segoe UI", Tahoma, Geneva, Verdana, sans-serif');
define('FONT_MONO', '"Courier New", Courier, monospace');

// Get current theme from user preference or session
function getCurrentTheme() {
    if (isset($_SESSION['theme'])) {
        return $_SESSION['theme'];
    }
    // Default to light mode
    return 'light';
}

// Set theme
function setTheme($theme) {
    if (in_array($theme, ['light', 'dark'])) {
        $_SESSION['theme'] = $theme;
        return true;
    }
    return false;
}

// Get theme color variables
function getThemeColors($theme = null) {
    if ($theme === null) {
        $theme = getCurrentTheme();
    }
    
    if ($theme === 'dark') {
        return [
            'bg_primary' => THEME_PRIMARY_DARK,
            'bg_secondary' => '#252541',
            'text_primary' => THEME_TEXT_LIGHT,
            'text_secondary' => '#b0b0b0',
            'border' => THEME_BORDER_LIGHT,
            'accent' => THEME_ACCENT_BLUE,
            'accent_alt' => THEME_ACCENT_ORANGE,
            'card_bg' => '#1f1f3a'
        ];
    } else {
        return [
            'bg_primary' => THEME_PRIMARY_LIGHT,
            'bg_secondary' => '#ffffff',
            'text_primary' => THEME_TEXT_DARK,
            'text_secondary' => '#666666',
            'border' => THEME_BORDER_DARK,
            'accent' => THEME_ACCENT_BLUE,
            'accent_alt' => THEME_ACCENT_ORANGE,
            'card_bg' => '#ffffff'
        ];
    }
}

// Generate CSS variables for current theme
function generateThemeCSS($theme = null) {
    $colors = getThemeColors($theme);
    $css = ":root {\n";
    foreach ($colors as $key => $value) {
        $css .= "    --color-" . str_replace('_', '-', $key) . ": " . $value . ";\n";
    }
    $css .= "    --font-primary: " . FONT_PRIMARY . ";\n";
    $css .= "    --font-mono: " . FONT_MONO . ";\n";
    $css .= "    --color-success: " . THEME_SUCCESS . ";\n";
    $css .= "    --color-danger: " . THEME_DANGER . ";\n";
    $css .= "    --color-warning: " . THEME_WARNING . ";\n";
    $css .= "    --color-info: " . THEME_INFO . ";\n";
    $css .= "}\n";
    return $css;
}
?>
