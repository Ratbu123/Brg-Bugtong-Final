<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "brg-pulo";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM `res-info` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>
        document.body.innerHTML = '<h2 style=\"text-align:center;\">Deleting... Please wait</h2>';
        setTimeout(() => window.location.href = 'admin.php', 1500);
    </script>";
    exit();
}

// Load residents by purok
$sql = "SELECT * FROM `res-info` ORDER BY purok, lname, fname";
$result = $conn->query($sql);

$residents = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $purok = $row['purok'];
        if (!isset($residents[$purok])) {
            $residents[$purok] = [];
        }
        $residents[$purok][] = $row;
    }
}
?>

<link rel="stylesheet" href="/BRG-PULO/styles/styles.css">

<div class="container">
    <h1>Populations by Purok</h1>
    <div class="barangay-list">
        <?php foreach ($residents as $purok => $people): ?>
            <div class="barangay-box">
                <h2><?= htmlspecialchars($purok) ?></h2>
                <p>Total Residents: <?= count($people) ?></p>
                <button onclick="togglePurok('purok_<?= $purok ?>')">View Residents</button>
            </div>

            <div id="purok_<?= $purok ?>" class="resident-table" style="display: none; margin-bottom: 40px;">
                <table>
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Age</th>
                            <th>DOB</th>
                            <th>Status</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($people as $person): ?>
                            <tr>
                                <td><img src="<?= $person['profile'] ?: 'images/sub/usericon.png' ?>" width="40" height="40" style="border-radius: 50%; object-fit: cover;"></td>
                                <td><?= htmlspecialchars("{$person['fname']} {$person['mname']} {$person['lname']}") ?></td>
                                <td><?= htmlspecialchars($person['age']) ?></td>
                                <td><?= htmlspecialchars($person['dob']) ?></td>
                                <td><?= htmlspecialchars($person['c-status']) ?></td>
                                <td><?= htmlspecialchars($person['number']) ?></td>
                                <td>
                                    <a href="?delete=<?= $person['id'] ?>" onclick="return confirm('Delete this resident?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function togglePurok(id) {
    const table = document.getElementById(id);
    table.style.display = table.style.display === "none" ? "block" : "none";
}
</script>
