<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($conn->real_escape_string($_POST['name']));
    $date = $_POST['date'];
    $certificate_type = trim($conn->real_escape_string($_POST['certificate']));

    $stmt = $conn->prepare("INSERT INTO request (name, date, certificate_type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $date, $certificate_type);

    if ($stmt->execute()) {
        $message = "<p class='success-msg'>✅ Data has been inserted successfully!</p>";
    } else {
        $message = "<p class='error-msg'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Certificate Request Form</title>
  <link rel="stylesheet" href="../styles/fill.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9fafb;
      padding: 20px;
    }
    .form-container {
      max-width: 480px;
      margin: 0 auto;
      background: white;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 20px;
      background-color: #2563eb;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 5px;
      font-size: 16px;
      cursor: pointer;
    }
    .success-msg {
      background-color: #d1fae5;
      color: #065f46;
      padding: 10px;
      border-radius: 5px;
      margin-top: 10px;
    }
    .error-msg {
      background-color: #fee2e2;
      color: #991b1b;
      padding: 10px;
      border-radius: 5px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2>📝 Request a Certificate</h2>
    <?= $message ?>
    <form action="" method="POST" autocomplete="off">
      <label for="name">Full Name</label>
      <input type="text" id="name" name="name" required placeholder="e.g., Juan Dela Cruz" aria-label="Full Name">

      <label for="date">Date of Request</label>
      <input type="date" id="date" name="date" value="<?= date('Y-m-d'); ?>" readonly aria-readonly="true">

      <label for="certificate">Certificate Type</label>
      <select id="certificate" name="certificate" required>
        <option value="" disabled selected>-- Select Certificate --</option>
        <option value="Certificate of Indigency">Certificate of Indigency</option>
        <option value="Certificate of Residency">Certificate of Residency</option>
      </select>

      <button type="submit">📨 Submit Request</button>
    </form>
  </div>

</body>
</html>
