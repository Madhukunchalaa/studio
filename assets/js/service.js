/**
 * Service Page JavaScript
 * Specific functionality for service.html
 */

document.addEventListener('DOMContentLoaded', function () {
    initServiceMorph();
    initServiceCTARedirects();
    initLazyVideos();
});

// ========================================
// LAZY VIDEO LOADING
// ========================================
function initLazyVideos() {
    var lazyVideos = [].slice.call(document.querySelectorAll("video.lazy-video"));

    if ("IntersectionObserver" in window) {
        var lazyVideoObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (video) {
                if (video.isIntersecting) {
                    for (var source in video.target.children) {
                        var videoSource = video.target.children[source];
                        if (typeof videoSource.tagName === "string" && videoSource.tagName === "SOURCE") {
                            videoSource.src = videoSource.dataset.src;
                        }
                    }

                    // Directly set src if video tag has data-src (fallback/alternative)
                    if (video.target.dataset.src) {
                        video.target.src = video.target.dataset.src;
                    }

                    video.target.load();
                    video.target.classList.remove("lazy-video");
                    video.target.play().catch(e => console.log('Autoplay prevented:', e));
                    lazyVideoObserver.unobserve(video.target);
                }
            });
        });

        lazyVideos.forEach(function (lazyVideo) {
            lazyVideoObserver.observe(lazyVideo);
        });
    }
}

// ========================================
// SERVICE MORPH ANIMATION
// ========================================

/**
 * Service data for morph animation
 */
const svcMorphServices = [
    {
        title: 'Strategic Brand Films',
        bestFor: 'Product Launches | Brand Awareness | High-Performance Ads',
        timeline: '5-7 days',
        description: 'Cinematic storytelling that converts. We craft emotionally resonant films that define your market position and drive measurable business outcomes.'
    },
    {
        title: 'YouTube Growth Engine',
        bestFor: 'Channel Growth | Audience Engagement | Authority Building',
        timeline: '3-5 days',
        description: 'Algorithm-optimized content that builds audiences. We engineer videos for the YouTube algorithm without sacrificing soul—every element designed for maximum retention and subscriber growth.'
    },
    {
        title: 'Global AI Avatar Solutions',
        bestFor: 'Multi-Market Campaigns | Training & Onboarding | Product Demos',
        timeline: '2-4 days',
        description: 'Scale your message across 60+ languages. Deploy a consistent, on-brand digital presenter globally in days, not months—perfect for scaling content across markets without reshoots or travel.'
    },
    {
        title: 'Transformative Learning Content',
        bestFor: 'E-Learning | Employee Training | Educational Courses',
        timeline: '5-7 days',
        description: 'Learning videos that actually stick. We transform complex information into engaging, retention-focused visual stories built for clarity, retention, and behavior change.'
    }
];

/**
 * Initialize service morph functionality
 */
function initServiceMorph() {
    const svcMorphSelectors = document.querySelectorAll('.svc-morph-selector-card');
    const svcMorphContent = document.getElementById('svcMorphContent');
    const svcMorphDisplay = document.getElementById('svcMorphDisplay');
    const svcMorphIndicators = document.querySelectorAll('.svc-morph-dot');
    const svcMorphVideos = document.querySelectorAll('.svc-morph-video-bg');

    if (!svcMorphContent || !svcMorphDisplay) return;

    /**
     * Switch to a specific service
     * @param {number} index - Service index
     */
    function svcMorphToService(index) {
        const service = svcMorphServices[index];
        if (!service) return;

        svcMorphDisplay.classList.add('svc-morph-animating');

        svcMorphVideos.forEach((video, i) => {
            if (i === index) {
                // Ensure lazy loaded src is set if it hasn't been yet
                if (video.classList.contains('lazy-video')) {
                    if (video.dataset.src && !video.src) {
                        video.src = video.dataset.src;
                        video.load();
                    }
                    video.classList.remove('lazy-video');
                }

                video.classList.add('svc-morph-active');
                if (video.tagName === 'VIDEO') {
                    video.play().catch(e => console.log('Video play error:', e));
                }
            } else {
                video.classList.remove('svc-morph-active');
                if (video.tagName === 'VIDEO') {
                    video.pause();
                }
            }
        });

        setTimeout(() => {
            svcMorphContent.innerHTML = `
                <h3 class="svc-morph-title-main">${service.title}</h3>
                <div class="svc-morph-meta-grid">
                    <div class="svc-morph-meta-item">
                        <div class="svc-morph-meta-label">Best For</div>
                        <div class="svc-morph-meta-value">${service.bestFor}</div>
                    </div>
                    <div class="svc-morph-meta-item">
                        <div class="svc-morph-meta-label">Timeline</div>
                        <div class="svc-morph-meta-value">${service.timeline}</div>
                    </div>
                </div>
                <p class="svc-morph-description-text">${service.description}</p>
            `;
        }, 200);

        setTimeout(() => {
            svcMorphDisplay.classList.remove('svc-morph-animating');
        }, 800);

        svcMorphSelectors.forEach((sel, i) => {
            sel.classList.toggle('svc-morph-active', i === index);
        });

        svcMorphIndicators.forEach((ind, i) => {
            ind.classList.toggle('svc-morph-active', i === index);
        });
    }

    // Add click listeners to selector cards
    svcMorphSelectors.forEach((selector, index) => {
        selector.addEventListener('click', () => {
            svcMorphToService(index);
        });
    });

    // Initialize first video
    window.addEventListener('load', () => {
        const firstVideo = document.getElementById('svcMorphVideo0');
        if (firstVideo && firstVideo.tagName === 'VIDEO') {
            firstVideo.play().catch(e => console.log('Initial video autoplay prevented:', e));
        }
    });

    // Auto-rotate services
    let svcMorphCurrentIndex = 0;
    setInterval(() => {
        svcMorphCurrentIndex = (svcMorphCurrentIndex + 1) % svcMorphServices.length;
        svcMorphToService(svcMorphCurrentIndex);
    }, 5000);
}

/**
 * Redirect common CTA elements on the service page to the contact page
 */
function initServiceCTARedirects() {
    const contactHref = 'contact.html';

    // Explicit CTA selectors present in this page
    const ctaSelectors = [
        '.learning-visual-column button',
        '.learning-cta-box button',
        '.brand-story-panel .timeline-bar-brand'
    ];

    ctaSelectors.forEach(sel => {
        document.querySelectorAll(sel).forEach(el => {
            el.addEventListener('click', (e) => {
                // Prevent default for anchors/buttons and navigate to contact page
                if (e && typeof e.preventDefault === 'function') e.preventDefault();
                window.location.href = contactHref;
            });
        });
    });

    // Also ensure any anchor CTAs inside svc-morph-cta-section are pointing to contact
    document.querySelectorAll('.svc-morph-cta-section a').forEach(a => {
        if (a.getAttribute('href') !== contactHref) a.setAttribute('href', contactHref);
    });
}

