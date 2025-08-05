<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Certificate Request Handling
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'], $_POST['date'], $_POST['certificate'], $_POST['email'])) {
    $name = htmlspecialchars($_POST['name']);
    $raw_date = $_POST['date'];
    $date = date("F j, Y", strtotime($raw_date));
    $certificate_type = htmlspecialchars($_POST['certificate']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $status = "Pending";

    $stmt = $conn->prepare("INSERT INTO request (name, date, status, certificate_type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $raw_date, $status, $certificate_type);

    if ($stmt->execute()) {
        $dompdf = new Dompdf();
        $html = "
            <style>
                body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; }
                h2 { text-align: center; margin-bottom: 10px; }
                .info { margin: 30px 0; }
                .footer { margin-top: 50px; text-align: right; }
            </style>
            <h2>Republic of the Philippines<br>Province of Batangas<br>City of Lipa<br><strong>Barangay Bugtong na Pulo</strong></h2>
            <hr>
            <p>This is to certify that <strong>{$name}</strong> has requested for a <strong>{$certificate_type}</strong> on <strong>{$date}</strong>.</p>
            <div class='info'>This certification is issued upon request for any legal purpose it may serve.</div>
            <div class='footer'>
                <p><strong>HENEROSO M. BADILLO</strong><br>Punong Barangay</p>
            </div>
        ";
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = "certificate_" . time() . ".pdf";
        $folder = __DIR__ . "/generated";
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        $pdfPath = "$folder/$filename";
        file_put_contents($pdfPath, $dompdf->output());

        if ($email) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'trstnjorge@gmail.com';
                $mail->Password = 'kape qhjm zgyv skzb';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('trstnjorge@gmail.com', 'Barangay Bugtong na Pulo');
                $mail->addAddress($email, $name);
                $mail->addAttachment($pdfPath);
                $mail->isHTML(true);
                $mail->Subject = "Certificate Request Confirmation - $certificate_type";
                $mail->Body = "Dear $name,<br>Your request for <strong>$certificate_type</strong> has been received. Please find your confirmation attached.<br><br>Thank you.<br><strong>Barangay Bugtong na Pulo</strong>";

                $mail->send();
                $_SESSION['success'] = true;
                header("Location: " . $_SERVER['PHP_SELF'] . "#services");
                exit;
            } catch (Exception $e) {
                $message = "<div class='bg-yellow-100 text-yellow-800 p-3 rounded'>⚠️ Email failed: {$mail->ErrorInfo}</div>";
            }
        } else {
            $_SESSION['success'] = true;
            header("Location: " . $_SERVER['PHP_SELF'] . "#services");
            exit;
        }
    } else {
        $message = "<div class='bg-red-100 text-red-800 p-3 rounded'>❌ Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Contact Form Handling
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['send_contact'])) {
    $contact_name = htmlspecialchars(trim($_POST['contact_name']));
    $contact_email = filter_var($_POST['contact_email'], FILTER_VALIDATE_EMAIL);
    $contact_message = htmlspecialchars(trim($_POST['contact_message']));

    if ($contact_name && $contact_email && $contact_message) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'trstnjorge@gmail.com';
            $mail->Password = 'kape qhjm zgyv skzb';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('trstnjorge@gmail.com', 'Contact Form - Barangay Website');
            $mail->addAddress('barangay@bpulo.gov');

            $mail->isHTML(true);
            $mail->Subject = "📬 Message from Contact Form";
            $mail->Body = "
                <h3>Contact Form Message</h3>
                <p><strong>Name:</strong> {$contact_name}</p>
                <p><strong>Email:</strong> {$contact_email}</p>
                <p><strong>Message:</strong><br>{$contact_message}</p>
            ";

            $mail->send();
            $_SESSION['success'] = true;
            header("Location: " . $_SERVER['PHP_SELF'] . "#contact");
            exit;
        } catch (Exception $e) {
            $message = "<div class='bg-yellow-100 text-yellow-800 p-3 rounded'>⚠️ Email failed: {$mail->ErrorInfo}</div>";
        }
    } else {
        $message = "<div class='bg-red-100 text-red-800 p-3 rounded'>❌ Please fill in all fields correctly.</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Brgy. Bugtong na Pulo</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">

<!-- Navbar -->
<header class="bg-blue-900 text-white p-4 sticky top-0 shadow z-50">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
    <h1 class="text-xl font-bold">Brgy. Bugtong na Pulo</h1>
    <nav class="space-x-4">
      <a href="#home" class="hover:underline">Home</a>
      <a href="#officials" class="hover:underline">Officials</a>
      <a href="#services" class="hover:underline">Services</a>
      <a href="#contact" class="hover:underline">Contact</a>
    </nav>
  </div>
</header>

<!-- Hero -->
<section id="home" class="bg-cover bg-center h-96 flex items-center justify-center text-white text-3xl font-bold" style="background-image: url('./images/BrgHall.png')">
  Maligayang Araw mga KABARANGGAY!
</section>

<!-- Barangay Officials Carousel -->
<section id="officials" class="max-w-6xl mx-auto py-10 px-4">
  <h2 class="text-2xl font-bold mb-6 text-blue-800 text-center">👥 Barangay Officials</h2>

  <div 
    x-data="{
      activeIndex: 0,
      paused: false,
      perSlide: 3,
      officials: [
        { name: 'HENEROSO M. BADILLO', position: 'Punong Barangay', email: 'bugtongnapulo08@gmail.com', contact: '0437236761', photo: 'heneroso.jpg' },
        { name: 'RONALDO M. BAUTISTA', position: 'Sangguniang Barangay Member', email: 'bugtongnpulo08@gmail.com', contact: '0437236761', photo: 'ronaldo.jpg' },
        { name: 'MICHAEL L. DIAZ', position: 'Sangguniang Barangay Member', email: 'bugtongnpulo08@gmail.com', contact: '0437236761', photo: 'michael.jpg' },
        { name: 'LAGRIMAS C. LAT', position: 'Sangguniang Barangay Member', email: 'bugtongnpulo08@gmail.com', contact: '0437236761', photo: 'lagrimas.jpg' },
        { name: 'JOEY T. VITERO', position: 'Sangguniang Barangay Member', email: 'bugtongnpulo08@gmail.com', contact: '0437236761', photo: 'joey.jpg' },
        { name: 'ORLANDO O. PUNZALAN', position: 'Sangguniang Barangay Member', email: 'bugtongnpulo08@gmail.com', contact: '0437236761', photo: 'orlando.jpg' },
        { name: 'ANGELITO A. MARQUEZ', position: 'Sangguniang Barangay Member', email: 'bugtongnpulo08@gmail.com', contact: '0437236761', photo: 'angelito.jpg' },
        { name: 'AUREA D. RECINTO', position: 'Sangguniang Barangay Member', email: 'bugtongnpulo08@gmail.com', contact: '0437236761', photo: 'aurea.jpg' },
        { name: 'IVAN H. LLANES', position: 'SK Chairperson', email: 'ivanllanes930@gmail.com', contact: '043-723-6761', photo: 'ivan.jpg' },
        { name: 'MINA P. TAPALLA', position: 'Barangay Secretary', email: 'mimi_tapalla@yahoo.com', contact: '043 723-6761', photo: 'mina.jpg' }
      ],
      get totalSlides() {
        return Math.ceil(this.officials.length / this.perSlide);
      },
      get slideWidth() {
        return 100 * this.totalSlides;
      },
      init() {
        setInterval(() => {
          if (!this.paused) {
            this.activeIndex = (this.activeIndex + 1) % this.totalSlides;
          }
        }, 4000);
      },
      slideStyle() {
        return `transform: translateX(-${this.activeIndex * (100 / this.totalSlides)}%); width: ${this.slideWidth}%;`;
      }
    }"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    class="relative"
  >

    <!-- Slides -->
    <div class="overflow-hidden">
      <div class="flex transition-transform duration-500 ease-in-out" :style="slideStyle()">
        <template x-for="slideIndex in totalSlides" :key="slideIndex">
          <div class="w-full flex md:w-full px-4 space-x-4">
            <template x-for="i in perSlide">
              <template x-if="officials[(slideIndex - 1) * perSlide + i - 1]">
                <div class="w-1/3 bg-white p-4 rounded shadow border-l-4 border-blue-700 text-center">
                  <img :src="'./images/officials/' + officials[(slideIndex - 1) * perSlide + i - 1].photo" alt="Photo"
                       class="w-24 h-24 rounded-full mx-auto mb-2 object-cover border-2 border-blue-700">
                  <h3 class="font-bold" x-text="officials[(slideIndex - 1) * perSlide + i - 1].name"></h3>
                  <p class="text-sm" x-text="officials[(slideIndex - 1) * perSlide + i - 1].position"></p>
                  <p class="text-sm">
                    📧 <a :href="'mailto:' + officials[(slideIndex - 1) * perSlide + i - 1].email" class="text-blue-600 hover:underline" x-text="officials[(slideIndex - 1) * perSlide + i - 1].email"></a>
                  </p>
                  <p class="text-sm">📱 <span x-text="officials[(slideIndex - 1) * perSlide + i - 1].contact"></span></p>
                </div>
              </template>
            </template>
          </div>
        </template>
      </div>
    </div>

    <!-- Dot Indicators -->
    <div class="flex justify-center mt-4 space-x-2">
      <template x-for="i in totalSlides" :key="i">
        <button @click="activeIndex = i - 1"
                class="w-3 h-3 rounded-full transition-all duration-300"
                :class="(i - 1) === activeIndex ? 'bg-blue-700 scale-125' : 'bg-gray-400'">
        </button>
      </template>
    </div>

  </div>
