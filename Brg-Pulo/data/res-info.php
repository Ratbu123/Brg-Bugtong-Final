<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "brg-pulo";
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$toastMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname     = trim($_POST['fname']);
    $lname     = trim($_POST['lname']);
    $mname     = trim($_POST['mname']);
    $age       = trim($_POST['age']);
    $number    = trim($_POST['number']);
    $c_status  = $_POST['c-status'];
    $dob       = $_POST['dob'];
    $housen    = trim($_POST['housen']);
    $purok     = $_POST['purok'];

    $profile = '';
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES["profile"]["name"], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $target_dir = "../uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $safeFileName = time() . '_' . basename($_FILES["profile"]["name"]);
            $target_file = $target_dir . $safeFileName;
            if (move_uploaded_file($_FILES["profile"]["tmp_name"], $target_file)) {
                $profile = $target_file;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO `res-info` 
        (fname, lname, mname, age, number, `c-status`, dob, profile, housen, purok) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $fname, $lname, $mname, $age, $number, $c_status, $dob, $profile, $housen, $purok);

    if ($stmt->execute()) {
        $toastMessage = "✅ Resident added successfully!";
    } else {
        $toastMessage = "❌ Failed to add resident: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Resident</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6">

  <?php if (!empty($toastMessage)): ?>
    <div id="toast" class="fixed top-6 right-6 z-50 bg-white text-gray-800 border border-green-500 px-5 py-3 shadow-lg rounded-md">
      <?= htmlspecialchars($toastMessage) ?>
    </div>
    <script>setTimeout(() => document.getElementById("toast").remove(), 4000);</script>
  <?php endif; ?>

  <div class="bg-white shadow-xl rounded-xl p-8 w-full max-w-4xl">
    <h2 class="text-3xl font-bold mb-6 text-center text-blue-800">➕ Add New Resident</h2>

    <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="disableSubmitBtn()">

      <!-- Profile Upload -->
      <div class="flex flex-col items-center space-y-4">
        <div class="relative group">
          <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-blue-500 shadow-md bg-gray-100">
            <img id="preview-image" src="../images/sub/usericon.png" alt="Preview" class="object-cover w-full h-full transition duration-300">
          </div>
          <label for="profileUpload" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-40 text-white text-xs font-semibold rounded-full cursor-pointer">
            Change Photo
          </label>
        </div>

        <div class="w-full text-center">
          <label for="profileUpload" class="bg-blue-600 text-white px-4 py-2 rounded cursor-pointer hover:bg-blue-700 transition">
            📁 Choose Profile Picture
          </label>
          <input id="profileUpload" type="file" name="profile" accept="image/*" class="hidden" onchange="previewImage(event)">
          <p id="file-name" class="text-sm text-gray-600 mt-1">No file selected</p>
        </div>
      </div>

      <!-- Resident Fields -->
      <div class="space-y-4">
        <input type="text" name="fname" placeholder="First Name" required class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
        <input type="text" name="lname" placeholder="Last Name" required class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
        <input type="text" name="mname" placeholder="Middle Name" required class="w-full p-2 border rounded focus:ring focus:ring-blue-300">
        <input type="number" name="age" id="age" placeholder="Age" required readonly class="w-full p-2 border rounded bg-gray-100">

        <input type="text" name="number" placeholder="Contact Number (e.g., 09XXXXXXXXX)" required pattern="09[0-9]{9}" maxlength="11" class="w-full p-2 border rounded">

        <select name="c-status" required class="w-full p-2 border rounded">
          <option value="">Civil Status</option>
          <option value="Single">Single</option>
          <option value="Married">Married</option>
          <option value="Widowed">Widowed</option>
          <option value="Separated">Separated</option>
        </select>

        <label class="block font-medium">Date of Birth</label>
        <input type="date" name="dob" id="dob" required class="w-full p-2 border rounded" onchange="calculateAge()">
        <input type="text" name="housen" placeholder="House Number" required class="w-full p-2 border rounded">

        <select name="purok" required class="w-full p-2 border rounded">
          <option value="">Street (Purok)</option>
          <option value="Areca Palm">Areca Palm</option>
          <option value="Bamboo Palm">Bamboo Palm</option>
          <option value="California Palm">California Palm</option>
          <option value="Chinese Palm">Chinese Palm</option>
          <option value="Christmas Palm">Christmas Palm</option>
          <option value="King Sago">King Sago</option>
          <option value="Lady Palm">Lady Palm</option>
          <option value="Majesty Palm">Majesty Palm</option>
          <option value="Olive Street">Olive Street</option>
          <option value="Palmdale Street">Palmdale Street</option>
          <option value="Periwinkle Loop">Periwinkle Loop</option>
        </select>

        <button id="submitBtn" type="submit" class="mt-6 bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 w-full">
          ➕ Submit Resident Info
        </button>
      </div>
    </form>
  </div>

  <script>
    function previewImage(event) {
      const input = event.target;
      const previewImage = document.getElementById('preview-image');
      const fileNameText = document.getElementById('file-name');
      if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImage.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
        fileNameText.textContent = input.files[0].name;
      } else {
        previewImage.src = "../images/sub/usericon.png";
        fileNameText.textContent = "No file selected";
      }
    }

    function calculateAge() {
      const dob = document.getElementById('dob').value;
      const ageField = document.getElementById('age');
      if (dob) {
        const today = new Date();
        const birthDate = new Date(dob);
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
        ageField.value = age;
      }
    }

    function disableSubmitBtn() {
      const btn = document.getElementById("submitBtn");
      btn.disabled = true;
      btn.innerText = "Submitting...";
    }
  </script>
</body>
</html>
