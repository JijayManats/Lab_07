<?php
require 'includes/initialize.php';
require 'includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Lab App</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">

<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2 w-72"></div>

<div class="flex min-h-screen">
  <!-- Sidebar -->
  <aside class="w-64 bg-slate-900 text-slate-200 flex flex-col shrink-0">
    <div class="px-6 py-5 border-b border-slate-800">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-indigo-500 flex items-center justify-center font-bold text-white">L</div>
        <div>
          <p class="font-semibold text-white leading-tight">Lab App</p>
          <p class="text-xs text-slate-400">User Management</p>
        </div>
      </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1">
      <button data-tab="create" onclick="switchTab('create')"
        class="tab-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition bg-indigo-600 text-white">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create User
      </button>
      <button data-tab="records" onclick="switchTab('records')"
        class="tab-btn w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition text-slate-300 hover:bg-slate-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
        </svg>
        User Records
      </button>
    </nav>

    <div class="px-4 py-4 border-t border-slate-800">
      <div class="flex items-center gap-3 mb-3">
        <div class="w-9 h-9 rounded-full bg-indigo-500 flex items-center justify-center text-sm font-bold text-white">
          <?= strtoupper(substr($_SESSION['fullname'] ?? 'U', 0, 1)) ?>
        </div>
        <div class="min-w-0">
          <p class="text-sm font-medium text-white truncate"><?= h($_SESSION['fullname']) ?></p>
          <p class="text-xs text-slate-400 truncate">@<?= h($_SESSION['username']) ?></p>
        </div>
      </div>
      <a href="logout.php" class="flex items-center justify-center gap-2 w-full px-3 py-2 rounded-lg text-sm font-medium text-slate-300 bg-slate-800 hover:bg-slate-700 transition">
        Logout
      </a>
    </div>
  </aside>

  <!-- Main -->
  <main class="flex-1 p-8">

    <!-- Create User Tab -->
    <section id="tab-create" class="tab-section">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Create User</h1>
        <p class="text-slate-500 text-sm mt-1">Add a new user record to the system</p>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 max-w-xl">
        <div id="create-alert" class="hidden mb-4 rounded-lg px-4 py-3 text-sm font-medium"></div>
        <form id="create-user-form" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Firstname</label>
              <input type="text" name="firstname" required
                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Lastname</label>
              <input type="text" name="lastname" required
                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
            <input type="text" name="username" required
              class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
              <input type="password" name="password" required
                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Confirm Password</label>
              <input type="password" name="confirm_password" required
                class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>
          </div>
          <button type="submit" id="create-submit-btn"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
            <span id="create-btn-text">Add User</span>
          </button>
        </form>
      </div>
    </section>

    <!-- Records Tab -->
    <section id="tab-records" class="tab-section hidden">
      <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
          <h1 class="text-2xl font-bold text-slate-800">User Records</h1>
          <p class="text-slate-500 text-sm mt-1">All registered users in the system</p>
        </div>
        <div class="relative">
          <input type="text" id="search-input" placeholder="Search users..."
            class="pl-10 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm w-64">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-left text-slate-500 uppercase text-xs tracking-wide">
              <th class="px-6 py-3 font-semibold">#</th>
              <th class="px-6 py-3 font-semibold">Name</th>
              <th class="px-6 py-3 font-semibold">Username</th>
              <th class="px-6 py-3 font-semibold">Date Created</th>
              <th class="px-6 py-3 font-semibold text-right">Actions</th>
            </tr>
          </thead>
          <tbody id="users-tbody" class="divide-y divide-slate-100"></tbody>
        </table>
        <div id="empty-state" class="hidden py-12 text-center text-slate-400 text-sm">No users found.</div>
      </div>

      <div class="flex items-center justify-between mt-4 flex-wrap gap-3">
        <p id="pagination-info" class="text-sm text-slate-500"></p>
        <div id="pagination-controls" class="flex items-center gap-1"></div>
      </div>
    </section>
  </main>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>
