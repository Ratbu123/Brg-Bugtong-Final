<?php
session_start();
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "brg-pulo");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // Don't escape here, use raw for password_verify

    // 1. Try login from `b-official`
    $sql_official = "SELECT * FROM `b-official` WHERE email = '$username'";
    $result_official = $conn->query($sql_official);

    if ($result_official && $result_official->num_rows === 1) {
        $user = $result_official->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fname'] . ' ' . $user['lname'];
            $_SESSION['position'] = $user['position'];
            $_SESSION['user_picture'] = !empty($user['picture']) ? $user['picture'] : 'images/sub/usericon.png';
            $_SESSION['role'] = 'official';

            header("Location: off-admin.php"); // Redirect to official admin panel
            exit();
        }
    }

    // 2. Try login from `mainadmin`
    $sql_admin = "SELECT * FROM `mainadmin` WHERE username = '$username' AND password = '$password'";
    $result_admin = $conn->query($sql_admin);

    if ($result_admin && $result_admin->num_rows === 1) {
        $_SESSION['user_name'] = $username;
        $_SESSION['role'] = 'mainadmin';

        header("Location: admin.php"); // Redirect to main admin panel
        exit();
    } else {
        $loginError = "Invalid username or password.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Barangay Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="styles/Loginstyles.css">
</head>
<body>
  <div class="left-text">
    Barangay Bugtong<br>na Pulo<br>Management<br>System
  </div>

  <div class="login-box">
    <div class="logo-container">
      <img src="images/OfficialSeal.png" alt="SK Logo">
      <img src="images/LipaLogo.png" alt="Lipa Logo">
    </div>
    <h2>Administrator</h2>

    <?php if (!empty($loginError)): ?>
      <div class="message error" style="color: red; text-align: center; margin-bottom: 10px;">
        <?= htmlspecialchars($loginError) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Email or Username" required>
      </div>

      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <button class="btn" type="submit">
        <i class="fas fa-sign-in-alt"></i> Log In
      </button>
    </form>

    <div class="forgot-password">
      <a href="#">Forgot Password?</a>
    </div>
  </div>
</body>
</html>
