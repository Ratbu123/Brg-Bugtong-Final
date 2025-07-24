<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $salary = trim($_POST['salary']);

    if ($title && $description) {
        $stmt = $conn->prepare("INSERT INTO job_listings (title, description, salary) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $salary);

        if ($stmt->execute()) {
            $success = "Job posted successfully!";
        } else {
            $error = "Error posting job: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Post a Job</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f2f2f2;
      padding: 40px;
    }

    .container {
      background-color: #fff;
      padding: 25px;
      max-width: 950px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }

    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    textarea {
      resize: vertical;
      min-height: 100px;
    }

    .btn {
      margin-top: 20px;
      background-color: #007bff;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      font-size: 1rem;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    .message {
      margin-top: 20px;
      text-align: center;
      font-weight: bold;
    }

    .message.success {
      color: green;
    }

    .message.error {
      color: red;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Post a Job Listing</h2>

    <?php if ($success): ?>
      <div class="message success"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="message error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <label for="title">Job Title*</label>
      <input type="text" name="title" id="title" required>

      <label for="description">Job Description*</label>
      <textarea name="description" id="description" required></textarea>

      <label for="salary">Salary</label>
      <input type="text" name="salary" id="salary">

      <button class="btn" type="submit">Post Job</button>
    </form>
  </div>
</body>
</html>

<?php $conn->close(); ?>