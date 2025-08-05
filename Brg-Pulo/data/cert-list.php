<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle approve/decline and redirect using PRG pattern
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'], $_POST['id'])) {
  $id = intval($_POST['id']);
  $action = $_POST['action'] === 'approve' ? 'approved' : 'declined';
  $stmt = $conn->prepare("UPDATE request SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $action, $id);
  $stmt->execute();
  $stmt->close();

  // Redirect to avoid form resubmission
  $redirectUrl = $_SERVER['PHP_SELF'] . '?' . http_build_query($_GET);
  header("Location: $redirectUrl");
  exit;
}

// Get search and filter
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$searchEscaped = $conn->real_escape_string($search);
$statusEscaped = $conn->real_escape_string($statusFilter);

$where = [];
if (!empty($search)) {
  $where[] = "(name LIKE '%$searchEscaped%' OR certificate_type LIKE '%$searchEscaped%')";
}
if (!empty($statusFilter)) {
  $where[] = "status = '$statusEscaped'";
}
$whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

// Count total for pagination
$countSql = "SELECT COUNT(*) as total FROM request $whereClause";
$countResult = $conn->query($countSql);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$sql = "SELECT id, name, date, certificate_type, status FROM request $whereClause ORDER BY date DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Certificate Requests</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6 text-center">Certificate Requests</h1>

    <!-- Search and Filter Bar -->
    <form method="GET" class="mb-6 flex flex-col sm:flex-row items-center gap-4 max-w-4xl mx-auto">
      <input 
        type="text" 
        name="search" 
        placeholder="Search by name or certificate type..." 
        value="<?= htmlspecialchars($search) ?>" 
        class="w-full sm:w-2/3 px-4 py-2 border border-gray-300 rounded shadow focus:outline-none focus:ring focus:border-blue-500"
      >

      <select name="status" class="w-full sm:w-1/3 px-3 py-2 border border-gray-300 rounded shadow">
        <option value="">All Statuses</option>
        <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
        <option value="approved" <?= $statusFilter === 'approved' ? 'selected' : '' ?>>Approved</option>
        <option value="declined" <?= $statusFilter === 'declined' ? 'selected' : '' ?>>Declined</option>
      </select>

      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Filter</button>
    </form>

    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 uppercase">
          <tr>
            <th class="px-4 py-3">Name</th>
            <th class="px-4 py-3">Date of Request</th>
            <th class="px-4 py-3">Certificate Type</th>
            <th class="px-4 py-3">View</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3 text-center">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
                <td class="px-4 py-2"><?= date("F j, Y", strtotime($row['date'])) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['certificate_type']) ?></td>
                <td class="px-4 py-2">
                  <a href="./data/reqform.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">View</a>
                </td>
                <td class="px-4 py-2">
                  <?php
                    if ($row['status'] === 'approved') {
                      echo '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">Approved</span>';
                    } elseif ($row['status'] === 'declined') {
                      echo '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">Declined</span>';
                    } else {
                      echo '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">Pending</span>';
                    }
                  ?>
                </td>
                <td class="px-4 py-2 text-center">
                  <?php if ($row['status'] === null || $row['status'] === ''): ?>
                    <div class="flex gap-2 justify-center">
                      <form method="POST">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">Approve</button>
                      </form>
                      <form method="POST">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="action" value="decline">
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">Decline</button>
                      </form>
                    </div>
                  <?php else: ?>
                    <span class="text-gray-400 text-sm">No actions</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center px-4 py-4 text-gray-500">No requests found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
      <div class="mt-6 flex justify-center gap-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <a href="?search=<?= urlencode($search) ?>&status=<?= urlencode($statusFilter) ?>&page=<?= $i ?>"
             class="px-3 py-1 border rounded <?= $i === $page ? 'bg-blue-600 text-white' : 'bg-white text-blue-600 hover:bg-blue-100' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>

<?php $conn->close(); ?>
