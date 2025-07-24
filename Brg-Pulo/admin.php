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
        <img src="images/sub/usericon.png" class="icon" alt="Employer Icon">
        <p>Hello,<br><strong>Employer</strong></p>
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
        <a onclick="showSection('add-off', event)">
          <i class="fas fa-user-plus"></i> Add officials
        </a>
        <a onclick="showSection('officials', event)">
          <i class="fas fa-user-tie"></i> Brg. Official
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
          <i class="fas fa-bullhorn"></i> Announcement
        </a>
      </nav>
    </aside>

    <!-- Top Bar -->
    <div class="topbar">
      <div class="spacer"></div>
      <div class="top-icons">
        <i class="fas fa-bell"></i>
        <button class="logout-btn" onclick="location.href='LogIn.php'">
          <i class="fas fa-arrow-right-from-bracket"></i> Log Out
        </button>
      </div>
    </div>

    <!-- Main Content Sections -->
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

      <section id="add-off" class="content-section">
        <h2>Add Officials</h2>
         <?php include 'data/add-off.php'; ?>
      </section>

      <section id="officials" class="content-section">
        <h2>Edit/Delete Officials</h2>
        <?php include 'data/brgofficials.php'; ?>
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