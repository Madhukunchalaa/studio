document.addEventListener('DOMContentLoaded', () => {
    // Initialize custom video controls for all showcase videos
    const videos = document.querySelectorAll('.showcase-video');
    videos.forEach(video => {
        new NativeVideoController(video);
    });
});

/**
 * Class to handle custom controls for native HTML5 videos
 */
class NativeVideoController {
    constructor(video) {
        this.video = video;
        this.id = video.id;
        this.container = video.closest('.video-card');

        // UI Elements
        this.playBtn = this.container.querySelector('.play-pause-btn');
        this.muteBtn = this.container.querySelector('.mute-btn');
        this.fullScreenBtn = this.container.querySelector('.fullscreen-btn');
        this.progressContainer = this.container.querySelector('.progress-container');
        this.progressBar = this.container.querySelector('.progress-bar');
        this.currentTimeEl = this.container.querySelector('.current-time');
        this.durationEl = this.container.querySelector('.duration');

        // Icons
        this.playIcon = this.playBtn.querySelector('.bi-play-fill');
        this.pauseIcon = this.playBtn.querySelector('.bi-pause-fill');
        this.muteIcon = this.muteBtn.querySelector('.bi-volume-mute-fill');
        this.unmuteIcon = this.muteBtn.querySelector('.bi-volume-up-fill');

        this.init();
    }

    init() {
        // Initial State
        this.video.muted = true; // Start muted
        this.updateMuteIcon();
        this.video.controls = false; // Hide native controls

        // Event Listeners
        this.playBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.togglePlay();
        });

        this.muteBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMute();
        });

        this.fullScreenBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleFullScreen();
        });

        this.progressContainer.addEventListener('click', (e) => {
            e.stopPropagation();
            this.seek(e);
        });

        // Video Events
        this.video.addEventListener('play', () => this.updatePlayIcon(true));
        this.video.addEventListener('pause', () => this.updatePlayIcon(false));
        this.video.addEventListener('timeupdate', () => this.updateProgress());
        this.video.addEventListener('loadedmetadata', () => {
            this.durationEl.textContent = this.formatTime(this.video.duration);
        });

        // Update initially incase metadata is already loaded
        if (this.video.readyState >= 1) {
            this.durationEl.textContent = this.formatTime(this.video.duration);
        }

        // Card Hover Play/Pause Logic (Optional - mimicking previous behavior if desired)
        // For now, we only use click on controls or specific interactions.
        // If we want hover-to-play like before:
        /*
        this.container.addEventListener('mouseenter', () => {
            this.video.play().catch(() => {});
        });
        this.container.addEventListener('mouseleave', () => {
            this.video.pause();
            this.video.currentTime = 0; // Reset on leave? Or just pause?
        });
        */
        // keeping it manual click for now as it's cleaner for full showcase.
    }

    togglePlay() {
        if (this.video.paused) {
            this.video.play().catch(e => console.log("Play failed:", e));
        } else {
            this.video.pause();
        }
    }

    updatePlayIcon(isPlaying) {
        if (isPlaying) {
            this.playIcon.style.display = 'none';
            this.pauseIcon.style.display = 'block';
            this.container.classList.add('playing');
        } else {
            this.playIcon.style.display = 'block';
            this.pauseIcon.style.display = 'none';
            this.container.classList.remove('playing');
        }
    }

    toggleMute() {
        this.video.muted = !this.video.muted;
        this.updateMuteIcon();
    }

    updateMuteIcon() {
        if (this.video.muted) {
            this.muteIcon.style.display = 'block';
            this.unmuteIcon.style.display = 'none';
        } else {
            this.muteIcon.style.display = 'none';
            this.unmuteIcon.style.display = 'block';
        }
    }

    toggleFullScreen() {
        if (!document.fullscreenElement) {
            if (this.video.requestFullscreen) {
                this.video.requestFullscreen();
            } else if (this.video.webkitRequestFullscreen) { /* Safari */
                this.video.webkitRequestFullscreen();
            } else if (this.video.msRequestFullscreen) { /* IE11 */
                this.video.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    seek(e) {
        const rect = this.progressContainer.getBoundingClientRect();
        const pos = (e.clientX - rect.left) / rect.width;
        this.video.currentTime = pos * this.video.duration;
    }

    updateProgress() {
        const percent = (this.video.currentTime / this.video.duration) * 100;
        this.progressBar.style.width = `${percent}%`;
        this.currentTimeEl.textContent = this.formatTime(this.video.currentTime);
    }

    formatTime(seconds) {
        if (!seconds) return "0:00";
        const m = Math.floor(seconds / 60);
        const s = Math.floor(seconds % 60);
        return `${m}:${s < 10 ? '0' : ''}${s}`;
    }
}
