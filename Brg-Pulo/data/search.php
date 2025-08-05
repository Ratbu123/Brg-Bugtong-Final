<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "brg-pulo";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM `res-info` ORDER BY purok, lname, fname";
$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

$residents = [];
$streets = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
        if (!in_array($row['purok'], $streets)) {
            $streets[] = $row['purok'];
        }
    }
}

sort($streets);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Directory</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 font-sans">

<div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Resident Directory</h1>

    <!-- Filter Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
        <select id="street-filter" onchange="filterResidents()"
                class="w-full sm:w-auto p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="all">All Streets</option>
            <?php foreach ($streets as $street): ?>
                <option value="<?= htmlspecialchars(strtolower($street)) ?>"><?= htmlspecialchars($street) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" id="search-input" placeholder="Search for a resident..."
               onkeyup="filterResidents()"
               class="w-full sm:w-64 p-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Residents List -->
    <div id="resident-list" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php foreach ($residents as $resident): ?>
            <?php
            $fullName = strtolower($resident['fname'] . ' ' . $resident['mname'] . ' ' . $resident['lname']);
            $street = strtolower($resident['purok']); // We now treat purok as street
            $image = htmlspecialchars(!empty($resident['profile']) ? $resident['profile'] : '../images/sub/usericon.png');
            ?>
            <div class="bg-white shadow rounded-lg flex p-4 gap-4 items-center resident-item"
                 data-name="<?= $fullName ?>" data-street="<?= $street ?>">
                <img src="<?= $image ?>" alt="Profile"
                     class="w-16 h-16 object-cover rounded-full border border-gray-300">
                <div class="flex-1 text-sm text-gray-700">
                    <p class="font-semibold text-lg text-gray-800">
                        <?= htmlspecialchars($resident['fname']) . ' ' . htmlspecialchars($resident['mname']) . ' ' . htmlspecialchars($resident['lname']) ?>
                    </p>
                    <p>Age: <?= htmlspecialchars($resident['age']) ?> | Status: <?= htmlspecialchars($resident['c-status']) ?></p>
                    <p>Contact: <?= htmlspecialchars($resident['number']) ?> | Street: <?= htmlspecialchars($resident['purok']) ?></p>
                    <p>DOB: <?= htmlspecialchars($resident['dob']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function filterResidents() {
        const searchInput = document.getElementById('search-input').value.toLowerCase();
        const selectedStreet = document.getElementById('street-filter').value.toLowerCase();
        const residents = document.querySelectorAll('.resident-item');

        residents.forEach(resident => {
            const name = resident.getAttribute('data-name');
            const street = resident.getAttribute('data-street');
            const matchesName = name.includes(searchInput);
            const matchesStreet = selectedStreet === 'all' || street === selectedStreet;
            resident.style.display = (matchesName && matchesStreet) ? 'flex' : 'none';
        });
    }
</script>

</body>
</html>
