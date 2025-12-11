// library_list.js – FINAL VERSION


document.addEventListener('DOMContentLoaded', () => {

  const welcomeUser = document.getElementById('welcomeUser');
  const logoutBtn = document.getElementById('logoutBtn');
  const gamesList = document.getElementById('gamesList');

  const searchInput = document.getElementById('search');
  const platformFilter = document.getElementById('filterPlatform');
  const genreFilter = document.getElementById('filterGenre');
  const yearFilter = document.getElementById('filterYear');
  const ratingFilter = document.getElementById('filterRating');  // ⭐ FIXED
  const sortBy = document.getElementById('sortBy');
  const showWishlist = document.getElementById('showWishlist');

  async function api(path, options = {}) {
    const res = await fetch(path, options);
    return res.json();
  }

  async function checkAuth() {
    const j = await api('auth.php?action=me');
    if (j.status === 'success') {
      welcomeUser.textContent = `Hi, ${j.user.username}`;
    } else {
      window.location.href = 'index.html';
    }
  }

  async function loadFilters() {
    const j = await api('games.php?filters=1');
    if (j.status !== 'success') return;

    platformFilter.innerHTML = `<option value="">Platform</option>`;
    j.platforms.forEach(p => { if (p) platformFilter.innerHTML += `<option value="${p}">${p}</option>`; });

    genreFilter.innerHTML = `<option value="">Genre</option>`;
    j.genres.forEach(g => { if (g) genreFilter.innerHTML += `<option value="${g}">${g}</option>`; });

    yearFilter.innerHTML = `<option value="">Year</option>`;
    j.years.forEach(y => { if (y) yearFilter.innerHTML += `<option value="${y}">${y}</option>`; });
  }

  async function loadGames() {
    const params = new URLSearchParams();

    if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
    if (platformFilter.value) params.set('platform', platformFilter.value);
    if (genreFilter.value) params.set('genre', genreFilter.value);
    if (yearFilter.value) params.set('year', yearFilter.value);

    // ⭐ RATING FILTER WORKS
    if (ratingFilter.value) params.set('rating', ratingFilter.value);

    if (showWishlist.checked) params.set('is_wishlist', '1');

    params.set('sort', sortBy.value);

    const j = await api('games.php?' + params.toString());
    if (j.status === 'success') renderGames(j.games);
  }

  function renderGames(games) {
    gamesList.innerHTML = '';

    if (games.length === 0) {
      gamesList.innerHTML = `<div class="card">No games found.</div>`;
      return;
    }

    for (const g of games) {
      const div = document.createElement('div');
      div.className = "game-card card";

      const ratingStars = "★".repeat(g.rating) + "☆".repeat(5 - g.rating);

      div.innerHTML = `
        <h4>${g.title}</h4>
        <p><strong>Platform:</strong> ${g.platform || '—'}</p>
        <p><strong>Genre:</strong> ${g.genre || '—'}</p>
        <p><strong>Year:</strong> ${g.release_year || '—'}</p>
        <p><strong>Rating:</strong> ${ratingStars} (${g.rating}/5)</p>
        <p class="muted">${g.comment || ''}</p>

        <div class="game-actions">
          <a href="library.html?editId=${g.id}" class="button" style="background:#4CAF50;">Edit</a>
          <button data-delete="${g.id}">Delete</button>
        </div>
      `;

      gamesList.appendChild(div);
    }
  }

  gamesList.addEventListener('click', async (e) => {
    const deleteId = e.target.dataset.delete;
    if (!deleteId) return;

    if (!confirm('Delete this game?')) return;

    const res = await fetch('games.php', {
      method: 'DELETE',
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify({ id: deleteId })
    });

    const j = await res.json();
    if (j.status === 'success') loadGames();
  });

  let timer = null;
  searchInput.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(loadGames, 250);
  });

  platformFilter.addEventListener('change', loadGames);
  genreFilter.addEventListener('change', loadGames);
  yearFilter.addEventListener('change', loadGames);
  ratingFilter.addEventListener('change', loadGames);
  sortBy.addEventListener('change', loadGames);
  showWishlist.addEventListener('change', loadGames);

  logoutBtn.addEventListener('click', async () => {
    const fd = new FormData();
    fd.append('action', 'logout');
    await fetch('auth.php', { method: 'POST', body: fd });
    window.location.href = 'index.html';
  });

  (async () => {
    await checkAuth();
    await loadFilters();
    await loadGames();
  })();
});
