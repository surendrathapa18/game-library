// library.js
// Handles: loading stats, filling edit form, adding/editing games
// 
document.addEventListener('DOMContentLoaded', () => {

  const welcomeUser   = document.getElementById('welcomeUser');
  const logoutBtn     = document.getElementById('logoutBtn');
  const totalGames    = document.getElementById('totalGames');
  const avgRating     = document.getElementById('avgRating');
  const wishlistCount = document.getElementById('wishlistCount');

  const form     = document.getElementById('addGameForm');
  const clearBtn = document.getElementById('clearBtn');

  if (!form) {
    console.error('addGameForm not found on page.');
    return;
  }

  async function api(path, options = {}) {
    const res = await fetch(path, options);
    return res.json();
  }

  // AUTH CHECK
  async function checkAuth() {
    const j = await api('auth.php?action=me');
    if (j.status === 'success') {
      welcomeUser.textContent = `Hi, ${j.user.username}`;
    } else {
      window.location.href = 'index.html';
    }
  }

  //  LOAD STATS + ALL GAMES 
  async function loadStats() {
    const j = await api('games.php?all=1');
    if (j.status !== 'success') {
      alert('Failed to load stats.');
      return;
    }

    totalGames.textContent = j.stats.total;
    avgRating.textContent  = j.stats.avg_rating;

    const wishlisted = j.games.filter(g => g.is_wishlist == 1).length;
    wishlistCount.textContent = wishlisted;

    const params = new URLSearchParams(window.location.search);
    const editId = params.get('editId');
    if (editId) populateForm(editId, j.games);
  }

  //  POPULATE FORM FOR EDIT 
  function populateForm(editId, games) {
    const game = games.find(g => String(g.id) === String(editId));
    if (!game) return;

    document.getElementById('gameId').value       = editId;
    document.getElementById('title').value        = game.title;
    document.getElementById('platform').value     = game.platform || '';
    document.getElementById('genre').value        = game.genre || '';
    document.getElementById('release_year').value = game.release_year || '';
    document.getElementById('rating').value       = game.rating || 0;
    document.getElementById('comment').value      = game.comment || '';
    document.getElementById('is_wishlist').checked = (game.is_wishlist == 1);

    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  //  ADD / UPDATE GAME
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('gameId').value;

    const data = new FormData();
    data.append('title',        document.getElementById('title').value.trim());
    data.append('platform',     document.getElementById('platform').value);
    data.append('genre',        document.getElementById('genre').value);

    const yearEl = document.getElementById('release_year');
    data.append('release_year', yearEl ? yearEl.value : '');

    data.append('rating',       document.getElementById('rating').value);
    data.append('comment',      document.getElementById('comment').value);

    if (document.getElementById('is_wishlist').checked) {
      data.append('is_wishlist', '1');
    }

    if (!data.get('title')) {
      alert('Title required');
      return;
    }

    // --- CREATE ---
    if (!id) {
      const res = await fetch('games.php', { method: 'POST', body: data });
      const j = await res.json();
      console.log('Create response:', j);

      if (j.status === 'success') {
        clearForm();
        await loadStats();
        alert('Game added!');
      } else {
        alert(j.message || 'Failed to add');
      }
      return;
    }

    // UPDATE 
    data.append('id', id);
    const body = new URLSearchParams();
    for (const pair of data.entries()) body.append(pair[0], pair[1]);

    const res = await fetch('games.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString()
    });

    const j = await res.json();
    console.log('Update response:', j);

    if (j.status === 'success') {
      clearForm();
      await loadStats();
      alert('Game updated!');
    } else {
      alert(j.message || 'Failed to update');
    }
  });

  // CLEAR FORM 
  clearBtn.addEventListener('click', clearForm);

  function clearForm() {
    document.getElementById('gameId').value = '';
    form.reset();
  }

  //  LOGOUT 
  logoutBtn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('action', 'logout');

    const res = await fetch('auth.php', { method: 'POST', body: fd });
    const j = await res.json();

    if (j.status === 'success') {
      window.location.href = 'index.html';
    }
  });

  //  INIT 
  (async () => {
    await checkAuth();
    await loadStats();
  })();
});
