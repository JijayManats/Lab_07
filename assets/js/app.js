let currentPage = 1;
let recordsLoaded = false;
let searchTimeout = null;

function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(btn => {
    if (btn.dataset.tab === tab) {
      btn.classList.add('bg-indigo-600', 'text-white');
      btn.classList.remove('text-slate-300', 'hover:bg-slate-800');
    } else {
      btn.classList.remove('bg-indigo-600', 'text-white');
      btn.classList.add('text-slate-300', 'hover:bg-slate-800');
    }
  });

  document.querySelectorAll('.tab-section').forEach(section => {
    section.classList.toggle('hidden', section.id !== `tab-${tab}`);
  });

  if (tab === 'records' && !recordsLoaded) {
    loadUsers(1);
  }
}

function showToast(type, message) {
  const container = document.getElementById('toast-container');
  const toast = document.createElement('div');
  const styles = type === 'success' ? 'bg-emerald-500 text-white' : 'bg-red-500 text-white';
  toast.className = `${styles} px-4 py-3 rounded-lg shadow-lg text-sm font-medium flex items-start gap-2 transition-all duration-300 opacity-0 translate-x-4`;
  toast.innerHTML = `<span>${message}</span>`;
  container.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.remove('opacity-0', 'translate-x-4');
  });

  setTimeout(() => {
    toast.classList.add('opacity-0', 'translate-x-4');
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

function skeletonRows(count = 5) {
  let rows = '';
  for (let i = 0; i < count; i++) {
    rows += `
      <tr class="animate-pulse">
        <td class="px-6 py-4"><div class="h-3 w-6 bg-slate-200 rounded"></div></td>
        <td class="px-6 py-4"><div class="h-3 w-32 bg-slate-200 rounded"></div></td>
        <td class="px-6 py-4"><div class="h-3 w-24 bg-slate-200 rounded"></div></td>
        <td class="px-6 py-4"><div class="h-3 w-20 bg-slate-200 rounded"></div></td>
        <td class="px-6 py-4 text-right"><div class="h-3 w-14 bg-slate-200 rounded ml-auto"></div></td>
      </tr>`;
  }
  return rows;
}

// Show skeleton immediately so the records tab feels instant on first switch
document.getElementById('users-tbody').innerHTML = skeletonRows();

async function loadUsers(page) {
  currentPage = page;
  const tbody = document.getElementById('users-tbody');
  const emptyState = document.getElementById('empty-state');
  emptyState.classList.add('hidden');
  tbody.innerHTML = skeletonRows();

  const search = document.getElementById('search-input').value.trim();
  const url = `api/get_users.php?page=${page}&q=${encodeURIComponent(search)}`;

  try {
    const res = await fetch(url);
    const json = await res.json();
    recordsLoaded = true;
    renderUsers(json);
  } catch (err) {
    tbody.innerHTML = '';
    emptyState.classList.remove('hidden');
    emptyState.textContent = 'Failed to load users.';
    showToast('error', 'Could not load user records.');
  }
}

function renderUsers(json) {
  const tbody = document.getElementById('users-tbody');
  const emptyState = document.getElementById('empty-state');
  const info = document.getElementById('pagination-info');
  const controls = document.getElementById('pagination-controls');

  if (!json.data || json.data.length === 0) {
    tbody.innerHTML = '';
    emptyState.classList.remove('hidden');
    emptyState.textContent = 'No users found.';
    info.textContent = '';
    controls.innerHTML = '';
    return;
  }

  emptyState.classList.add('hidden');

  tbody.innerHTML = json.data.map((u, i) => `
    <tr class="hover:bg-slate-50 transition">
      <td class="px-6 py-4 text-slate-500">${(json.page - 1) * 5 + i + 1}</td>
      <td class="px-6 py-4 font-medium text-slate-800">${escapeHtml(u.firstname)} ${escapeHtml(u.lastname)}</td>
      <td class="px-6 py-4 text-slate-600">@${escapeHtml(u.username)}</td>
      <td class="px-6 py-4 text-slate-500">${formatDate(u.created_at)}</td>
      <td class="px-6 py-4 text-right">
        <button onclick="deleteUser(${u.id})" class="text-red-500 hover:text-red-700 font-medium text-xs">Delete</button>
      </td>
    </tr>
  `).join('');

  const start = (json.page - 1) * 5 + 1;
  const end = Math.min(json.page * 5, json.total);
  info.textContent = `Showing ${start}-${end} of ${json.total} users`;

  let buttons = '';
  buttons += `<button ${json.page <= 1 ? 'disabled' : ''} onclick="loadUsers(${json.page - 1})"
    class="px-3 py-1.5 rounded-lg text-sm border border-slate-300 ${json.page <= 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100'}">Prev</button>`;

  for (let p = 1; p <= json.totalPages; p++) {
    buttons += `<button onclick="loadUsers(${p})"
      class="px-3 py-1.5 rounded-lg text-sm border ${p === json.page ? 'bg-indigo-600 text-white border-indigo-600' : 'border-slate-300 text-slate-600 hover:bg-slate-100'}">${p}</button>`;
  }

  buttons += `<button ${json.page >= json.totalPages ? 'disabled' : ''} onclick="loadUsers(${json.page + 1})"
    class="px-3 py-1.5 rounded-lg text-sm border border-slate-300 ${json.page >= json.totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100'}">Next</button>`;

  controls.innerHTML = buttons;
}

function escapeHtml(str) {
  const div = document.createElement('div');
  div.textContent = str ?? '';
  return div.innerHTML;
}

function formatDate(dateStr) {
  if (!dateStr) return '—';
  const d = new Date(String(dateStr).replace(' ', 'T'));
  if (isNaN(d)) return dateStr;
  return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

async function deleteUser(id) {
  if (!confirm('Are you sure you want to delete this user?')) return;

  try {
    const res = await fetch('api/delete_user.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}`
    });
    const json = await res.json();
    showToast(json.success ? 'success' : 'error', json.message);
    if (json.success) loadUsers(currentPage);
  } catch (err) {
    showToast('error', 'Something went wrong.');
  }
}

document.getElementById('create-user-form').addEventListener('submit', async function (e) {
  e.preventDefault();
  const form = e.target;
  const btn = document.getElementById('create-submit-btn');
  const btnText = document.getElementById('create-btn-text');
  const alertBox = document.getElementById('create-alert');

  alertBox.classList.add('hidden');
  btn.disabled = true;
  btn.classList.add('opacity-70', 'cursor-not-allowed');
  btnText.innerHTML = `<svg class="animate-spin h-4 w-4 text-white inline mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Adding...`;

  try {
    const res = await fetch('api/create_user.php', {
      method: 'POST',
      body: new FormData(form)
    });
    const json = await res.json();

    alertBox.classList.remove('hidden');
    alertBox.textContent = json.message;
    alertBox.className = `mb-4 rounded-lg px-4 py-3 text-sm font-medium ${json.success ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}`;

    if (json.success) {
      form.reset();
      showToast('success', json.message);
      recordsLoaded = false; // force a fresh reload next time the Records tab is opened
    } else {
      showToast('error', json.message);
    }
  } catch (err) {
    alertBox.classList.remove('hidden');
    alertBox.textContent = 'Something went wrong. Please try again.';
    alertBox.className = 'mb-4 rounded-lg px-4 py-3 text-sm font-medium bg-red-50 text-red-700 border border-red-200';
    showToast('error', 'Something went wrong. Please try again.');
  } finally {
    btn.disabled = false;
    btn.classList.remove('opacity-70', 'cursor-not-allowed');
    btnText.textContent = 'Add User';
  }
});

document.getElementById('search-input').addEventListener('input', function () {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => loadUsers(1), 400);
});
