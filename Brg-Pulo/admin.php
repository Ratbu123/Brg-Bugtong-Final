<?php
$defaultSection = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
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
    <img src="images/sub/usericon.png" alt="Admin Icon" class="w-20 h-20 rounded-full mb-2 shadow">
    <p class="text-center text-sm text-gray-600">Hello,<br><span class="font-bold">Admin</span></p>
  </div>
  <nav class="mt-4 space-y-1 px-4 text-sm">
    <?php
      $navs = [
        'dashboard' => 'Dashboard',
        'add-off' => 'Add Officials',
        'officials' => 'Brg. Officials',
        'request' => 'Requests',
      ];
      $icons = [
        'dashboard' => 'fa-chart-line',
        'add-off' => 'fa-user-plus',
        'officials' => 'fa-user-tie',
        'request' => 'fa-envelope',
      ];
      foreach ($navs as $id => $label) {
        echo "<a href='#$id' id='link-$id' onclick=\"showSection('$id', event)\" class='flex items-center gap-3 px-4 py-2 rounded hover:bg-blue-100 transition duration-200'>
          <i class='fas {$icons[$id]}'></i> $label
        </a>";
      }
    ?>
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
      <h1 class="text-lg font-semibold">Barangay Information Management</h1>
    </div>
    <div class="flex items-center gap-4">
      <!-- Notification Bell with Dropdown -->
      <div class="relative" id="notifWrapper">
        <button id="notifBell" class="relative text-gray-600 hover:text-blue-600 focus:outline-none">
          <i class="fas fa-bell text-xl"></i>
          <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center animate-ping">!</span>
        </button>
        <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white shadow-lg rounded-lg z-40 border border-gray-200">
          <div class="p-3 border-b font-semibold text-sm text-gray-700">Notifications</div>
          <ul class="max-h-60 overflow-y-auto text-sm text-gray-700">
            <li class="px-4 py-2 hover:bg-gray-100 border-b">New certificate request received.</li>
            <li class="px-4 py-2 hover:bg-gray-100 border-b">Official account updated.</li>
            <li class="px-4 py-2 hover:bg-gray-100">System backup completed.</li>
          </ul>
          <div class="text-center text-blue-600 py-2 text-sm border-t hover:bg-gray-100 cursor-pointer">View All</div>
        </div>
      </div>
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

    <section id="add-off" class="content-section">
      <h2 class="text-2xl font-bold mb-4">Add Officials</h2>
      <?php include __DIR__ . '/data/add-off.php'; ?>
    </section>

    <section id="officials" class="content-section">
      <h2 class="text-2xl font-bold mb-4">Barangay Officials</h2>
      <?php include __DIR__ . '/data/brgofficials.php'; ?>
    </section>

    <section id="request" class="content-section">
      <h2 class="text-2xl font-bold mb-4">Requests</h2>
      <?php include 'data/cert-list.php'; ?>
    </section>

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

    const notifBell = document.getElementById('notifBell');
    const notifDropdown = document.getElementById('notifDropdown');
    notifBell.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!document.getElementById('notifWrapper').contains(e.target)) {
        notifDropdown.classList.add('hidden');
      }
    });
  });
</script>

</body>
</html>
