// Theme toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('theme-toggle');
        const body = document.body;
        
        // Check for saved theme preference or use preferred color scheme
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            body.classList.add('dark-theme');
        }
        
        // Toggle theme when button is clicked
        themeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            body.classList.toggle('dark-theme');
            
            // Save the current theme to localStorage
            const isDark = body.classList.contains('dark-theme');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Update the icon
            const icon = this.querySelector('i');
            if (isDark) {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });
        
        // Update the icon based on current theme
        if (body.classList.contains('dark-theme')) {
            const icon = themeToggle.querySelector('i');
            if (icon) {
               icon.classList.remove('fa-moon');
               icon.classList.add('fa-sun');
            }
        }
    });