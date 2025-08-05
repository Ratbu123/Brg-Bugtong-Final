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
        document.body.innerHTML = '<h2 style=\"text-align:center; font-family:sans-serif;\">Deleting... Please wait</h2>';
        setTimeout(() => window.location.href = 'admin.php', 1500);
    </script>";
    exit();
}

// Load residents grouped by purok
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Residents by Purok</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col items-center justify-start p-8">

    <div class="w-full max-w-6xl">
        <h1 class="text-3xl font-extrabold text-center text-blue-800 mb-10">👥 Residents Grouped by Purok</h1>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($residents as $purok => $people): ?>
                <div class="bg-white shadow-lg rounded-lg p-5 border border-blue-100">
                    <h2 class="text-xl font-semibold text-gray-800 mb-1">Purok: <?= htmlspecialchars($purok) ?></h2>
                    <p class="mb-3 text-sm text-gray-600">Total Residents: <strong><?= count($people) ?></strong></p>
                    <button onclick="togglePurok('purok_<?= $purok ?>')" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                        📋 View Residents
                    </button>
                </div>

                <div id="purok_<?= $purok ?>" class="col-span-3 mt-4 hidden transition-all duration-300 ease-in-out">
                    <div class="overflow-x-auto bg-white border border-gray-200 shadow rounded-lg p-4">
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-100 text-gray-900 uppercase text-xs tracking-wider">
                                <tr>
                                    <th class="p-3">Profile</th>
                                    <th class="p-3">Name</th>
                                    <th class="p-3">Age</th>
                                    <th class="p-3">DOB</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Contact</th>
                                    <th class="p-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($people as $person): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="p-3">
                                            <img src="<?= htmlspecialchars($person['profile'] ?: './images/sub/usericon.png') ?>"
                                                 alt="Profile"
                                                 class="w-10 h-10 rounded-full object-cover border border-gray-300">
                                        </td>
                                        <td class="p-3"><?= htmlspecialchars("{$person['fname']} {$person['mname']} {$person['lname']}") ?></td>
                                        <td class="p-3"><?= htmlspecialchars($person['age']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($person['dob']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($person['c-status']) ?></td>
                                        <td class="p-3"><?= htmlspecialchars($person['number']) ?></td>
                                        <td class="p-3">
                                            <a href="?delete=<?= $person['id'] ?>"
                                               onclick="return confirm('Are you sure you want to delete this resident?')"
                                               class="inline-block bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                               🗑️ Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function togglePurok(id) {
            const el = document.getElementById(id);
            el.classList.toggle('hidden');
        }
    </script>

</body>
</html>
