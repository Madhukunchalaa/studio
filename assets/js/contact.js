/**
 * Contact Page JavaScript
 * Specific functionality for contact.html
 */

document.addEventListener('DOMContentLoaded', function () {
    initFAQ();
    initVideoControls();
    initFormSubmission();
});

// ========================================
// FAQ TOGGLE
// ========================================

/**
 * Toggle FAQ item
 * @param {HTMLElement} button - The button that triggered the toggle
 */
function toggleFAQ(button) {
    const faqItem = button.closest('.studio351-faq-item');
    if (!faqItem) return;

    const allItems = document.querySelectorAll('.studio351-faq-item');

    // Close all other items
    allItems.forEach(item => {
        if (item !== faqItem) {
            item.classList.remove('active');
        }
    });

    // Toggle current item
    faqItem.classList.toggle('active');
}

/**
 * Initialize FAQ functionality
 */
function initFAQ() {
    // Make toggleFAQ available globally for onclick handlers
    window.toggleFAQ = toggleFAQ;
}

// ========================================
// VIDEO CONTROLS
// ========================================

/**
 * Initialize video play/pause and mute/unmute controls
 */
function initVideoControls() {
    // Play/Pause functionality
    document.querySelectorAll('.play-pause-btn').forEach(button => {
        button.addEventListener('click', function () {
            const videoId = this.getAttribute('data-video');
            const video = document.getElementById(videoId);
            if (!video) return;

            const playIcon = this.querySelector('.play-icon');
            const pauseIcon = this.querySelector('.pause-icon');

            if (video.paused) {
                video.play().catch(error => {
                    console.log('Video play prevented:', error);
                });
                if (playIcon) playIcon.style.display = 'none';
                if (pauseIcon) pauseIcon.style.display = 'inline';
            } else {
                video.pause();
                if (playIcon) playIcon.style.display = 'inline';
                if (pauseIcon) pauseIcon.style.display = 'none';
            }
        });
    });

    // Mute/Unmute functionality
    document.querySelectorAll('.mute-unmute-btn').forEach(button => {
        button.addEventListener('click', function () {
            const videoId = this.getAttribute('data-video');
            const video = document.getElementById(videoId);
            if (!video) return;

            const unmuteIcon = this.querySelector('.unmute-icon');
            const muteIcon = this.querySelector('.mute-icon');

            if (video.muted) {
                video.muted = false;
                if (unmuteIcon) unmuteIcon.style.display = 'inline';
                if (muteIcon) muteIcon.style.display = 'none';
            } else {
                video.muted = true;
                if (unmuteIcon) unmuteIcon.style.display = 'none';
                if (muteIcon) muteIcon.style.display = 'inline';
            }
        });
    });
}

/**
 * Handle Contact Form Submission
 * Redirects to thank-you.html
 */
function initFormSubmission() {
    const submitBtn = document.querySelector('.studiox-contact-btn');
    if (submitBtn) {
        submitBtn.addEventListener('click', function (e) {
            // Validate that required fields are not empty
            const requiredInputs = document.querySelectorAll('.prj-text-field[required], .prj-message-field[required], .prj-choice-field[required]');
            let isValid = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#ff0055'; // Highlight error
                } else {
                    input.style.borderColor = 'rgba(255, 255, 255, 0.2)'; // Reset
                }
            });

            if (isValid) {
                // Redirect to thank-you page
                window.location.href = 'thank-you.html';
            } else {
                // Optional: Alert user or just let them see red borders
                alert('Please fill in all required fields.');
            }
        });
    }
}

