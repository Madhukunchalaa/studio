/**
 * About Page JavaScript
 * Specific functionality for about.html
 */

document.addEventListener('DOMContentLoaded', function () {
    initAboutAnimations();
    initTriReveal();
    initTypewriter('.about-team-note', '.about-team-note-text', 60);
    initTypewriter('.about-typewriter', '.about-typewriter-text', 50);
    initParticles();
});

// ========================================
// SCROLL ANIMATIONS
// ========================================

/**
 * Initialize Intersection Observer for scroll animations
 */
function initAboutAnimations() {
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('about-animated');

                // Determine animation based on element
                if (entry.target.classList.contains('about-hero-content')) {
                    entry.target.style.animation = 'about-fadeInUp 0.8s ease-out forwards';
                } else if (entry.target.parentElement &&
                    (entry.target.parentElement.classList.contains('about-solution-icons') ||
                        entry.target.parentElement.classList.contains('about-solution-text'))) {
                    entry.target.style.animation = 'about-scaleIn 0.6s ease-out forwards';
                } else if (entry.target.classList.contains('about-comparison-side')) {
                    const index = Array.from(entry.target.parentElement.children).indexOf(entry.target);
                    if (index === 0) {
                        entry.target.style.animation = 'about-slideInLeft 0.7s ease-out forwards';
                    } else if (index === 2) {
                        entry.target.style.animation = 'about-slideInRight 0.7s ease-out forwards';
                    }
                } else {
                    entry.target.style.animation = 'about-fadeInUp 0.8s ease-out forwards';
                }
            }
        });
    }, observerOptions);

    // Observe all elements with about-animate-on-scroll class
    const aboutAnimatedElements = document.querySelectorAll('.about-animate-on-scroll');
    aboutAnimatedElements.forEach(el => observer.observe(el));
}

// ========================================
// TRIANGLE CARDS SCROLL REVEAL
// ========================================

/**
 * Scroll-based triangle card reveal with scroll lock
 * Prevents scrolling past section until all 4 cards are revealed
 */
function initTriReveal() {
    const section = document.querySelector('.studio-x-section');
    const cards = Array.from(document.querySelectorAll('.studio-x-section .studio-x-tri'));
    if (!section || !cards.length) return;

    // If on mobile (<= 768px) show all cards and disable scroll-reveal behavior
    try {
        if (window.matchMedia && window.matchMedia('(max-width: 768px)').matches) {
            section.classList.add('studio-x-show-all');
            cards.forEach(card => card.classList.add('studio-x-tri-active'));
            return; // skip attaching wheel handlers and observers on mobile
        }
    } catch (e) {
        // If matchMedia isn't supported for some reason, fall back to default behavior
        console.warn('matchMedia check failed in initTriReveal:', e);
    }

    let currentIndex = 0;
    let isInView = false;
    let isLocked = false;
    let canScroll = true;
    const totalCards = cards.length;

    // Reset all cards
    const resetCards = () => {
        section.classList.remove('studio-x-show-all');
        cards.forEach(card => card.classList.remove('studio-x-tri-active'));
        currentIndex = 0;
        isLocked = false;
    };

    // Show card at specific index
    const showCard = (index) => {
        if (index >= 0 && index < cards.length) {
            cards[index].classList.add('studio-x-tri-active');
        }

        // If all cards are shown, unlock scrolling
        if (index >= cards.length - 1) {
            section.classList.add('studio-x-show-all');
            isLocked = false;
        }
    };

    // Hide card at specific index
    const hideCard = (index) => {
        if (index >= 0 && index < cards.length) {
            cards[index].classList.remove('studio-x-tri-active');
        }
        section.classList.remove('studio-x-show-all');
    };

    // Handle wheel event with scroll lock
    const handleWheel = (e) => {
        if (!isInView || !canScroll) return;

        // If section is locked and user tries to scroll down beyond last card
        if (isLocked && e.deltaY > 0 && currentIndex >= totalCards) {
            e.preventDefault();
            return;
        }

        canScroll = false;

        if (e.deltaY > 0) {
            // Scrolling down
            if (currentIndex < totalCards) {
                e.preventDefault();
                showCard(currentIndex);
                currentIndex++;
            }
        } else if (e.deltaY < 0) {
            // Scrolling up
            if (currentIndex > 0) {
                e.preventDefault();
                currentIndex--;
                hideCard(currentIndex);
            }
        }

        // Re-enable scrolling after delay
        setTimeout(() => {
            canScroll = true;
        }, 500);
    };

    // Intersection Observer to detect when section is in view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.target !== section) return;

            if (entry.isIntersecting) {
                isInView = true;
                isLocked = true;

                // Show first card immediately when section comes into view
                if (currentIndex === 0) {
                    showCard(0);
                    currentIndex = 1;
                }
            } else {
                isInView = false;

                // Reset if scrolled away before completing
                if (currentIndex < totalCards) {
                    resetCards();
                }
            }
        });
    }, {
        threshold: 0.4,
        rootMargin: '0px'
    });

    observer.observe(section);

    // Listen to wheel events with preventDefault capability
    window.addEventListener('wheel', handleWheel, { passive: false });

    // Initial setup
    resetCards();
}

// ========================================
// TYPEWRITER EFFECT
// ========================================

/**
 * Typewriter effect for text elements


// ========================================
// PARTICLE ANIMATION
// ========================================

/**
 * Initialize particle animation
 */
function initParticles() {
    const particlesContainer = document.getElementById('particles');
    if (!particlesContainer) return;

    const particleCount = 50;

    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('light-particle');

        // Random position
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDuration = (Math.random() * 15 + 10) + 's';
        particle.style.animationDelay = Math.random() * 5 + 's';

        // Random size variation
        const size = Math.random() * 3 + 1;
        particle.style.width = size + 'px';
        particle.style.height = size + 'px';

        particlesContainer.appendChild(particle);
    }
}