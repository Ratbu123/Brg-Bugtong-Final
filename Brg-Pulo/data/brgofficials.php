<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = $conn->query("DELETE FROM `b-official` WHERE id = $id");

    if ($delete) {
        echo "<script>alert('Official deleted successfully!'); window.location='brgofficials.php';</script>";
    } else {
        echo "Delete failed: " . $conn->error;
    }
    exit();
}

// UPDATE
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $fname = $_POST['fname'];
    $mname = $_POST['mname'];
    $lname = $_POST['lname'];
    $number = $_POST['number'];
    $barangay = $_POST['barangay'];
    $position = $_POST['position'];
    $age = $_POST['age'];
    $dob = $_POST['dateofbirth'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($_FILES['picture']['name']) {
        $target = "data/uploads/" . basename($_FILES['picture']['name']);
        move_uploaded_file($_FILES['picture']['tmp_name'], $target);
        $picture = $target;
        $sql = "UPDATE `b-official` SET fname='$fname', mname='$mname', lname='$lname', number='$number', barangay='$barangay', position='$position', age='$age', dateofbirth='$dob', address='$address', email='$email', password='$password', picture='$picture' WHERE id=$id";
    } else {
        $sql = "UPDATE `b-official` SET fname='$fname', mname='$mname', lname='$lname', number='$number', barangay='$barangay', position='$position', age='$age', dateofbirth='$dob', address='$address', email='$email', password='$password' WHERE id=$id";
    }

    if ($conn->query($sql)) {
        echo "<script>alert('Official updated successfully!'); window.location='brgofficials.php';</script>";
    } else {
        echo "Update failed: " . $conn->error;
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Barangay Officials</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your own external CSS file -->
</head>
<body>

<h2>Barangay Officials</h2>

<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>Picture</th>
        <th>Full Name</th>
        <th>Position</th>
        <th>Barangay</th>
        <th>Contact Number</th>
        <th>Age</th>
        <th>Date of Birth</th>
        <th>Address</th>
        <th>Email</th>
        <th>Password</th>
        <th>Actions</th>
    </tr>

    <?php
    $result = $conn->query("SELECT * FROM `b-official`");
    while ($row = $result->fetch_assoc()):
    ?>
        <tr>
            <td><img src="<?= $row['picture'] ?>" width="60"></td>
            <td><?= $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname'] ?></td>
            <td><?= $row['position'] ?></td>
            <td><?= $row['barangay'] ?></td>
            <td><?= $row['number'] ?></td>
            <td><?= $row['age'] ?></td>
            <td><?= $row['dateofbirth'] ?></td>
            <td><?= $row['address'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['password'] ?></td>
            <td>
                <button onclick='openEditModal(<?= json_encode($row) ?>)'>Edit</button>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this official?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<!-- Modal Background Overlay -->
<div id="overlay" class="modal-overlay" style="display:none;"></div>

<!-- Edit Modal Window -->
<div id="editModal" class="modal-window" style="display:none;">
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit-id">
        <label>First Name: <input type="text" name="fname" id="edit-fname" required></label><br><br>
        <label>Middle Name: <input type="text" name="mname" id="edit-mname" required></label><br><br>
        <label>Last Name: <input type="text" name="lname" id="edit-lname" required></label><br><br>
        <label>Number: <input type="text" name="number" id="edit-number" required></label><br><br>
        <label>Barangay: <input type="text" name="barangay" id="edit-barangay" required></label><br><br>
        <label>Position: <input type="text" name="position" id="edit-position" required></label><br><br>
        <label>Age: <input type="number" name="age" id="edit-age" required></label><br><br>
        <label>Date of Birth: <input type="date" name="dateofbirth" id="edit-dob" required></label><br><br>
        <label>Address: <input type="text" name="address" id="edit-address" required></label><br><br>
        <label>Email: <input type="email" name="email" id="edit-email" required></label><br><br>
        <label>Password: <input type="text" name="password" id="edit-password" required></label><br><br>
        <label>Change Picture: <input type="file" name="picture"></label><br><br>
        <button type="submit" name="update">Update</button>
        <button type="button" onclick="closeModal()">Cancel</button>
    </form>
</div>

<script>
function openEditModal(data) {
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-fname').value = data.fname;
    document.getElementById('edit-mname').value = data.mname;
    document.getElementById('edit-lname').value = data.lname;
    document.getElementById('edit-number').value = data.number;
    document.getElementById('edit-barangay').value = data.barangay;
    document.getElementById('edit-position').value = data.position;
    document.getElementById('edit-age').value = data.age;
    document.getElementById('edit-dob').value = data.dateofbirth;
    document.getElementById('edit-address').value = data.address;
    document.getElementById('edit-email').value = data.email;
    document.getElementById('edit-password').value = data.password;

    document.getElementById('editModal').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}
</script>

</body>
</html>
