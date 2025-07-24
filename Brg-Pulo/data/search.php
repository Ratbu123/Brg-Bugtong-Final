<?php
// residents.php

$host = "localhost";
$username = "root";
$password = "";
$database = "brg-pulo";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fixed: removed 'barangay' column which doesn't exist
$sql = "SELECT * FROM `res-info` ORDER BY purok, lname, fname";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$residents = [];
$puroks = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
        if (!in_array($row['purok'], $puroks)) {
            $puroks[] = $row['purok'];
        }
    }
}

sort($puroks); // optional
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Directory</title>
    <link rel="stylesheet" href="/BRG-PULO/styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Fallback style in case your CSS is not applied -->
    <style>
        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .filter-container select,
        .filter-container input {
            padding: 8px;
            font-size: 16px;
        }

        #resident-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .resident-item {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            padding: 12px;
            border-radius: 8px;
            background-color: #f9f9f9;
            gap: 15px;
        }

        .resident-item img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
        }

        .resident-item .info {
            flex: 1;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Resident Directory</h1>

    <!-- Filter Section -->
    <div class="filter-container">
        <select id="purok-filter" onchange="filterResidents()">
            <option value="all">All Puroks</option>
            <?php foreach ($puroks as $purok): ?>
                <option value="<?= htmlspecialchars(strtolower($purok)) ?>"><?= htmlspecialchars($purok) ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" id="search-input" placeholder="Search for a resident..." onkeyup="filterResidents()">
    </div>

    <!-- Resident List Section -->
    <div id="resident-list">
        <?php foreach ($residents as $resident): ?>
            <div class="resident-item" 
                 data-name="<?= strtolower($resident['fname'] . ' ' . $resident['mname'] . ' ' . $resident['lname']) ?>" 
                 data-purok="<?= strtolower($resident['purok']) ?>">
                <img src="<?= htmlspecialchars($resident['profile'] ?: '/BRG-PULO-REV/images/sub/usericon.png') ?>" alt="Profile">
                <div class="info">
                    <strong><?= htmlspecialchars($resident['fname']) ?> <?= htmlspecialchars($resident['mname']) ?> <?= htmlspecialchars($resident['lname']) ?></strong><br>
                    Age: <?= htmlspecialchars($resident['age']) ?> | Status: <?= htmlspecialchars($resident['c-status']) ?><br>
                    Contact: <?= htmlspecialchars($resident['number']) ?> | Purok: <?= htmlspecialchars($resident['purok']) ?><br>
                    DOB: <?= htmlspecialchars($resident['dob']) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function filterResidents() {
        var searchInput = document.getElementById('search-input').value.toLowerCase();
        var selectedPurok = document.getElementById('purok-filter').value.toLowerCase();
        var residents = document.querySelectorAll('.resident-item');

        residents.forEach(function(resident) {
            var name = resident.getAttribute('data-name');
            var purok = resident.getAttribute('data-purok');

            var matchesName = name.includes(searchInput);
            var matchesPurok = (selectedPurok === 'all') || (purok === selectedPurok);

            resident.style.display = (matchesName && matchesPurok) ? 'flex' : 'none';
        });
    }
</script>

</body>
</html>
