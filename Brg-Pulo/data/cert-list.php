<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Handle Approve or Decline
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'], $_POST['id'])) {
  $id = intval($_POST['id']);
  $action = $_POST['action'] === 'approve' ? 'approved' : 'declined';
  $stmt = $conn->prepare("UPDATE request SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $action, $id);
  $stmt->execute();
  $stmt->close();
}

// Fetch all requests (without filtering by certificate type)
$sql = "SELECT id, name, date, certificate_type, status FROM request ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Certificate Requests</title>
</head>
<body>

  <h1>List of Certificate Requests</h1>
  
  <table border="1" cellpadding="5" cellspacing="0">
    <thead>
      <tr>
        <th>Name</th>
        <th>Date of Request</th>
        <th>Certificate Type</th>
        <th>View Certificate</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo date("F j, Y", strtotime($row['date'])); ?></td>
            <td><?php echo htmlspecialchars($row['certificate_type']); ?></td>
            <td><a href="./data/reqform.php?id=<?php echo $row['id']; ?>">View</a></td>
            <td>
              <?php
                if ($row['status'] === 'approved') {
                  echo '✔';
                } elseif ($row['status'] === 'declined') {
                  echo '❌';
                } else {
                  echo 'Pending';
                }
              ?>
            </td>
            <td>
              <?php if (!$row['status']): ?>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="action" value="approve">
                  <button type="submit">Approve</button>
                </form>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="action" value="decline">
                  <button type="submit">Decline</button>
                </form>
              <?php else: ?>
                No actions
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="6">No requests found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</body>
</html>

<?php $conn->close(); ?>
