<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// DELETE
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete = $conn->query("DELETE FROM `b-official` WHERE id = $id");
    echo $delete
        ? "<script>alert('Official deleted successfully!'); window.location='/Brg-Bugtong-Final/Brg-Pulo/admin.php';</script>"
        : "Delete failed: " . $conn->error;
    exit();
}

// UPDATE
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $fields = [
        'fname', 'mname', 'lname', 'number', 'position',
        'age', 'dateofbirth', 'address', 'email'
    ];
    $values = [];
    foreach ($fields as $field) {
        $values[$field] = $_POST[$field] ?? '';
    }

    $pictureClause = '';
    if ($_FILES['picture']['name']) {
        $target = "data/uploads/" . basename($_FILES['picture']['name']);
        move_uploaded_file($_FILES['picture']['tmp_name'], $target);
        $pictureClause = ", picture='$target'";
    }

    $sql = "UPDATE `b-official` SET 
            fname='{$values['fname']}', mname='{$values['mname']}', lname='{$values['lname']}',
            number='{$values['number']}', position='{$values['position']}',
            age='{$values['age']}', dateofbirth='{$values['dateofbirth']}', address='{$values['address']}',
            email='{$values['email']}'
            $pictureClause WHERE id=$id";

    echo $conn->query($sql)
        ? "<script>alert('Official updated successfully!'); window.location='/Brg-Bugtong-Final/Brg-Pulo/admin.php';</script>"
        : "Update failed: " . $conn->error;
    exit();
}

$result = $conn->query("SELECT * FROM `b-official`");
if (!$result || $result->num_rows === 0) {
    echo "<div class='text-center p-6 text-red-600 font-semibold'>No officials found in the database.</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Barangay Officials</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
  <div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold text-blue-700 mb-6">👥 Barangay Officials Directory</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="bg-white p-4 rounded-lg shadow">
          <img src="<?= $row['picture'] ?: 'https://via.placeholder.com/100' ?>" class="w-24 h-24 object-cover rounded-full border mb-3 mx-auto">
          <h2 class="text-xl font-semibold text-center"><?= $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname'] ?></h2>
          <p class="text-center text-sm text-gray-600 mb-2">Position: <strong><?= $row['position'] ?></strong></p>
          <div class="flex justify-center gap-2 mt-3">
            <button type="button" onclick='openEditModal(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-3 py-1 rounded">Edit</button>
            <button type="button" onclick='openViewModal(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded">View</button>
            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this official?')" class="bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded">Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <!-- Overlay -->
  <div id="overlay" class="fixed inset-0 bg-black bg-opacity-40 hidden z-40"></div>

  <!-- Edit Modal -->
  <div id="editModal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-6 w-full max-w-lg rounded-lg shadow z-50 hidden">
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" name="id" id="edit-id">
      <div class="grid grid-cols-2 gap-3">
        <input type="text" name="fname" id="edit-fname" placeholder="First Name" class="input" required>
        <input type="text" name="mname" id="edit-mname" placeholder="Middle Name" class="input" required>
        <input type="text" name="lname" id="edit-lname" placeholder="Last Name" class="input" required>
        <input type="text" name="number" id="edit-number" placeholder="Contact" class="input" required>
        <input type="text" name="position" id="edit-position" placeholder="Position" class="input" required>
        <input type="number" name="age" id="edit-age" placeholder="Age" class="input" required>
        <input type="date" name="dateofbirth" id="edit-dob" class="input" required>
        <input type="text" name="address" id="edit-address" placeholder="Address" class="input" required>
        <input type="email" name="email" id="edit-email" placeholder="Email" class="input" required>
        <input type="file" name="picture" class="input col-span-2">
      </div>
      <div class="flex justify-end space-x-2 pt-3">
        <button type="submit" name="update" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Update</button>
        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
      </div>
    </form>
  </div>

  <!-- View Modal -->
  <div id="viewModal" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white p-6 w-full max-w-md rounded-lg shadow z-50 hidden">
    <h2 class="text-xl font-bold mb-4">Official Details</h2>
    <div class="space-y-2 text-sm text-gray-700" id="viewDetails"></div>
    <div class="text-right mt-4">
      <button onclick="closeModal('viewModal')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Close</button>
    </div>
  </div>

  <script>
    function openEditModal(data) {
      const fields = ['id','fname','mname','lname','number','position','age','dateofbirth','address','email'];
      fields.forEach(f => {
        const el = document.getElementById('edit-' + f);
        if (el) el.value = data[f] || '';
      });
      document.getElementById('editModal').classList.remove('hidden');
      document.getElementById('overlay').classList.remove('hidden');
    }

    function openViewModal(data) {
      const viewDetails = document.getElementById('viewDetails');
      viewDetails.innerHTML = `
        <p><strong>Name:</strong> ${data.fname} ${data.mname} ${data.lname}</p>
        <p><strong>Position:</strong> ${data.position}</p>
        <p><strong>Contact:</strong> ${data.number}</p>
        <p><strong>Email:</strong> ${data.email}</p>
        <p><strong>Address:</strong> ${data.address}</p>
        <p><strong>Age:</strong> ${data.age}</p>
        <p><strong>Date of Birth:</strong> ${data.dateofbirth}</p>
      `;
      document.getElementById('viewModal').classList.remove('hidden');
      document.getElementById('overlay').classList.remove('hidden');
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.add('hidden');
      document.getElementById('overlay').classList.add('hidden');
    }
  </script>

  <style>
    .input {
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 0.375rem;
      width: 100%;
    }
  </style>
</body>
</html>
