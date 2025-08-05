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
$name = $date_of_request = $certificate_type = "";

if ($id > 0) {
  $stmt = $conn->prepare("SELECT name, date, certificate_type FROM request WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $date_of_request = date("jS \of F, Y", strtotime($row['date']));
    $certificate_type = $row['certificate_type'];
  }

  $stmt->close();
}

$conn->close();

$clean_name = htmlspecialchars($name ?: '[Name]');
$clean_type = ucwords(strtolower($certificate_type ?: 'Certificate'));
$is_indigency = (strtolower($certificate_type) === 'certificate of indigency');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($clean_type) ?> - Barangay Certificate</title>
  <style>
    body {
      font-family: 'Times New Roman', serif;
      background-color: #fff;
      color: #000;
      padding: 40px;
    }

    .certificate-container {
      max-width: 800px;
      margin: auto;
      border: 3px solid #333;
      padding: 50px;
      background: #fefefe;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
    }

    .certificate-container img {
      width: 90px;
      margin: 0 auto 10px;
      display: block;
    }

    .text-center {
      text-align: center;
    }

    .title h2 {
      font-size: 24px;
      font-weight: bold;
      text-decoration: underline;
      margin: 30px 0 20px;
    }

    .content p {
      font-size: 16px;
      text-align: justify;
      margin-bottom: 15px;
      line-height: 1.8;
    }

    .footer {
      margin-top: 60px;
      text-align: right;
    }

    .footer p {
      margin-bottom: 5px;
    }

    @media print {
      #printBtn {
        display: none;
      }

      body {
        margin: 0;
      }

      .certificate-container {
        border: none;
        padding: 0;
        box-shadow: none;
      }
    }

    #printBtn {
      position: fixed;
      top: 20px;
      right: 30px;
      background: #2563eb;
      color: #fff;
      border: none;
      padding: 10px 16px;
      border-radius: 6px;
      font-size: 14px;
      cursor: pointer;
      z-index: 1000;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    #printBtn:hover {
      background-color: #1d4ed8;
    }
  </style>
</head>
<body>

<!-- Print Button -->
<?php if ($id > 0 && $name): ?>
  <button id="printBtn" onclick="window.print()">🖨️ Print / Download</button>

  <div class="certificate-container">
    <div class="text-center">
      <img src="../images/LipaLogo.png" alt="Barangay Seal">
      <h1>Republic of the Philippines</h1>
      <h2>Province of Batangas</h2>
      <h2>City of Lipa</h2>
      <h2><strong>BARANGAY BUGTONG NA PULO</strong></h2>
    </div>

    <div class="title text-center">
      <h2><?= strtoupper($clean_type) ?></h2>
    </div>

    <div class="content">
      <p><strong>TO WHOM IT MAY CONCERN:</strong></p>
      <p>
        This is to certify that <strong><?= $clean_name ?></strong> is a bonafide resident of Barangay Bugtong na Pulo, City of Lipa, Province of Batangas.
      </p>

      <p>
        <?php if ($is_indigency): ?>
          Based on the records and verification of this barangay, the above-named person belongs to an indigent family with limited financial means. This certification is issued upon their request to support the application for any government or private assistance related to medical, educational, or social services, and for whatever legal purpose it may serve.
        <?php else: ?>
          This certification is issued to confirm that <strong><?= $clean_name ?></strong> resides in Barangay Bugtong na Pulo, City of Lipa, and may be used for any legal purpose it may serve.
        <?php endif; ?>
      </p>

      <p>
        Issued this <strong><?= $date_of_request ?: '[Date not provided]' ?></strong> at Barangay Bugtong na Pulo, City of Lipa, Province of Batangas, Philippines.
      </p>
    </div>

    <div class="footer">
      <p>Certified Correct:</p><br><br>
      <p><strong>HENEROSO M. BADILLO</strong><br>Punong Barangay</p>
    </div>
  </div>
<?php else: ?>
  <div class="text-center text-red-600 text-lg mt-20">
    <p>❌ Invalid Certificate Request.</p>
    <p>Please go back and try again.</p>
  </div>
<?php endif; ?>

</body>
</html>
