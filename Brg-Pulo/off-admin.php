<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user info from session
$userName = $_SESSION['user_name'] ?? 'Employer';
$userPicture = $_SESSION['user_picture'] ?? 'images/sub/usericon.png';

// Ensure image path is correct (stored relative to project root)
if (!empty($userPicture) && file_exists($userPicture)) {
    $displayPicture = $userPicture;
} else {
    $displayPicture = 'images/sub/usericon.png'; // fallback
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Employer Dashboard</title>
  <link rel="stylesheet" href="/styles/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <img src="<?= htmlspecialchars($displayPicture) ?>" class="icon" alt="Profile Picture">
        <p>Hello,<br><strong><?= htmlspecialchars($userName) ?></strong></p>
      </div>
      <nav>
        <a class="active" onclick="showSection('dashboard', event)">
          <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a onclick="showSection('search', event)">
          <i class="fas fa-magnifying-glass"></i> Search Residents
        </a>
        <a onclick="showSection('populations', event)">
          <i class="fas fa-users"></i> Populations
        </a>
        <a onclick="showSection('request', event)">
          <i class="fas fa-envelope"></i> Requests
        </a>
        <a onclick="showSection('add', event)">
          <i class="fas fa-house-user"></i> Add Residents
        </a>
        <a onclick="showSection('hire', event)">
          <i class="fas fa-briefcase"></i> Job Posting
        </a>
        <a onclick="showSection('announce', event)">
          <i class="fas fa-bullhorn"></i> Job Posting
        </a>
      </nav>
    </aside>

    <!-- Top Bar -->
    <div class="topbar">
      <div class="spacer"></div>
      <div class="top-icons">
        <i class="fas fa-bell"></i>
        <form method="POST" action="Login.php" style="display:inline;">
          <button class="logout-btn" type="submit">
            <i class="fas fa-arrow-right-from-bracket"></i> Log Out
          </button>
        </form>
      </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
      <section id="dashboard" class="content-section">
        <h2>Dashboard</h2>
        <?php include 'data/dashboard.php'; ?>
      </section>

      <section id="search" class="content-section">
        <h2>Search Residents</h2>
        <?php include 'data/search.php'; ?>
      </section>

      <section id="populations" class="content-section" style="display: block;">
        <h2>Populations</h2>
        <?php include 'data/viewall.php'; ?>
      </section>

      <section id="request" class="content-section">
        <h2>Requests</h2>
        <?php include 'data/cert-list.php'; ?>
      </section>

      <section id="add" class="content-section">
        <?php include 'data/res-info.php'; ?>
      </section>

      <section id="hire" class="content-section">
        <?php include 'data/hiring.php'; ?>
      </section>

      <section id="announce" class="content-section">
        <?php include 'data/announcements.php'; ?>
      </section>
    </main>
  </div>

  <script src="script.js"></script>

</body>
</html>
