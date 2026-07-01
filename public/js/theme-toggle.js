// CODE-CITE:
//   Title: Theme Toggle JavaScript Helper
//   Type: ai
//   Value: Antigravity Gemini
//   Notes: Handles persistence and toggling of light/dark theme settings.
document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtns = document.querySelectorAll('.theme-toggle-btn');
    
    const updateToggleButtons = (theme) => {
        themeToggleBtns.forEach(btn => {
            if (theme === 'light') {
                btn.innerHTML = '🌙'; // Icon to switch to dark mode
                btn.setAttribute('aria-label', 'Aktifkan Mode Gelap');
                btn.title = 'Aktifkan Mode Gelap';
            } else {
                btn.innerHTML = '☀️'; // Icon to switch to light mode
                btn.setAttribute('aria-label', 'Aktifkan Mode Terang');
                btn.title = 'Aktifkan Mode Terang';
            }
        });
    };

    // Set initial state of buttons
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    updateToggleButtons(currentTheme);

    themeToggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const theme = document.documentElement.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            updateToggleButtons(theme);
        });
    });
});
