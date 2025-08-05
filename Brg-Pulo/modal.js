// modal.js

// Function to open the modal and load the residents
function openModal(barangay) {
    try {
        const residents = JSON.parse(document.getElementById('residentData').textContent);
        const puroks = residents[barangay] || {};

        document.getElementById('barangay-name').textContent = barangay;

        let residentHtml = '';
        for (const purok in puroks) {
            residentHtml += `
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-blue-800 mb-2">${purok}</h3>
                    <ul class="space-y-2">
            `;
            puroks[purok].forEach(resident => {
                residentHtml += `
                    <li class="flex items-center bg-white rounded-lg shadow p-4">
                        <img src="${resident.profile || '../images/sub/usericon.png'}" alt="Profile" class="w-12 h-12 rounded-full mr-4 border">
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">${resident.lname}, ${resident.fname} ${resident.mname}</p>
                            <p class="text-gray-700">Age: ${resident.age} | Status: ${resident['c-status']}</p>
                            <p class="text-gray-600">📱 ${resident.number}</p>
                        </div>
                    </li>
                `;
            });
            residentHtml += `</ul></div>`;
        }

        document.getElementById('resident-list').innerHTML = residentHtml;
        document.getElementById('modal').classList.remove('hidden');
    } catch (error) {
        console.error("Error loading resident data:", error);
        document.getElementById('resident-list').innerHTML = `<p class="text-red-600">⚠️ Failed to load resident data.</p>`;
    }
}

// Function to close the modal
function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}
