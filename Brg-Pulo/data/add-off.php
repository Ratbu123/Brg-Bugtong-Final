<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$successMsg = "";
$errorMsg = "";
$uploadError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("<div class='text-red-600 font-bold'>Connection failed: " . $conn->connect_error . "</div>");
    }

    // Sanitize and validate inputs
    $fname      = $conn->real_escape_string($_POST['fname']);
    $mname      = $conn->real_escape_string($_POST['mname']);
    $lname      = $conn->real_escape_string($_POST['lname']);
    $number     = $conn->real_escape_string($_POST['number']);
    $position   = $conn->real_escape_string($_POST['position']);
    $age        = (int)$_POST['age'];
    $dob        = $conn->real_escape_string($_POST['dateofbirth']);
    $address    = $conn->real_escape_string($_POST['address']);
    $email      = $conn->real_escape_string($_POST['email']);
    $passwordPlain = $_POST['password'];

    // Hash the password
    $passwordHashed = password_hash($passwordPlain, PASSWORD_BCRYPT);

    // Check for duplicate email
    $check = $conn->query("SELECT id FROM `b-official` WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $errorMsg = "Email already exists. Please use a different email.";
    } else {
        // Image upload logic
        $targetDir = __DIR__ . "/uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

        $pictureName = basename($_FILES["picture"]["name"]);
        $imageFileType = strtolower(pathinfo($pictureName, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];

        if (empty($pictureName)) {
            $uploadError = "Please upload a profile image.";
        } elseif (!getimagesize($_FILES["picture"]["tmp_name"])) {
            $uploadError = "Uploaded file is not a valid image.";
        } elseif (!in_array($imageFileType, $allowedTypes)) {
            $uploadError = "Only JPG, JPEG, PNG & GIF formats are allowed.";
        } else {
            $newFileName = uniqid("official_", true) . '.' . $imageFileType;
            $targetFile = $targetDir . $newFileName;
            if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                $uploadError = "Failed to upload image.";
            }
        }

        if ($uploadError === "") {
            $picturePathForDB = "data/uploads/" . $newFileName;

            $sql = "INSERT INTO `b-official` (
                        fname, mname, lname, number, position,
                        age, dateofbirth, address, picture, email, password
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssissssss",
                $fname, $mname, $lname, $number, $position,
                $age, $dob, $address, $picturePathForDB, $email, $passwordHashed
            );

            if ($stmt->execute()) {
                echo "<script>
                    alert('Barangay official added successfully!');
                    window.location.href = 'admin.php#dashboard';
                </script>";
                exit;
            } else {
                $errorMsg = "Database error: " . $stmt->error;
            }
            $stmt->close();
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Barangay Official</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">

  <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-blue-700 mb-6">➕ Add Barangay Official</h2>

    <?php if (!empty($successMsg)): ?>
      <div class="bg-green-100 text-green-800 p-4 rounded mb-4"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if (!empty($uploadError)): ?>
      <div class="bg-yellow-100 text-yellow-800 p-4 rounded mb-4"><?= htmlspecialchars($uploadError) ?></div>
    <?php endif; ?>
    <?php if (!empty($errorMsg)): ?>
      <div class="bg-red-100 text-red-800 p-4 rounded mb-4"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <input type="text" name="fname" placeholder="First Name" required class="p-2 border rounded w-full">
      <input type="text" name="mname" placeholder="Middle Name" required class="p-2 border rounded w-full">
      <input type="text" name="lname" placeholder="Last Name" required class="p-2 border rounded w-full">
      <input type="text" name="number" placeholder="Contact Number" required class="p-2 border rounded w-full">
      <input type="text" name="position" placeholder="Position" required class="p-2 border rounded w-full">
      <input type="number" name="age" placeholder="Age" required class="p-2 border rounded w-full min-w-0">
      <input type="date" name="dateofbirth" placeholder="Date of Birth" required class="p-2 border rounded w-full">
      <input type="text" name="address" placeholder="Address" required class="p-2 border rounded w-full">

      <div class="col-span-1 md:col-span-2">
        <label class="block mb-1 text-sm font-medium text-gray-700">Upload Profile Picture</label>
        <input type="file" name="picture" accept="image/*" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded cursor-pointer bg-gray-50">
      </div>

      <input type="email" name="email" placeholder="Email" required class="p-2 border rounded w-full">
      <input type="password" name="password" placeholder="Password" required class="p-2 border rounded w-full">

      <div class="col-span-1 md:col-span-2">
        <button type="submit" class="w-full bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 transition">
          ✅ Add Official
        </button>
      </div>
    </form>
  </div>

</body>
</html>
