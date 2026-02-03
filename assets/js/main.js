

  // alert('hello')
// ========================================
// INTERACTIVE SERVICES SECTION
// ========================================

document.addEventListener('DOMContentLoaded', function() {
  // Get all service items and video wrappers
  const serviceItems = document.querySelectorAll('.service-item');
  const videoWrappers = document.querySelectorAll('.video-wrapper');
  const emptyState = document.getElementById('emptyState');
  
  // Track current playing video
  let currentVideo = null;

  // Function to handle service selection
  function selectService(serviceName, serviceElement) {
    // Update active state on menu items
    serviceItems.forEach(item => item.classList.remove('active'));
    serviceElement.classList.add('active');

    // Hide empty state
    if (emptyState) {
      emptyState.style.display = 'none';
    }

    // Stop current video if exists
    if (currentVideo) {
      currentVideo.pause();
      currentVideo.currentTime = 0;
    }

    // Show selected video
    videoWrappers.forEach(wrapper => {
      if (wrapper.getAttribute('data-video') === serviceName) {
        wrapper.classList.add('active');
        const video = wrapper.querySelector('video');
        
        // Play video with error handling
        if (video) {
          video.play().catch(error => {
            console.log('Video autoplay prevented:', error);
            // You can add a play button overlay here if needed
          });
          currentVideo = video;
        }
      } else {
        wrapper.classList.remove('active');
      }
    });
  }

  // Add click event listeners to service items
  serviceItems.forEach(item => {
    item.addEventListener('click', function() {
      const serviceName = this.getAttribute('data-service');
      selectService(serviceName, this);
    });

    // Add keyboard accessibility
    item.addEventListener('keypress', function(e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        const serviceName = this.getAttribute('data-service');
        selectService(serviceName, this);
      }
    });

    // Make items keyboard accessible
    item.setAttribute('tabindex', '0');
    item.setAttribute('role', 'button');
  });

  // Optional: Auto-select first service on load
  // Uncomment the following lines if you want the first service to be active by default
  /*
  setTimeout(() => {
    if (serviceItems.length > 0) {
      const firstService = serviceItems[0].getAttribute('data-service');
      selectService(firstService, serviceItems[0]);
    }
  }, 500);
  */

  // Handle video play/pause on visibility change
  document.addEventListener('visibilitychange', function() {
    if (document.hidden && currentVideo) {
      currentVideo.pause();
    } else if (!document.hidden && currentVideo) {
      currentVideo.play().catch(error => {
        console.log('Video play prevented:', error);
      });
    }
  });
});

// ========================================
// SMOOTH SCROLL FOR INTERNAL LINKS
// ========================================

document.addEventListener('DOMContentLoaded', function() {
  // Select all links with hashes
  const links = document.querySelectorAll('a[href^="#"]');
  
  links.forEach(link => {
    link.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      
      // Don't prevent default if it's just "#"
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
});

// ========================================
// INTERSECTION OBSERVER FOR ANIMATIONS
// ========================================

document.addEventListener('DOMContentLoaded', function() {
  // Observe feature rows for fade-in animations
  const featureRows = document.querySelectorAll('.feature-row');
  
  const observerOptions = {
    threshold: 0.2,
    rootMargin: '0px 0px -100px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
      }
    });
  }, observerOptions);

  featureRows.forEach(row => {
    row.style.opacity = '0';
    row.style.transform = 'translateY(30px)';
    row.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
    observer.observe(row);
  });
});

// ========================================
// VIDEO LAZY LOADING
// ========================================

document.addEventListener('DOMContentLoaded', function() {
  // Lazy load videos that are not immediately visible
  const videos = document.querySelectorAll('video[data-src]');
  
  const videoObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const video = entry.target;
        const source = video.querySelector('source');
        
        if (source && source.dataset.src) {
          source.src = source.dataset.src;
          video.load();
          videoObserver.unobserve(video);
        }
      }
    });
  });

  videos.forEach(video => {
    videoObserver.observe(video);
  });
});

// ========================================
// UTILITY FUNCTIONS
// ========================================

// Function to pause all videos
function pauseAllVideos() {
  const videos = document.querySelectorAll('video');
  videos.forEach(video => {
    video.pause();
  });
}

// Function to check if element is in viewport
function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

// Export functions for use in other scripts if needed
window.VisionXUtils = {
  pauseAllVideos,
  isInViewport
};


document.querySelectorAll('.video-card').forEach(card => {
      const video = card.querySelector('video');
      
      card.addEventListener('mouseenter', () => {
        video.play();
      });
      
      card.addEventListener('mouseleave', () => {
        video.pause();
        video.currentTime = 0;
      });

      // Click to play/pause
      card.addEventListener('click', () => {
        if (video.paused) {
          video.play();
        } else {
          video.pause();
        }
      });
    });

    // Contact button action
    const contactBtn = document.querySelector('.contact-btn');
    if (contactBtn) {
      contactBtn.addEventListener('click', () => {
        // Add your contact form/page navigation here
        alert('Contact form would open here!');
      });
    }




    // image gallery
     const images = document.querySelectorAll('.gallery-image');
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        const modalImage = document.getElementById('modalImage');
        const modalCaption = document.getElementById('modalCaption');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        let currentIndex = 0;
        let imageData = [];

        images.forEach((figure, index) => {
            const img = figure.querySelector('img');
            const caption = figure.querySelector('figcaption').textContent;
            
            imageData.push({
                src: img.src,
                caption: caption
            });

            figure.addEventListener('click', () => {
                currentIndex = index;
                showImage(currentIndex);
                modal.show();
            });
        });

        function showImage(index) {
            modalImage.src = imageData[index].src;
            modalCaption.textContent = imageData[index].caption;
        }

        prevBtn.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + imageData.length) % imageData.length;
            showImage(currentIndex);
        });

        nextBtn.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % imageData.length;
            showImage(currentIndex);
        });

        document.addEventListener('keydown', (e) => {
            if (document.querySelector('.modal.show')) {
                if (e.key === 'ArrowLeft') {
                    prevBtn.click();
                } else if (e.key === 'ArrowRight') {
                    nextBtn.click();
                }
            }
        });

  // Navbar scroll effect
function initNavbarScroll() {
  const navbar = document.getElementById("navbar");
  
  if (navbar) {
    window.addEventListener("scroll", function () {
      if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
      } else {
        navbar.classList.remove("scrolled");
      }
    });
  }
}

// Wait for header to load, then initialize
setTimeout(initNavbarScroll, 100);