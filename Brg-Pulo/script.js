// ========== Sidebar Navigation ========== //
function showSection(id, event) {
    // Hide all sections
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });

    // Remove 'active' from all links
    document.querySelectorAll('.sidebar nav a').forEach(link => {
        link.classList.remove('active');
    });

    // Show selected section
    const target = document.getElementById(id);
    if (target) {
        target.style.display = 'block';
    }

    // Set clicked link as active
    if (event?.currentTarget) {
        event.currentTarget.classList.add('active');
    }
}

// On page load, show dashboard section
window.addEventListener('DOMContentLoaded', () => {
    const defaultLink = document.querySelector('.sidebar nav a.active') || document.querySelector('.sidebar nav a');
    if (defaultLink) {
        showSection(defaultLink.getAttribute('data-section') || 'dashboard', { currentTarget: defaultLink });
    }
});

// ========== Image Preview Before Upload ========== //
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview-image');

    if (input.files?.[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            if (preview) preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// ========== View All Modal Functionality ========== //
document.querySelectorAll('.view-all-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const barangay = btn.getAttribute('data-barangay');
        const modalId = `modal-${barangay.toLowerCase().replace(/\s+/g, '-')}`;
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'block';
    });
});

// ========== Close Modal (× button) ========== //
document.querySelectorAll('.modal .close').forEach(closeBtn => {
    closeBtn.addEventListener('click', () => {
        const modal = closeBtn.closest('.modal');
        if (modal) modal.style.display = 'none';
    });
});

// ========== Close Modal on Outside Click ========== //
window.addEventListener('click', event => {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
});