</section>


<!-- Services -->
<section id="services" class="bg-white py-10 px-4">
  <div class="max-w-3xl mx-auto text-center">
    <h2 class="text-2xl font-bold mb-4">📝 Services</h2>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="bg-blue-700 text-white px-4 py-2 rounded">Request Certificate</button>
  </div>
</section>

<!-- Contact -->
<section id="contact" class="max-w-6xl mx-auto py-10 px-4">
  <h2 class="text-2xl font-bold mb-4">📬 Contact Us</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <ul class="space-y-2">
      <li>📘 facebook.com/buqtpnp</li>
      <li>📧 barangay@bpulo.gov</li>
      <li>📱 0927-123-4567</li>
    </ul>
    <form class="space-y-4" method="POST" action="#contact">
      <input type="text" name="contact_name" placeholder="Your Name" class="w-full p-2 border rounded" required>
      <input type="email" name="contact_email" placeholder="Your Email" class="w-full p-2 border rounded" required>
      <textarea name="contact_message" placeholder="Your Message" class="w-full p-2 border rounded" required></textarea>
      <button type="submit" name="send_contact" class="bg-blue-700 text-white px-4 py-2 rounded">Send</button>
    </form>
  </div>
</section>

<!-- Request Certificate Modal -->
<div id="formModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 <?= isset($_SESSION['success']) || !empty($message) ? '' : 'hidden' ?>">
  <div class="bg-white p-6 rounded-lg w-full max-w-md">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Request Certificate</h2>
      <button onclick="document.getElementById('formModal').classList.add('hidden')" class="text-gray-500 text-2xl">&times;</button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
      <div id="flash-message" class="bg-green-100 text-green-800 p-3 rounded mb-3">✅ Request submitted successfully!</div>
      <?php unset($_SESSION['success']); ?>
    <?php elseif (!empty($message)): ?>
      <div id="flash-message" class="mb-3"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-2">
        <label class="block text-sm font-medium">Full Name</label>
        <input type="text" name="name" required class="w-full p-2 border rounded">
      </div>
      <div class="mb-2">
        <label class="block text-sm font-medium">Email Address</label>
        <input type="email" name="email" required class="w-full p-2 border rounded">
      </div>
      <div class="mb-2">
        <label class="block text-sm font-medium">Certificate Type</label>
        <select name="certificate" required class="w-full p-2 border rounded">
          <option value="Certificate of Indigency">Certificate of Indigency</option>
          <option value="Certificate of Residency">Certificate of Residency</option>
        </select>
      </div>
      <input type="hidden" name="date" value="<?= date('Y-m-d') ?>">
      <button type="submit" class="bg-blue-700 text-white px-4 py-2 rounded w-full">Submit</button>
    </form>
  </div>
</div>


<!-- Footer -->
<footer class="bg-blue-900 text-white text-center p-4 mt-10">
  &copy; <?= date('Y') ?> Barangay Bugtong na Pulo. All rights reserved.
</footer>

<!-- Flash Message Auto-hide -->
<script>
  setTimeout(() => {
    const msg = document.getElementById('flash-message');
    if (msg) msg.remove();
  }, 3000);
</script>

</body>
</html>
