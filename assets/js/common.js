/**
 * Common JavaScript Functions
 * Shared functionality across all pages
 */

// ========================================
// NAVBAR & FOOTER LOADING
// ========================================

/**
 * Load navbar into placeholder
 */
function loadNavbar() {
    const navbarPlaceholder = document.getElementById('navbar-placeholder');
    if (!navbarPlaceholder) return;

    fetch('navbar.html')
        .then(res => res.text())
        .then(data => {
            navbarPlaceholder.innerHTML = data;

            // Extract and execute any scripts from navbar
            const container = navbarPlaceholder;
            const scripts = container.querySelectorAll('script');
            scripts.forEach(script => {
                const newScript = document.createElement('script');
                newScript.textContent = script.textContent;
                document.body.appendChild(newScript);
            });

            // Load navbar.js after HTML is injected
            const navbarScript = document.createElement('script');
            navbarScript.src = 'assets/js/navbar.js';
            document.body.appendChild(navbarScript);
        })
        .catch(error => console.error('Error loading navbar:', error));
}

/**
 * Load footer into placeholder
 */
function loadFooter() {
    const footerPlaceholder = document.getElementById('footer-placeholder');
    if (!footerPlaceholder) return;

    fetch('footer.html')
        .then(res => res.text())
        .then(data => {
            footerPlaceholder.innerHTML = data;
        })
        .catch(error => console.error('Error loading footer:', error));
}

// ========================================
// NAVBAR SCROLL EFFECT
// ========================================

/**
 * Initialize navbar scroll effect
 */
function initNavbarScroll() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// ========================================
// SMOOTH SCROLL
// ========================================

/**
 * Initialize smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href === '#') return;

            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// ========================================
// GLOBAL MODAL LOGIC
// ========================================
function injectModal() {
    if (document.getElementById('serviceModal')) return;
    const modalHTML = `
<div id="serviceModal" class="studiox-modal">
  <div class="studiox-modal-content">
    <span class="studiox-close-btn" onclick="closeModal()">&times;</span>
    <h2 class="studiox-modal-title">Start Your Project</h2>
    <p class="studiox-modal-subtitle">Tell us about your vision. We'll get back to you within 24 hours.</p>
    <form class="studiox-modal-form" action="contact.php" method="POST">
      <div class="form-group"><label>Full Name*</label><input type="text" name="full_name" required placeholder="Your Name"></div>
      <div class="form-group"><label>Work Email*</label><input type="email" name="email" required placeholder="your.email@company.com"></div>
      <div class="form-group"><label>Mobile Number</label><input type="tel" name="mobile" placeholder="+1 234 567 890"></div>
      <div class="form-group"><label>Service Interest</label>
         <select name="goal"><option value="General Inquiry">General Inquiry</option><option value="Brand Films">Brand Films</option><option value="YouTube Growth">YouTube Growth</option><option value="AI Avatars">AI Avatars</option><option value="Learning Content">Learning Content</option></select>
      </div>
      <div class="form-group"><label>Project Details*</label><textarea name="message" required placeholder="Tell us what you're building..."></textarea></div>
      <button type="submit" class="studiox-modal-submit">Submit Request</button>
    </form>
  </div>
</div>`;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    // Add close info
    const modal = document.getElementById('serviceModal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === this) closeModal();
        });
    }
}

function openModal() {
    injectModal(); // Ensure it exists
    const modal = document.getElementById('serviceModal');
    if (modal) {
        setTimeout(() => {
            modal.classList.add('show');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }, 10);
    }
}

function closeModal() {
    const modal = document.getElementById('serviceModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

// Make functions global
window.openModal = openModal;
window.closeModal = closeModal;


// ========================================
// INITIALIZE ON DOM LOAD
// ========================================

document.addEventListener('DOMContentLoaded', function () {
    loadNavbar();
    loadFooter();
    initNavbarScroll();
    initSmoothScroll();
    initLiteYouTube();
    initLazyIframes();
    initLazyVideos();
    injectModal();
});

/**
 * Initialize Lite YouTube Embeds (Click-to-Load)
 */
function initLiteYouTube() {
    document.querySelectorAll('.lite-youtube').forEach(wrapper => {
        wrapper.addEventListener('click', function () {
            if (this.dataset.loaded) return;

            const videoId = this.dataset.videoId;
            if (!videoId) return;

            // Create iframe
            const iframe = document.createElement('iframe');

            // Copy optional params from data attributes or use defaults
            const params = this.dataset.params || 'autoplay=1&rel=0&modestbranding=1&iv_load_policy=3&controls=1';

            iframe.src = `https://www.youtube.com/embed/${videoId}?${params}`;
            iframe.width = "100%";
            iframe.height = "100%";
            iframe.frameBorder = "0";
            iframe.allow = "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
            iframe.allowFullscreen = true;

            // Clear facade and append iframe
            this.innerHTML = '';
            this.appendChild(iframe);
            this.dataset.loaded = 'true';
        });
    });
}

/**
 * Initialize Lazy Iframes (Scroll-to-Load)
 * Used for background videos or off-screen content
 */
function initLazyIframes() {
    const lazyIframes = document.querySelectorAll('.lazy-iframe');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const iframe = entry.target;
                    if (iframe.dataset.src) {
                        iframe.src = iframe.dataset.src;
                        // Optional: fade in
                        iframe.onload = () => {
                            iframe.style.opacity = 1;
                        };
                    }
                    observer.unobserve(iframe);
                }
            });
        }, { rootMargin: '200px' }); // Load when 200px away

        lazyIframes.forEach(iframe => {
            observer.observe(iframe);
        });
    } else {
        // Fallback for older browsers
        lazyIframes.forEach(iframe => {
            if (iframe.dataset.src) {
                iframe.src = iframe.dataset.src;
            }
        });
    }
}


/**
 * Initialize Lazy Videos (Local MP4s)
 * Looks for .lazy-video class and swaps data-src to src on <source> children
 */
function initLazyVideos() {
    const lazyVideos = document.querySelectorAll('.lazy-video');
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const video = entry.target;
                    const sources = video.querySelectorAll('source');
                    let changed = false;
                    sources.forEach(source => {
                        if (source.dataset.src) {
                            source.src = source.dataset.src;
                            changed = true;
                        }
                    });

                    if (changed) {
                        video.load();
                        // Explicitly handle autoplay if attribute exists
                        if (video.hasAttribute('autoplay')) {
                            var playPromise = video.play();
                            if (playPromise !== undefined) {
                                playPromise.catch(error => {
                                    // Auto-play was prevented
                                    console.log('Autoplay prevented:', error);
                                });
                            }
                        }
                    }
                    observer.unobserve(video);
                }
            });
        }, { rootMargin: '200px' });
        lazyVideos.forEach(v => observer.observe(v));
    }
}


