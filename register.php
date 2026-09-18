<?php
require 'includes/initialize.php';
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
$alert = get_alert();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register - Lab App</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 px-4 py-10">
  <div class="w-full max-w-md">
    <div class="text-center mb-6">
      <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-indigo-500 shadow-lg mb-3">
        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
        </svg>
      </div>
      <h1 class="text-2xl font-bold text-white">Create an account</h1>
      <p class="text-slate-400 text-sm mt-1">Sign up to get started</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl p-8">
      <?php if ($alert): ?>
        <div class="mb-4 rounded-lg px-4 py-3 text-sm font-medium <?= $alert['type'] === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
          <?= h($alert['message']) ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="register_process.php" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Firstname</label>
            <input type="text" name="firstname" required autofocus
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
            <label class="block text-sm font-medium text-slate-700 mb-1">Confirm</label>
            <input type="password" name="confirm_password" required
              class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
          </div>
        </div>
        <button type="submit"
          class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg transition shadow-md hover:shadow-lg">
          Create Account
        </button>
      </form>

      <p class="text-center text-sm text-slate-500 mt-6">
        Already have an account?
        <a href="login.php" class="text-indigo-600 font-semibold hover:underline">Sign in</a>
      </p>
    </div>
  </div>
</body>
</html>
