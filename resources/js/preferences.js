// Apply theme preference
function applyTheme() {
    const theme = getCookie('theme') || 'light';
    const body = document.body;
    
    // Remove existing theme classes
    body.classList.remove('theme-light', 'theme-dark');
    
    // Apply selected theme
    if (theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        body.classList.add('theme-dark');
    } else {
        body.classList.add('theme-light');
    }
}

// Get cookie helper function
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

// Apply theme on page load
document.addEventListener('DOMContentLoaded', function() {
    applyTheme();
    
    // Listen for system theme changes if auto mode
    const theme = getCookie('theme');
    if (theme === 'auto') {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', applyTheme);
    }
});