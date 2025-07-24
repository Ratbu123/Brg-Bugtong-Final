// js/modal.js

// Function to open the modal and load the residents
function openModal(barangay) {
    // Get the residents data from PHP (using JSON encoding)
    const residents = JSON.parse('<?php echo json_encode($residents); ?>');
    const puroks = residents[barangay] || {};

    // Set barangay name
    document.getElementById('barangay-name').textContent = barangay;

    let residentHtml = '';
    for (const purok in puroks) {
        residentHtml += `<h3>${purok}</h3><ul>`;
        puroks[purok].forEach(resident => {
            residentHtml += `
                <li class="resident-card">
                    <img src="${resident.profile || '../images/sub/usericon.png'}" alt="Profile">
                    <div class="info">
                        <strong>${resident.lname}, ${resident.fname} ${resident.mname}</strong><br>
                        Age: ${resident.age} | Status: ${resident['c-status']}<br>
                        Contact: ${resident.number}
                    </div>
                </li>
            `;
        });
        residentHtml += '</ul>';
    }

    // Inject the resident list HTML into the modal
    document.getElementById('resident-list').innerHTML = residentHtml;

    // Display the modal
    document.getElementById('modal').style.display = 'flex';
}

// Function to close the modal
function closeModal() {
    document.getElementById('modal').style.display = 'none';
}