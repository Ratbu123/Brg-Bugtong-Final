<?php
session_start();

// DB Connection
$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get logged-in official ID (from session)
$officialId = $_SESSION['official_id'] ?? null;
$official = null;

if ($officialId) {
    $stmt = $conn->prepare("SELECT email FROM `b-official` WHERE id = ?");
    $stmt->bind_param("i", $officialId);
    $stmt->execute();
    $result = $stmt->get_result();
    $official = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Official Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

  <div class="bg-white shadow-lg rounded-xl w-full max-w-sm p-6 text-center">
    <h2 class="text-2xl font-bold text-blue-700 mb-6">👤 Official Settings</h2>

    <?php if ($official): ?>
      <div class="flex flex-col items-center space-y-4">
        <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Icon" class="w-24 h-24 rounded-full border border-gray-300">
        <p class="text-lg font-semibold text-gray-800">Username (Email):</p>
        <p class="text-xl text-blue-700 font-bold"><?= htmlspecialchars($official['email']) ?></p>
      </div>
    <?php else: ?>
      <p class="text-red-600 font-semibold">⚠️ No official is currently logged in.</p>
    <?php endif; ?>
  </div>

</body>
</html>
