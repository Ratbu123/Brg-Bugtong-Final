// Toggle visibility of content sections in sidebar navigation
function showSection(id, event) {
    // Hide all content sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });

    // Remove active class from all nav links
    document.querySelectorAll('.sidebar nav a').forEach(link => {
        link.classList.remove('active');
    });

    // Show the selected section
    document.getElementById(id).style.display = 'block';

    // Highlight the active link
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
}

// Show the dashboard section by default when page loads
window.onload = () => {
    showSection('dashboard', { currentTarget: document.querySelector('.sidebar nav a.active') });
};

// Preview selected image before upload
function previewImage(event) {
    const input = event.target;
    const previewImage = document.getElementById('preview-image');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Modal functionality for "View All" buttons
document.querySelectorAll('.view-all-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const barangay = this.getAttribute('data-barangay');
        const modalId = 'modal-' + barangay.toLowerCase().replace(/\s+/g, '-');
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    });
});

// Close modal when the close (×) is clicked
document.querySelectorAll('.modal .close').forEach(closeBtn => {
    closeBtn.addEventListener('click', function () {
        this.closest('.modal').style.display = 'none';
    });
});

// Close modal if user clicks outside the modal content
window.addEventListener('click', function (event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});