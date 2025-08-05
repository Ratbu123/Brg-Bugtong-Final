<?php
require __DIR__ . '/vendor/autoload.php'; // Adjust path if needed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['contact_name']));
    $email = filter_var($_POST['contact_email'], FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars(trim($_POST['contact_message']));

    if ($name && $email && $message) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'trstnjorge@gmail.com'; // your sender email
            $mail->Password = 'kape qhjm zgyv skzb';   // your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('trstnjorge@gmail.com', 'Brgy. Bugtong na Pulo Website');
            $mail->addAddress('barangay@bpulo.gov'); // recipient (e.g., barangay email)

            $mail->isHTML(true);
            $mail->Subject = "📬 New Message from Contact Form";
            $mail->Body = "
                <h2>New Message from Contact Form</h2>
                <p><strong>Name:</strong> {$name}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Message:</strong><br>{$message}</p>
            ";

            $mail->send();
            header("Location: index.php#contact?success=1");
        } catch (Exception $e) {
            echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ All fields are required.";
    }
}
