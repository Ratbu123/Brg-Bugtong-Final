<?php
$conn = new mysqli("localhost", "root", "", "brg-pulo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$editData = null;

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM `res-info` WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<script>alert('Resident deleted successfully.'); window.location.href = 'admin.php?section=populations';</script>";
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $stmt = $conn->prepare("UPDATE `res-info` SET fname=?, mname=?, lname=?, age=?, dob=?, `c-status`=?, number=? WHERE id=?");
    $stmt->bind_param(
        "sssssssi",
        $_POST['fname'],
        $_POST['mname'],
        $_POST['lname'],
        $_POST['age'],
        $_POST['dob'],
        $_POST['c_status'],
        $_POST['number'],
        $_POST['edit_id']
    );
    $stmt->execute();
    echo "<script>alert('Resident updated successfully.'); window.location.href = 'admin.php?section=populations';</script>";
    exit();
}

// Handle edit
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM `res-info` WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $editData = $result->fetch_assoc();
    }
}

// Fetch residents
$sql = "SELECT * FROM `res-info` ORDER BY purok, lname, fname";
$result = $conn->query($sql);
$residents = [];
$streets = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $residents[] = $row;
        if (!in_array($row['purok'], $streets)) {
            $streets[] = $row['purok'];
        }
    }
}
sort($streets);
?>

<!-- STREETS BOXES -->
<div class="p-4">
  <h2 class="text-2xl font-bold mb-4 text-gray-800">All Residents by Street</h2>
  <div class="flex flex-wrap gap-4 mb-8">
    <?php foreach ($streets as $street): ?>
      <div class="cursor-pointer bg-blue-100 hover:bg-blue-200 text-blue-800 px-6 py-4 rounded-lg shadow text-center"
           onclick="openModal('modal_<?= $street ?>')">
        <strong><?= htmlspecialchars($street) ?></strong>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- MODALS PER STREET -->
<?php foreach ($streets as $street): ?>
  <div id="modal_<?= $street ?>" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white max-w-5xl w-full p-6 rounded-lg shadow-lg relative overflow-y-auto max-h-[90vh] flex flex-col">
      <button onclick="closeModal('modal_<?= $street ?>')" class="absolute top-2 right-4 text-gray-600 text-2xl font-bold hover:text-red-500">&times;</button>
      <h2 class="text-xl font-bold mb-4 text-gray-800">Residents in Street: <?= htmlspecialchars($street) ?></h2>

      <div class="overflow-x-auto">
        <table class="min-w-full bg-white text-sm border border-gray-200">
          <thead class="bg-gray-100 text-gray-700 font-semibold">
            <tr>
              <th class="px-4 py-2">Profile</th>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">Age</th>
              <th class="px-4 py-2">DOB</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Contact</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($residents as $person): ?>
              <?php if ($person['purok'] == $street): ?>
                <tr class="border-t">
                  <td class="p-2 text-center">
                    <img src="<?= $person['profile'] ?: 'images/sub/usericon.png' ?>" alt="Profile"
                         class="w-10 h-10 rounded-full object-cover inline-block">
                  </td>
                  <td class="p-2"><?= htmlspecialchars($person['fname'] . ' ' . $person['mname'] . ' ' . $person['lname']) ?></td>
                  <td class="p-2"><?= htmlspecialchars($person['age']) ?></td>
                  <td class="p-2"><?= htmlspecialchars($person['dob']) ?></td>
                  <td class="p-2"><?= htmlspecialchars($person['c-status']) ?></td>
                  <td class="p-2"><?= htmlspecialchars($person['number']) ?></td>
                  <td class="p-2 flex flex-wrap gap-2">
                    <a href="admin.php?edit=<?= $person['id'] ?>" class="bg-yellow-400 text-white px-3 py-1 rounded hover:bg-yellow-500">Edit</a>
                    <a href="admin.php?delete=<?= $person['id'] ?>" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                       onclick="return confirm('Are you sure you want to delete this resident?')">Delete</a>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- EDIT MODAL -->
<?php if ($editData): ?>
  <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-lg relative">
      <button onclick="window.location.href='admin.php?section=populations'" class="absolute top-2 right-4 text-gray-600 text-2xl font-bold hover:text-red-500">&times;</button>
      <h3 class="text-lg font-bold mb-4">Edit Resident</h3>
      <form method="POST" action="admin.php" class="space-y-3">
        <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
        <input type="text" name="fname" value="<?= htmlspecialchars($editData['fname']) ?>" placeholder="First Name" required class="w-full border px-3 py-2 rounded">
        <input type="text" name="mname" value="<?= htmlspecialchars($editData['mname']) ?>" placeholder="Middle Name" required class="w-full border px-3 py-2 rounded">
        <input type="text" name="lname" value="<?= htmlspecialchars($editData['lname']) ?>" placeholder="Last Name" required class="w-full border px-3 py-2 rounded">
        <input type="number" name="age" value="<?= htmlspecialchars($editData['age']) ?>" placeholder="Age" required class="w-full border px-3 py-2 rounded">
        <input type="date" name="dob" value="<?= htmlspecialchars($editData['dob']) ?>" required class="w-full border px-3 py-2 rounded">
        <input type="text" name="c_status" value="<?= htmlspecialchars($editData['c-status']) ?>" placeholder="Civil Status" required class="w-full border px-3 py-2 rounded">
        <input type="text" name="number" value="<?= htmlspecialchars($editData['number']) ?>" placeholder="Contact Number" required class="w-full border px-3 py-2 rounded">
        <div class="text-right">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>

<!-- SCRIPTS -->
<script>
function openModal(id) {
  const modal = document.getElementById(id);
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}
function closeModal(id) {
  const modal = document.getElementById(id);
  modal.classList.remove('flex');
  modal.classList.add('hidden');
}
</script>
