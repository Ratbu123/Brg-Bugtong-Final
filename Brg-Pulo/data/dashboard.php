<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'])) {
  $name = trim($_POST['name']);
  $date = $_POST['date'];
  $certificate_type = "Certificate of Indigency";
  $status = "Pending";

  $stmt = $conn->prepare("INSERT INTO request (name, date, status, certificate_type) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $date, $status, $certificate_type);
  if ($stmt->execute()) {
    header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
    exit;
  }
  $stmt->close();
}

$success = isset($_GET['success']);

function getCount($conn, $query) {
  $res = $conn->query($query);
  return $res && $res->num_rows > 0 ? $res->fetch_assoc()['total'] : 0;
}

$residents       = getCount($conn, "SELECT COUNT(*) AS total FROM `res-info`");
$officials       = getCount($conn, "SELECT COUNT(*) AS total FROM `b-official`");
$approved        = getCount($conn, "SELECT COUNT(*) AS total FROM request WHERE LOWER(TRIM(status)) = 'approved'");
$newRequests     = getCount($conn, "SELECT COUNT(*) AS total FROM request WHERE status IS NULL OR TRIM(status) = ''");

$recentRequests = $conn->query("SELECT * FROM request ORDER BY date DESC LIMIT 3");
$allRequests    = $conn->query("SELECT * FROM request ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Barangay Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans p-6">

<?php if ($success): ?>
  <div id="successMsg" class="bg-green-100 text-green-800 p-4 rounded mb-4 shadow">
    ✅ Your request has been submitted successfully!
  </div>
  <script>
    setTimeout(() => document.getElementById('successMsg')?.remove(), 4000);
  </script>
<?php endif; ?>

<!-- Header -->
<div class="flex justify-between items-center mb-8">
  <h1 class="text-3xl font-bold text-gray-800">🏡 Barangay Dashboard</h1>
  <span class="text-sm text-gray-600"><?= date("l, F j, Y") ?></span>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 mb-10">
  <?php
    $metrics = [
      ["title" => "Officials", "value" => $officials, "color" => "blue"],
      ["title" => "Approved", "value" => $approved, "color" => "green"],
      ["title" => "New Requests", "value" => $newRequests, "color" => "red"]
    ];
    foreach ($metrics as $metric):
  ?>
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
      <h3 class="text-lg font-medium text-gray-700"><?= $metric['title'] ?></h3>
      <p class="text-3xl font-bold text-<?= $metric['color'] ?>-600 mt-2"><?= $metric['value'] ?></p>
    </div>
  <?php endforeach; ?>
</div>

<!-- Recent Requests -->
<div class="mb-10">
  <div class="flex justify-between items-center mb-3">
    <h2 class="text-xl font-bold text-gray-800">📝 Recent Requests</h2>
    <button onclick="toggleModal('modalAll')" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded shadow transition">
      See All
    </button>
  </div>
  <div class="overflow-x-auto bg-white rounded-xl shadow">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-200 text-gray-700 font-semibold">
        <tr>
          <th class="p-3 text-left">Name</th>
          <th class="p-3 text-left">Date</th>
          <th class="p-3 text-left">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $recentRequests->fetch_assoc()):
          $status = strtolower(trim($row['status'] ?? ''));
          $badge = match($status) {
            'approved'    => 'bg-green-500 text-white',
            'in progress' => 'bg-blue-500 text-white',
            'declined'    => 'bg-red-500 text-white',
            default       => 'bg-yellow-400 text-black'
          };
          $label = $status ? ucfirst($status) : "Pending";
        ?>
        <tr class="border-t">
          <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
          <td class="p-3"><?= date("M d, Y", strtotime($row['date'])) ?></td>
          <td class="p-3"><span class="px-2 py-1 rounded <?= $badge ?>"><?= $label ?></span></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>


<!-- All Requests Modal -->
<div id="modalAll" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50 backdrop-blur">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl p-6 overflow-y-auto max-h-[85vh] scale-95 transition">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">📋 All Requests</h2>
      <button onclick="toggleModal('modalAll')" class="text-2xl text-gray-500 hover:text-red-600">&times;</button>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-gray-200 text-gray-700 font-semibold">
        <tr><th class="p-3">Name</th><th class="p-3">Date</th><th class="p-3">Status</th></tr>
      </thead>
      <tbody>
        <?php
        $allRequests->data_seek(0); // reset pointer
        while ($row = $allRequests->fetch_assoc()):
          $status = strtolower(trim($row['status'] ?? ''));
          $badge = match($status) {
            'approved'    => 'bg-green-500 text-white',
            'in progress' => 'bg-blue-500 text-white',
            'declined'    => 'bg-red-500 text-white',
            default       => 'bg-yellow-400 text-black'
          };
          $label = $status ? ucfirst($status) : "Pending";
        ?>
        <tr class="border-t">
          <td class="p-3"><?= htmlspecialchars($row['name']) ?></td>
          <td class="p-3"><?= date("M d, Y", strtotime($row['date'])) ?></td>
          <td class="p-3"><span class="px-2 py-1 rounded <?= $badge ?>"><?= $label ?></span></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Floating Form Modal -->
<div id="formModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50 backdrop-blur">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-md p-6 scale-95 transition">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold">📝 Request Certificate</h2>
      <button onclick="toggleModal('formModal')" class="text-2xl text-gray-500 hover:text-red-600">&times;</button>
    </div>
    <form method="POST">
      <div class="mb-4">
        <label class="block text-sm font-medium">Name</label>
        <input type="text" name="name" required pattern="[A-Za-z\s]+" maxlength="100"
               class="mt-1 p-2 w-full border border-gray-300 rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
      </div>
      <div class="mb-4">
        <label class="block text-sm font-medium">Date</label>
        <input type="date" name="date" value="<?= date('Y-m-d') ?>" readonly
               class="mt-1 p-2 w-full border border-gray-300 rounded bg-gray-100 text-gray-700">
      </div>
      <div class="text-right">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit</button>
      </div>
    </form>
  </div>
</div>

<script>
  function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('hidden');
    modal.classList.toggle('flex');
  }
</script>

<?php $conn->close(); ?>
</body>
</html>
