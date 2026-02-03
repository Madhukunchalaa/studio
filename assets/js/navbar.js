/**
 * Navbar JavaScript
 * Handles navbar functionality including mobile menu
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNavbar);
    } else {
        initNavbar();
    }

    /**
     * Initialize navbar functionality
     */
    function initNavbar() {
        const menuToggle = document.querySelector('.menu-toggle');
        const mobileMenuCanvas = document.querySelector('.mobile-menu-canvas');
        const mobileOverlay = document.querySelector('.mobile-overlay');
        const mobileDropdowns = document.querySelectorAll('.mobile-nav-item.has-dropdown');
        const navbarContent = document.querySelector('.navbar-content');

        if (!menuToggle || !mobileMenuCanvas || !mobileOverlay) {
            console.warn('Navbar elements not found');
            return;
        }

        // Mobile menu toggle
        menuToggle.addEventListener('click', function() {
            menuToggle.classList.toggle('active');
            mobileMenuCanvas.classList.toggle('active');
            mobileOverlay.classList.toggle('active');
            document.body.style.overflow = mobileMenuCanvas.classList.contains('active') ? 'hidden' : '';
        });

        // Close menu when overlay is clicked
        mobileOverlay.addEventListener('click', function() {
            menuToggle.classList.remove('active');
            mobileMenuCanvas.classList.remove('active');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Mobile dropdown functionality
        mobileDropdowns.forEach(dropdown => {
            dropdown.addEventListener('click', function(e) {
                if (!e.target.classList.contains('mobile-dropdown-item')) {
                    dropdown.classList.toggle('active');
                }
            });
        });

        // Scroll-based background change
        if (navbarContent) {
            // Check initial scroll position
            function checkScroll() {
                if (window.scrollY > 50) {
                    navbarContent.classList.add('scrolled');
                } else {
                    navbarContent.classList.remove('scrolled');
                }
            }
            
            // Check on scroll
            window.addEventListener('scroll', checkScroll);
            
            // Check on page load (for pages that load with scroll position)
            checkScroll();
        }
    }
})();
