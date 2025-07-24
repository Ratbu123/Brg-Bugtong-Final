<?php
// data/res-info.php (clean, final, without Barangay select)

$host = "localhost";
$username = "root";
$password = "";
$database = "brg-pulo";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mname = $_POST['mname'];
    $age = $_POST['age'];
    $number = $_POST['number'];
    $c_status = $_POST['c-status'];
    $dob = $_POST['dob'];
    $housen = $_POST['housen'];
    $purok = $_POST['purok'];

    $profile = '';
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["profile"]["name"]);
        if (move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file)) {
            $profile = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO `res-info` (fname, lname, mname, age, number, `c-status`, dob, profile, housen, purok) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $fname, $lname, $mname, $age, $number, $c_status, $dob, $profile, $housen, $purok);

    if ($stmt->execute()) {
        echo "<script>alert('Resident added successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Resident</title>
    <link rel="stylesheet" href="/BRG-PULO/styles/styles.css">
</head>
<body>

<div class="form-container">
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-header">
            <h2>Add Resident</h2>
        </div>

        <div class="form-grid">
            <div class="image-upload-section">
                <div id="preview-container">
                    <img id="preview-image" src="../images/sub/usericon.png" alt="Preview">
                </div>
                <input type="file" name="profile" accept="image/*" onchange="previewImage(event)">
            </div>

            <div class="form-fields-section">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
                <input type="text" name="mname" placeholder="Middle Name" required>
                <input type="number" name="age" placeholder="Age" required>
                <input type="text" name="number" placeholder="Contact Number" required>

                <label>Civil Status:</label>
                <select name="c-status" required>
                    <option value="">Select Status</option>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Widowed">Widowed</option>
                    <option value="Separated">Separated</option>
                </select>

                <label>Date of Birth:</label>
                <input type="date" name="dob" required>

                <input type="text" name="housen" placeholder="House Number" required>

                <label>Street:</label>
                <select name="purok" required>
                    <option value="">Select Street</option>
                    <option value="Areca Palm">Areca Palm</option>
                    <option value="Bamboo Palm">Bamboo Palm</option>
                    <option value="California Palm">California Palm</option>
                    <option value="Chinese Palm">Chinese Palm</option>
                    <option value="Christmas Palm">Christmans Palm</option>
                    <option value="King Sago">King Sago</option>
                    <option value="Lady Palm">Lady Palm</option>
                    <option value="Majesty Palm">Majesty Palm</option>
                    <option value="Olive Street">Olive Street</option>
                    <option value="Palmdale Street">Palmdale Street</option>
                    <option value="Periwinkle Loop">Periwinkle Loop</option>
                </select>

                <button type="submit">Add Resident</button>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(event) {
    const input = event.target;
    const previewImage = document.getElementById('preview-image');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>