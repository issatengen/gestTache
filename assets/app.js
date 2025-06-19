import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
    // Toggle sidebar with proper mobile handling
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Check if we're on mobile
    function isMobile() {
        return window.matchMedia("(max-width: 768px)").matches;
    }
    
    // Toggle sidebar function
    function toggleSidebar() {
        if (isMobile()) {
            // On mobile, we want to overlay the sidebar
            sidebar.classList.toggle('mobile-show');
            sidebar.classList.remove('mobile-hidden');
            content.classList.toggle('mobile-pushed');
            // Show overlay when sidebar is open
            document.querySelector('.sidebar-overlay').style.display = sidebar.classList.contains('mobile-show') ? 'block' : 'none';
        } else {
            // On desktop, we want to push content
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        }
    }

    function initSidebar() {
        if (isMobile()) {
            sidebar.classList.add('mobile-hidden');
            sidebar.classList.remove('mobile-show');
            content.classList.remove('collapsed');
            content.classList.remove('mobile-pushed');
            document.querySelector('.sidebar-overlay').style.display = 'none';
        } else {
            sidebar.classList.remove('mobile-hidden', 'mobile-show');
            content.classList.remove('mobile-pushed');
        }
    }
    // Set up event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);

    // Sidebar overlay click closes sidebar on mobile
    document.querySelector('.sidebar-overlay').addEventListener('click', function() {
        if (isMobile() && sidebar.classList.contains('mobile-show')) {
            sidebar.classList.remove('mobile-show');
            content.classList.remove('mobile-pushed');
            this.style.display = 'none';
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        initSidebar();
    });
    // Set up event listeners
    sidebarToggle.addEventListener('click', toggleSidebar);
    
    // Handle window resize
    window.addEventListener('resize', function() {
        initSidebar();
    });
    
    // Initialize on load
    initSidebar();
    
    // Make sidebar links active when clicked
    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('#sidebar .nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // On mobile, close sidebar after clicking a link
            if (isMobile()) {
                toggleSidebar();
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.matches('.dropdown-toggle') && !event.target.closest('.dropdown-menu')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
            });
        }
    });
});
