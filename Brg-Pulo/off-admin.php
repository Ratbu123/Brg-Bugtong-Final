<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Session data
$userName = $_SESSION['user_name'] ?? 'Brgy. Official';
$userPicture = $_SESSION['user_picture'] ?? __DIR__ . '/images/sub/usericon.png';
$displayPicture = (file_exists($userPicture)) ? $userPicture : __DIR__ . '/images/sub/usericon.png';
$defaultSection = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Brgy. Official Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    .content-section { display: none; }
    .active-link { background-color: #e0f2fe; font-weight: bold; }
    .sidebar-toggle { display: none; }
    @media (max-width: 1024px) {
      .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
      .sidebar.mobile-visible { transform: translateX(0); }
      .sidebar-toggle { display: inline-block; }
    }
  </style>
</head>
<body class="bg-gray-100 text-gray-900">

<!-- Sidebar -->
<aside id="sidebar" class="sidebar w-64 bg-white shadow-lg fixed h-full top-0 left-0 z-30 overflow-y-auto lg:translate-x-0">
  <div class="p-6 flex flex-col items-center border-b border-gray-200">
    <img src="<?= htmlspecialchars($displayPicture) ?>" alt="Profile" class="w-20 h-20 rounded-full mb-2 shadow">
    <p class="text-center text-sm text-gray-600">Hello,<br><span class="font-bold"><?= htmlspecialchars($userName) ?></span></p>
  </div>
  <nav class="mt-4 space-y-1 px-4 text-sm">
    <a href="#dashboard" id="link-dashboard" onclick="showSection('dashboard', event)" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-100 transition duration-200">
      <i class="fas fa-chart-line"></i> Dashboard
    </a>
    <a href="#request" id="link-request" onclick="showSection('request', event)" class="flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-100 transition duration-200">
      <i class="fas fa-envelope"></i> Requests
    </a>
  </nav>
</aside>

<!-- Main Content -->
<div class="flex flex-col lg:ml-64 min-h-screen">

  <!-- Topbar -->
  <header class="bg-white shadow flex justify-between items-center p-4 sticky top-0 z-20">
    <div class="flex items-center gap-4">
      <button id="toggleSidebar" class="sidebar-toggle text-gray-600 hover:text-blue-600 text-xl lg:hidden">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="text-lg font-semibold">Brgy. Official Panel</h1>
    </div>
    <div class="flex items-center gap-4">
      <button onclick="location.href='LogIn.php'" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center gap-2">
        <i class="fas fa-arrow-right-from-bracket"></i> Log Out
      </button>
    </div>
  </header>

  <!-- Page Sections -->
  <main class="flex-1 p-6">
    <section id="dashboard" class="content-section">
      <h2 class="text-2xl font-bold mb-4">Dashboard</h2>
      <?php include __DIR__ . '/data/dashboard.php'; ?>
    </section>

    <section id="request" class="content-section">
      <h2 class="text-2xl font-bold mb-4">Certificate Requests</h2>
      <?php include __DIR__ . '/data/cert-list.php'; ?>
    </section>
  </main>
</div>

<!-- Scripts -->
<script>
  function showSection(id, event = null) {
    if (event) event.preventDefault();
    document.querySelectorAll('.content-section').forEach(sec => sec.style.display = 'none');
    document.getElementById(id).style.display = 'block';

    document.querySelectorAll('.sidebar nav a').forEach(link => link.classList.remove('active-link'));
    const activeLink = document.getElementById('link-' + id);
    if (activeLink) activeLink.classList.add('active-link');
  }

  document.addEventListener("DOMContentLoaded", () => {
    const defaultSection = "<?= $defaultSection ?>";
    const hash = window.location.hash.replace('#', '') || defaultSection;
    showSection(hash);

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    toggleBtn?.addEventListener('click', () => {
      sidebar.classList.toggle('mobile-visible');
    });
  });
</script>

</body>
</html>
