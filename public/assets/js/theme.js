// Theme Toggle Script

document.addEventListener('DOMContentLoaded', function () {
    const themeToggle = document.getElementById('themeToggle');

    // Init on load
    initializeTheme();

    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
});

function initializeTheme() {
    // Check if dark mode is enabled (from localStorage or server)
    const isDarkMode = localStorage.getItem('darkMode') === 'true';
    
    if (isDarkMode) {
        enableDarkMode();
    } else {
        disableDarkMode();
    }
}

function toggleTheme() {
    const isDarkMode = document.body.classList.contains('dark-mode');
    
    if (isDarkMode) {
        disableDarkMode();
    } else {
        enableDarkMode();
    }
}

function enableDarkMode() {
    document.body.classList.add('dark-mode');
    localStorage.setItem('darkMode', 'true');
    updateThemeToggleButton(true);
    sendThemeToServer('dark');
}

function disableDarkMode() {
    document.body.classList.remove('dark-mode');
    localStorage.setItem('darkMode', 'false');
    updateThemeToggleButton(false);
    sendThemeToServer('light');
}

function updateThemeToggleButton(isDark) {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.textContent = isDark ? 'Light mode' : 'Dark mode';
    }
}

function sendThemeToServer(theme) {
    // Optional: keep session in sync. If it fails, localStorage still works.
    const formData = new FormData();
    formData.append('action', 'set_theme');
    formData.append('theme', theme);

    fetch('/TimeForge_Capstone/includes/theme_handler.php', {
        method: 'POST',
        body: formData,
    }).catch(function () {
        // ignore
    });
}
