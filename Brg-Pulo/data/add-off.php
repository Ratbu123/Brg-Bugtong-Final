<?php
// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "brg-pulo";

    $conn = new mysqli($host, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("<div class='error'>Connection failed: " . $conn->connect_error . "</div>");
    }

    // Sanitize inputs
    $fname = $conn->real_escape_string($_POST['fname']);
    $mname = $conn->real_escape_string($_POST['mname']);
    $lname = $conn->real_escape_string($_POST['lname']);
    $number = $conn->real_escape_string($_POST['number']);
    $barangay = $conn->real_escape_string($_POST['barangay']);
    $position = $conn->real_escape_string($_POST['position']);
    $age = (int)$_POST['age'];
    $dateofbirth = $conn->real_escape_string($_POST['dateofbirth']);
    $address = $conn->real_escape_string($_POST['address']);
    $email = $conn->real_escape_string($_POST['email']);
    $passwordPlain = $conn->real_escape_string($_POST['password']); // Store plain password

    // Upload directory (absolute path)
    $targetDir = __DIR__ . "/uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $pictureName = basename($_FILES["picture"]["name"]);
    $targetFile = $targetDir . $pictureName;

    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ["jpg", "jpeg", "png", "gif"];

    $uploadError = "";
    if (empty($_FILES["picture"]["name"])) {
        $uploadError = "Please upload an image file.";
    } else {
        $check = getimagesize($_FILES["picture"]["tmp_name"]);
        if ($check === false) {
            $uploadError = "File is not a valid image.";
        } elseif (!in_array($imageFileType, $allowedTypes)) {
            $uploadError = "Only JPG, JPEG, PNG & GIF files are allowed.";
        } elseif (!move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
            $uploadError = "Error uploading the image.";
        }
    }

    if ($uploadError === "") {
        // Path saved to DB (relative URL for HTML)
        $picturePathForDB = "data/uploads/" . $pictureName;

        $sql = "INSERT INTO `b-official` (
                    fname, mname, lname, number, barangay, position,
                    age, dateofbirth, address, picture, email, password
                ) VALUES (
                    '$fname', '$mname', '$lname', '$number', '$barangay', '$position',
                    $age, '$dateofbirth', '$address', '$picturePathForDB', '$email', '$passwordPlain'
                )";

        if ($conn->query($sql) === TRUE) {
            // Redirect to admin.php after successfully adding the official
            header("Location: /brg-pulo/admin.php");
            exit; // Make sure to stop further script execution after redirect
        } else {
            $errorMsg = "Database error: " . $conn->error;
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Official</title>

    <!-- Link to the CSS file -->
    <link rel="stylesheet" href="/styles/styles.css"> <!-- Or adjust the path as needed -->
</head>
<body>

<?php if (!empty($successMsg)): ?>
    <div class="message success"><?= htmlspecialchars($successMsg) ?></div>
<?php endif; ?>

<?php if (!empty($uploadError)): ?>
    <div class="message error"><?= htmlspecialchars($uploadError) ?></div>
<?php endif; ?>

<?php if (!empty($errorMsg)): ?>
    <div class="message error"><?= htmlspecialchars($errorMsg) ?></div>
<?php endif; ?>

<form action="data/add-off.php" method="POST" enctype="multipart/form-data" class="add-off-form">
    <input type="text" name="fname" placeholder="First Name" required>
    <input type="text" name="mname" placeholder="Middle Name" required>
    <input type="text" name="lname" placeholder="Last Name" required>
    <input type="text" name="number" placeholder="Contact Number" required>
    <input type="text" name="barangay" placeholder="Barangay" required>
    <input type="text" name="position" placeholder="Position" required>
    <input type="number" name="age" placeholder="Age" required min="1">
    <input type="date" name="dateofbirth" placeholder="Date of Birth" required>
    <input type="text" name="address" placeholder="Address" required>
    <input type="file" name="picture" accept="image/*" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Add Official</button>
</form>

</body>
</html>