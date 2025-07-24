<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$name = "";
$date_of_request = "";
$certificate_type = "";

// Fetch the data for the specified ID
if ($id > 0) {
  $stmt = $conn->prepare("SELECT name, date, certificate_type FROM request WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $date_of_request = date("jS \of F, Y", strtotime($row['date']));
    $certificate_type = $row['certificate_type'];  // Get the certificate type
  }

  $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?php echo ucfirst($certificate_type); ?> Certificate</title>
  <link rel="stylesheet" href="../styles/certificate.css">
</head>
<body>

  <!-- Certificate Content -->
  <div class="certificate-container">
    <div class="header">
      <img src="../images/OfficialSeal.png" alt="Barangay Seal">
      <h1>Republic of the Philippines</h1>
      <h2>Province of Lanao del Sur</h2>
      <h2>Marawi City</h2>
      <h2>BARANGAY TOROS</h2>
    </div>

    <div class="title">
      <h2><u>
        <?php
          if ($certificate_type == 'Certificate of indigency') {
            echo "CERTIFICATE OF INDIGENCY";
          } else {
            echo "CERTIFICATE OF RESIDENCY";
          }
        ?>
      </u></h2>
    </div>

    <div class="content">
      <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
      <p>
        This is to certify that <strong><?php echo $name ? htmlspecialchars($name) : '[Blank]'; ?></strong>,
        <?php
          if ($certificate_type == 'Certificate of indigency') {
            echo "widow, 70 years old, Filipino citizen, is a resident of this barangay and belongs to an indigent family. She has no means of livelihood to support her medication and hospitalization needs.";
          } else {
            echo "is a legal resident of this barangay.";
          }
        ?>
      </p>
      <p>
        <?php
          if ($certificate_type == 'Certificate of indigency') {
            echo "This certification is issued as a requirement for medical assistance to be processed by her daughter <strong>RAYNIDAH M. DIPATUAN</strong>, and for whatever legal purpose it may serve her.";
          } else {
            echo "This certification is issued to confirm that <strong>" . htmlspecialchars($name) . "</strong> resides in Barangay Toros, Marawi City.";
          }
        ?>
      </p>
      <p>Issued this <strong><?php echo $date_of_request ? $date_of_request : '[Blank]'; ?></strong> at Barangay Toros, Marawi City, Philippines.</p>
    </div>

    <div class="footer">
      <div class="signature">
        <p>Certified Correct:</p><br><br>
        <p><strong>HON. NIDRALDEN A. USMAN</strong><br>Barangay Chairman</p>
      </div>
    </div>
  </div>

</body>
</html>
