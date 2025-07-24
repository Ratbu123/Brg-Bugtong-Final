<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "brg-pulo";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle POST -> redirect to avoid resubmission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name'])) {
    $name = $_POST['name'];
    $date = $_POST['date'];

    $stmt = $conn->prepare("INSERT INTO request (name, date) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $date);

    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
        exit;
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$success = isset($_GET['success']);

// Dashboard data
$officialsResult = $conn->query("SELECT COUNT(*) AS total FROM `b-official`");
$row = $officialsResult->fetch_assoc();
$officials = $row ? $row['total'] : 0;

$approvedResult = $conn->query("SELECT COUNT(*) AS total FROM request WHERE LOWER(TRIM(status)) = 'approved'");
$approved = ($approvedResult && $approvedResult->num_rows > 0) ? $approvedResult->fetch_assoc()['total'] : 0;

$jobs = $conn->query("SELECT COUNT(*) AS total FROM job_listings")->fetch_assoc()['total'] ?? 0;


$recentRequests = $conn->query("SELECT * FROM request ORDER BY date DESC LIMIT 3");
$allRequests = $conn->query("SELECT * FROM request ORDER BY date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Dashboard</title>
  <style>

    .cards {
      display: flex;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 30px;
    }

    .barangay-box {
      flex: 1;
      min-width: 250px;
      background-color: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      text-align: center;
      transition: transform 0.2s;
    }

    .barangay-box:hover {
      transform: scale(1.02);
    }

    .section {
      margin-bottom: 40px;
    }

    .section-title {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .section-title h2 {
      font-size: 1.5rem;
    }

    .section-title a {
      font-size: 0.9rem;
      color: #007bff;
      text-decoration: none;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
      background-color: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    table th, table td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    .status-label {
      padding: 6px 10px;
      border-radius: 8px;
      color: #fff;
      font-size: 0.9rem;
    }

    .status-label.success { background-color: #28a745; }
    .status-label.info { background-color: #17a2b8; }
    .status-label.danger { background-color: #dc3545; }
    .status-label.warning { background-color: #ffc107; color: #000; }

    table button {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 6px 14px;
      border-radius: 6px;
      cursor: pointer;
    }

    table button:hover {
      background-color: #0056b3;
    }

    /* Modal Styles */
    .modal, .form-modal {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-content, .form-modal-content {
      background-color: #fff;
      margin: 5% auto;
      padding: 20px;
      border-radius: 12px;
      width: 90%;
      max-width: 700px;
      max-height: 80vh;
      overflow-y: auto;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .close-btn, .form-close-btn {
      float: right;
      font-size: 24px;
      font-weight: bold;
      color: #333;
      cursor: pointer;
    }

    .close-btn:hover, .form-close-btn:hover {
      color: red;
    }

    .form-modal-content form label {
      display: block;
      margin-top: 10px;
    }

    .form-modal-content form input {
      width: 100%;
      padding: 8px;
      margin-top: 4px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    .form-modal-content form button {
      margin-top: 15px;
      background-color: #007bff;
      color: white;
      padding: 8px 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .form-modal-content form button:hover {
      background-color: #0056b3;
    }
  </style>
</head>
<body>

<?php if ($success): ?>
  <p style="color: green;">Request submitted successfully!</p>
<?php endif; ?>

<div class="cards">
  <div class="barangay-box">
    <h3>No. of Officials</h3>
    <h2><?= $officials ?></h2>
  </div>
  <div class="barangay-box">
    <h3>Approved Requests</h3>
    <h2><?= $approved ?></h2>
  </div>
  <div class="barangay-box">
    <h3>Unread Messages</h3>
    <h2><?= $jobs ?></h2>
  </div>
</div>

<div class="section">
  <div class="section-title">
    <h2>Recently Requested</h2>
    <a href="#" onclick="document.getElementById('modal').style.display='block'">See All</a>
  </div>
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $recentRequests->fetch_assoc()): ?>
        <?php
          $status = strtolower(trim($row['status']));
          $class = 'warning'; $label = 'Processing';
          if ($status === 'approved') { $class = 'success'; $label = 'Approved'; }
          elseif ($status === 'in progress') { $class = 'info'; $label = 'In Progress'; }
          elseif ($status === 'declined') { $class = 'danger'; $label = 'Declined'; }
        ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= date("M d, Y", strtotime($row['date'])) ?></td>
          <td><span class="status-label <?= $class ?>"><?= $label ?></span></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- See All Modal -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="document.getElementById('modal').style.display='none'">&times;</span>
    <h2>All Requests</h2>
    <table>
      <thead>
        <tr><th>Name</th><th>Date</th><th>Status</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $allRequests->fetch_assoc()): ?>
          <?php
            $status = strtolower(trim($row['status']));
            $class = 'warning'; $label = 'Processing';
            if ($status === 'approved') { $class = 'success'; $label = 'Approved'; }
            elseif ($status === 'in progress') { $class = 'info'; $label = 'In Progress'; }
            elseif ($status === 'declined') { $class = 'danger'; $label = 'Declined'; }
          ?>
          <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= date("M d, Y", strtotime($row['date'])) ?></td>
            <td><span class="status-label <?= $class ?>"><?= $label ?></span></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Request Documents -->
<div class="section">
  <div class="section-title"><h2>Request Documents</h2></div>
  <table>
    <thead><tr><th>Document</th><th>Description</th><th>Action</th></tr></thead>
    <tbody>
      <tr>
        <td>Certificate of Indigency</td>
        <td>Issued to residents classified as low-income. Required for scholarships, medical, or financial aid.</td>
        <td><button onclick="openFormModal()">Apply Now</button></td>
      </tr>
    </tbody>
  </table>
</div>

<!-- Floating Request Form -->
<div id="formModal" class="form-modal">
  <div class="form-modal-content">
    <span class="form-close-btn" onclick="closeFormModal()">&times;</span>
    <h2>Request Certificate of Indigency</h2>
    <form method="POST" action="">
      <label for="name">Name</label>
      <input type="text" id="name" name="name" required>
      <label for="date">Date</label>
      <input type="date" id="date" name="date" value="<?= date('Y-m-d') ?>" readonly>
      <button type="submit">Submit</button>
    </form>
  </div>
</div>

<?php $conn->close(); ?>

<script>
  function openFormModal() {
    document.getElementById("formModal").style.display = "block";
  }
  function closeFormModal() {
    document.getElementById("formModal").style.display = "none";
  }
  window.onclick = function(e) {
    if (e.target === document.getElementById("modal")) document.getElementById("modal").style.display = "none";
    if (e.target === document.getElementById("formModal")) document.getElementById("formModal").style.display = "none";
  };
</script>

</body>
</html>
