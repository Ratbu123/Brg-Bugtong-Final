<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $date = $_POST['date']; // Comes from the readonly field
    $certificate_type = $_POST['certificate']; // Added this field for certificate type

    $stmt = $conn->prepare("INSERT INTO request (name, date, certificate_type) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $date, $certificate_type); // Adjust the bind_param to include the certificate type

    if ($stmt->execute()) {
        $message = "<p class='success-msg'>Data has been inserted successfully!</p>";
    } else {
        $message = "<p class='error-msg'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Insert Data</title>
  <link rel="stylesheet" href="../styles/fill.css">
</head>
<body>

  <div class="form-container">
    <h2>Insert Data for Certificate</h2>
    <?php echo $message; ?>
    <form action="" method="POST">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" required>

      <label for="date">Date of Request</label>
      <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" readonly>

      <label for="certificate">Certificate Type</label>
      <select id="certificate" name="certificate" required>
        <option value="Certificate of indigency">Certificate of Indigency</option>
        <option value="Certificate of residency">Certificate of Residency</option>
      </select>

      <button type="submit">Submit</button>
    </form>
  </div>

</body>
</html>
