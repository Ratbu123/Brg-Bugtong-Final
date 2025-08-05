<?php
require __DIR__ . '/../vendor/autoload.php'; // Ensure Dompdf is loaded using __DIR__

use Dompdf\Dompdf;

$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$name = $date_of_request = $certificate_type = '';

if ($id > 0) {
    $stmt = $conn->prepare("SELECT name, date, certificate_type FROM request WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
        $date_of_request = date("j F Y", strtotime($row['date']));
        $certificate_type = strtolower($row['certificate_type']);
    }
    $stmt->close();
}
$conn->close();

// Certificate logic
$is_indigency = ($certificate_type === 'certificate of indigency');
$title = $is_indigency ? 'SERTIPIKO NG KAHIRAPAN' : 'SERTIPIKO NG PANINIRAHAN';
$logo_path = __DIR__ . '/../images/barangay_logo.png'; // Adjust logo path
$logo_base64 = '';

// Convert image to base64
if (file_exists($logo_path)) {
    $imageData = base64_encode(file_get_contents($logo_path));
    $logo_base64 = 'data:image/png;base64,' . $imageData;
}

// HTML content
$html = <<<HTML
<!DOCTYPE html>
<html lang="fil">
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: 'Times New Roman', serif; margin: 50px; }
    .certificate-container { border: 3px solid #000; padding: 40px; }
    .center { text-align: center; }
    .logo { width: 100px; margin-bottom: 10px; }
    .title { font-size: 22px; text-decoration: underline; font-weight: bold; margin: 20px 0; }
    p { font-size: 16px; text-align: justify; line-height: 1.6; margin-bottom: 10px; }
    .footer { margin-top: 50px; text-align: right; }
  </style>
</head>
<body>
  <div class="certificate-container">
    <div class="center">
      <img src="$logo_base64" class="logo" alt="Barangay Logo" />
      <h3>Republika ng Pilipinas</h3>
      <h4>Lalawigan ng Batangas</h4>
      <h4>Lungsod ng Lipa</h4>
      <h2><strong>BARANGAY BUGTONG NA PULO</strong></h2>
      <div class="title">$title</div>
    </div>

    <p><strong>SA SINUMANG KINAUUKULAN:</strong></p>
    <p>
      Ito ay nagpapatunay na si <strong>$name</strong> ay isang legal na naninirahan sa Barangay Bugtong na Pulo, Lipa City, Batangas.
    </p>
HTML;

if ($is_indigency) {
  $html .= <<<HTML
    <p>
      Siya ay kabilang sa pamilyang indigent at walang sapat na pagkakakitaan para sa pang-araw-araw na gastusin at pangangailangang medikal.
    </p>
    <p>
      Ang sertipikong ito ay inilalabas ngayong <strong>$date_of_request</strong> bilang patunay ng kanyang kalagayan at maaaring gamitin sa anumang legal na layunin, kabilang ang pagkuha ng tulong-medikal.
    </p>
HTML;
} else {
  $html .= <<<HTML
    <p>
      Ang sertipikong ito ay inilalabas ngayong <strong>$date_of_request</strong> bilang patunay ng kanyang paninirahan sa nasabing barangay at maaaring gamitin sa anumang legal na layunin.
    </p>
HTML;
}

$html .= <<<HTML
    <div class="footer">
      <p><strong>Pinagtibay ni:</strong></p><br><br>
      <p><strong>HENEROSO M. BADILLO</strong><br>Punong Barangay</p>
    </div>
  </div>
</body>
</html>
HTML;

// Render PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Barangay_Certificate_$name.pdf", ["Attachment" => false]); // Set to true to force download
exit;
