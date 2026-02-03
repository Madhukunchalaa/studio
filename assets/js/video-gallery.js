// Video Gallery & Playback Modal Logic

document.addEventListener('DOMContentLoaded', () => {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const videoModal = document.getElementById('video-modal');
    const modalVideoPlayer = document.getElementById('modal-video-player');
    
    // Check if close button exists, if not finding it dynamically might be safer
    const closeBtn = document.querySelector('.close-video-modal');

    // Function to open modal
    function openVideoModal(videoSrc) {
        if (!videoModal || !modalVideoPlayer) return;
        
        // Update source
        modalVideoPlayer.src = videoSrc;
        
        // Show modal
        videoModal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent background scroll
        
        // Play video
        modalVideoPlayer.currentTime = 0;
        modalVideoPlayer.play().catch(e => console.log('Autoplay prevented:', e));
    }

    // Function to close modal - Attach to window for global access from HTML onclick
    window.closeVideoModal = function() {
        if (!videoModal || !modalVideoPlayer) return;
        
        // Hide modal
        videoModal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scroll
        
        // Pause and reset
        modalVideoPlayer.pause();
        modalVideoPlayer.src = "";
    };

    // Attach click listeners to gallery items
    galleryItems.forEach(item => {
        item.addEventListener('click', (e) => {
            // Find the video source within the clicked item
            const video = item.querySelector('video');
            if (video && video.src) {
                openVideoModal(video.src);
            }
        });
    });

    // Close on overlay click
    if (videoModal) {
        videoModal.addEventListener('click', (e) => {
            if (e.target === videoModal) {
                closeVideoModal();
            }
        });
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && videoModal && videoModal.classList.contains('active')) {
            closeVideoModal();
        }
    });
});
