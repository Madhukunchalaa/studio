
// Modal Logic
function openModal() {
    const modal = document.getElementById('projectModal');
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
}

function closeModal() {
    const modal = document.getElementById('projectModal');
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    }
}

// Close on outside click
window.onclick = function(event) {
    const modal = document.getElementById('projectModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Close on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Conditional Form Logic
document.addEventListener('DOMContentLoaded', function() {
    const projectTypeSelect = document.getElementById('project_type_select');
    const projectDetailsGroup = document.getElementById('project_details_group');

    if (projectTypeSelect && projectDetailsGroup) {
        projectTypeSelect.addEventListener('change', function() {
            if (this.value) {
                projectDetailsGroup.style.display = 'block';
                // Trigger a small fade-in if desired, or just show it
                projectDetailsGroup.style.opacity = 0;
                setTimeout(() => {
                    projectDetailsGroup.style.transition = 'opacity 0.5s ease';
                    projectDetailsGroup.style.opacity = 1;
                }, 10);
            } else {
                projectDetailsGroup.style.display = 'none';
            }
        });
    }
});
