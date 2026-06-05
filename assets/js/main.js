/* =============================================
   DramaNest — assets/js/main.js
   ============================================= */

/* ---- Theme ---- */
function initTheme() {
  const saved = localStorage.getItem('dramaNestTheme') || 'light';
  document.documentElement.setAttribute('data-theme', saved);
  updateThemeBtn(saved);
}

function toggleTheme() {
  const current = document.documentElement.getAttribute('data-theme');
  const next = current === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('dramaNestTheme', next);
  updateThemeBtn(next);
}

function updateThemeBtn(theme) {
  const btn = document.getElementById('themeBtn');
  if (btn) btn.textContent = theme === 'dark' ? '☀️ Terang' : '🌙 Gelap';
}

/* ---- Font Size ---- */
let currentFontSize = parseFloat(localStorage.getItem('dramaNestFont') || '15');

function initFontSize() {
  document.documentElement.style.setProperty('--font-size-base', currentFontSize + 'px');
  const el = document.getElementById('fontSizeDisplay');
  if (el) el.textContent = currentFontSize + 'px';
}

function changeFontSize(delta) {
  currentFontSize = Math.min(22, Math.max(11, currentFontSize + delta));
  localStorage.setItem('dramaNestFont', currentFontSize);
  document.documentElement.style.setProperty('--font-size-base', currentFontSize + 'px');
  const el = document.getElementById('fontSizeDisplay');
  if (el) el.textContent = currentFontSize + 'px';
}

/* ---- Live Date & Time ---- */
function startClock() {
  const el = document.getElementById('liveClock');
  const dateEl = document.getElementById('liveDate');
  if (!el && !dateEl) return;

  const days   = ['Ahad','Isnin','Selasa','Rabu','Khamis','Jumaat','Sabtu'];
  const months = ['Jan','Feb','Mac','Apr','Mei','Jun','Jul','Ogos','Sep','Okt','Nov','Dis'];

  function tick() {
    const now = new Date();
    if (el) {
      const h = String(now.getHours()).padStart(2,'0');
      const m = String(now.getMinutes()).padStart(2,'0');
      const s = String(now.getSeconds()).padStart(2,'0');
      el.textContent = `${h}:${m}:${s}`;
    }
    if (dateEl) {
      const d = days[now.getDay()];
      const dd = now.getDate();
      const mo = months[now.getMonth()];
      const yr = now.getFullYear();
      dateEl.textContent = `${d}, ${dd} ${mo} ${yr}`;
    }
  }
  tick();
  setInterval(tick, 1000);
}

/* ---- Bear Widget ---- */
const bearMessages = [
  '🐾 Hai! Selamat datang ke DramaNest!',
  '🍵 Jangan lupa minum air ya!',
  '📺 Hari ni nak tengok drama apa?',
  '✨ Koleksi drama kamu cantik!',
  '🎉 Semangat update senarai!',
];

function initBear() {
  const bubble = document.getElementById('bearBubble');
  if (!bubble) return;
  setTimeout(() => showBearMessage(bearMessages[0]), 800);
}

function showBearMessage(msg) {
  const bubble = document.getElementById('bearBubble');
  if (!bubble) return;
  bubble.textContent = msg;
  bubble.classList.add('show');
  setTimeout(() => bubble.classList.remove('show'), 3500);
}

function bearClick() {
  const msg = bearMessages[Math.floor(Math.random() * bearMessages.length)];
  showBearMessage(msg);
}

/* ---- Genre Tag Input ---- */
function initGenreInput(wrapperId) {
  const wrapper = document.getElementById(wrapperId);
  if (!wrapper) return;

  const tagsContainer = wrapper.querySelector('.genre-tags');
  const input         = wrapper.querySelector('.genre-input-field');
  const suggestions   = wrapper.querySelector('.genre-suggestions');
  const hiddenInput   = wrapper.querySelector('input[type="hidden"]');

  let currentTags = [];

  // Load saved genres
  let savedGenres = [];
  fetch('api/get_genres.php')
    .then(r => r.json())
    .then(data => { savedGenres = data.genres || []; })
    .catch(() => {});

  function renderTags() {
    // Remove old tags (keep input)
    tagsContainer.querySelectorAll('.genre-tag').forEach(t => t.remove());
    currentTags.forEach((tag, i) => {
      const pill = document.createElement('span');
      pill.className = 'genre-tag';
      pill.innerHTML = `${tag} <button type="button" onclick="removeGenreTag('${wrapperId}',${i})">×</button>`;
      tagsContainer.insertBefore(pill, input);
    });
    if (hiddenInput) hiddenInput.value = currentTags.join(', ');
  }

  window[`removeGenreTag_${wrapperId}`] = (idx) => {
    currentTags.splice(idx, 1);
    renderTags();
  };

  window[`addGenreTag_${wrapperId}`] = (tag) => {
    tag = tag.trim();
    if (tag && !currentTags.includes(tag)) {
      currentTags.push(tag);
      renderTags();
      // Save new genre
      fetch('api/get_genres.php?save=' + encodeURIComponent(tag));
      if (!savedGenres.includes(tag)) savedGenres.push(tag);
    }
    input.value = '';
    suggestions.classList.remove('show');
    input.focus();
  };

  window[`getGenreTags_${wrapperId}`] = () => currentTags;

  // Pre-populate if editing
  const preload = wrapper.dataset.genres;
  if (preload) {
    currentTags = preload.split(',').map(s => s.trim()).filter(Boolean);
    renderTags();
  }

  input.addEventListener('input', () => {
    const val = input.value.toLowerCase();
    if (!val) { suggestions.classList.remove('show'); return; }
    const matches = savedGenres.filter(g => g.toLowerCase().includes(val) && !currentTags.includes(g));
    if (matches.length === 0) { suggestions.classList.remove('show'); return; }
    suggestions.innerHTML = matches.slice(0,8).map(g =>
      `<div class="genre-suggestion-item" onclick="addGenreTag_${wrapperId}('${g}')">${g}</div>`
    ).join('');
    suggestions.classList.add('show');
  });

  input.addEventListener('keydown', (e) => {
    if ((e.key === 'Enter' || e.key === ',') && input.value.trim()) {
      e.preventDefault();
      window[`addGenreTag_${wrapperId}`](input.value.replace(',','').trim());
    }
    if (e.key === 'Backspace' && !input.value && currentTags.length) {
      currentTags.pop();
      renderTags();
    }
  });

  tagsContainer.addEventListener('click', () => input.focus());
  document.addEventListener('click', (e) => {
    if (!wrapper.contains(e.target)) suggestions.classList.remove('show');
  });
}

// Global wrapper for removeGenreTag called from HTML
function removeGenreTag(wrapperId, idx) {
  if (typeof window[`removeGenreTag_${wrapperId}`] === 'function')
    window[`removeGenreTag_${wrapperId}`](idx);
}

function addGenreTag(wrapperId, tag) {
  if (typeof window[`addGenreTag_${wrapperId}`] === 'function')
    window[`addGenreTag_${wrapperId}`](tag);
}

/* ---- Success Popup ---- */
function showSuccessPopup(title) {
  const overlay = document.getElementById('successPopup');
  const titleEl = document.getElementById('successTitle');
  if (!overlay) return;
  if (titleEl) titleEl.textContent = `"${title}" berjaya ditambah! 🎊`;
  overlay.classList.add('show');
  launchConfetti();
  showBearMessage('🎉 Drama baru ditambah! Syabas!');
  setTimeout(() => overlay.classList.remove('show'), 3000);
}

function closeSuccessPopup() {
  const overlay = document.getElementById('successPopup');
  if (overlay) overlay.classList.remove('show');
}

/* ---- Duplicate Popup ---- */
function showDuplicatePopup(title) {
  const overlay = document.getElementById('duplicatePopup');
  const msgEl   = document.getElementById('duplicateMsg');
  if (!overlay) return;
  if (msgEl) msgEl.textContent = `"${title}" sudah ada dalam senarai. Cuba nama lain?`;
  overlay.classList.add('show');
}

function closeDuplicatePopup() {
  const overlay = document.getElementById('duplicatePopup');
  if (overlay) overlay.classList.remove('show');
}

/* ---- Delete Confirm ---- */
function confirmDelete(id, title) {
  const overlay = document.getElementById('deletePopup');
  const msgEl   = document.getElementById('deleteMsg');
  const btn     = document.getElementById('deleteConfirmBtn');
  if (!overlay) return;
  if (msgEl) msgEl.textContent = `Anda pasti ingin memadam "${title}"?`;
  if (btn)  btn.onclick = () => executeDelete(id);
  overlay.classList.add('show');
}

function closeDeletePopup() {
  const overlay = document.getElementById('deletePopup');
  if (overlay) overlay.classList.remove('show');
}

function executeDelete(id) {
  fetch('api/delete_drama.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id })
  })
  .then(r => r.json())
  .then(data => {
    closeDeletePopup();
    if (data.success) {
      showToast('Drama berjaya dipadam!', 'success');
      setTimeout(() => location.reload(), 1000);
    } else {
      showToast('Gagal memadam drama.', 'error');
    }
  });
}

/* ---- Confetti ---- */
function launchConfetti() {
  const colors = ['#0891b2','#06b6d4','#67e8f9','#0284c7','#f59e0b','#10b981','#ec4899'];
  const burst  = document.createElement('div');
  burst.className = 'confetti-burst';
  document.body.appendChild(burst);

  for (let i = 0; i < 60; i++) {
    const piece = document.createElement('div');
    piece.className = 'confetti-piece';
    piece.style.cssText = `
      left: ${Math.random()*100}%;
      top:  ${Math.random()*30}%;
      background: ${colors[Math.floor(Math.random()*colors.length)]};
      width:  ${6+Math.random()*8}px;
      height: ${6+Math.random()*8}px;
      border-radius: ${Math.random() > 0.5 ? '50%' : '2px'};
      animation-duration: ${1.2+Math.random()*1.5}s;
      animation-delay: ${Math.random()*0.5}s;
    `;
    burst.appendChild(piece);
  }

  setTimeout(() => burst.remove(), 2500);
}

/* ---- Toast ---- */
function showToast(message, type = 'info') {
  const container = document.getElementById('toastContainer');
  if (!container) return;
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  const icons = { success:'✅', error:'❌', warning:'⚠️', info:'ℹ️' };
  toast.textContent = (icons[type] || '') + ' ' + message;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), 3100);
}

/* ---- Check Duplicate ---- */
async function checkDuplicate(title, type) {
  if (!title.trim()) return false;
  const r = await fetch(`api/check_duplicate.php?title=${encodeURIComponent(title)}&type=${type}`);
  const d = await r.json();
  return d.exists;
}

/* ---- Drama Sorting ---- */
function sortTable(sortBy) {
  const tbody = document.getElementById('dramaTableBody');
  if (!tbody) return;
  const rows = Array.from(tbody.querySelectorAll('tr[data-title]'));

  rows.sort((a, b) => {
    switch (sortBy) {
      case 'male':
        return (a.dataset.male || '').localeCompare(b.dataset.male || '');
      case 'female':
        return (a.dataset.female || '').localeCompare(b.dataset.female || '');
      case 'alpha':
        return (a.dataset.title || '').localeCompare(b.dataset.title || '');
      case 'year':
        return new Date(a.dataset.start || 0) - new Date(b.dataset.start || 0);
      default:
        return 0;
    }
  });

  tbody.innerHTML = '';
  rows.forEach(r => tbody.appendChild(r));

  // Update active button
  document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
  const activeBtn = document.querySelector(`.sort-btn[data-sort="${sortBy}"]`);
  if (activeBtn) activeBtn.classList.add('btn-primary');
}

/* ---- Type Filter for drama_list ---- */
function filterByType(type) {
  document.querySelectorAll('.drama-section').forEach(section => {
    if (type === 'all' || section.dataset.type === type) {
      section.style.display = '';
    } else {
      section.style.display = 'none';
    }
  });

  document.querySelectorAll('.type-tab').forEach(tab => {
    tab.classList.toggle('active', tab.dataset.type === type);
  });

  document.querySelectorAll('.counter-mini-card').forEach(card => {
    card.classList.toggle('active', card.dataset.type === type);
  });
}

/* ---- Format Date Display ---- */
function formatDate(dateStr) {
  if (!dateStr) return '-';
  const [y, m, d] = dateStr.split('-');
  return `${d}/${m}/${y}`;
}

/* ---- Add Drama — Multi-step Form ---- */
let addDramaStep = 1;
let howManyDramas = 1;
let currentDramaIndex = 0;
let selectedType = '';
let addedDramas = [];

function initAddForm() {
  showStep(1);
}

function showStep(step) {
  addDramaStep = step;
  document.querySelectorAll('.add-step').forEach(s => {
    s.style.display = s.dataset.step == step ? '' : 'none';
  });

  document.querySelectorAll('.step-item').forEach((s, i) => {
    s.classList.remove('active','done');
    if (i + 1 < step) s.classList.add('done');
    if (i + 1 === step) s.classList.add('active');
  });
}

function selectType(type, num) {
  selectedType = type;
  document.querySelectorAll('.type-select-card').forEach(c => c.classList.remove('selected'));
  const selected = document.querySelector(`.type-select-card[data-type="${type}"]`);
  if (selected) selected.classList.add('selected');
}

function proceedFromType() {
  if (!selectedType) { showToast('Sila pilih jenis drama!', 'warning'); return; }
  showStep(2);
}

function proceedFromCount() {
  const val = parseInt(document.getElementById('howMany').value);
  if (!val || val < 1 || val > 50) { showToast('Sila masukkan bilangan 1-50!', 'warning'); return; }
  howManyDramas = val;
  currentDramaIndex = 0;
  updateDramaFormHeader();
  showStep(3);
  initGenreInput('genreWrapper');
}

function updateDramaFormHeader() {
  const el = document.getElementById('dramaFormHeader');
  if (el) el.textContent = `Drama ${currentDramaIndex + 1} daripada ${howManyDramas}`;
}

async function submitDramaForm() {
  const title      = document.getElementById('dramaTitle').value.trim();
  const maleLead   = document.getElementById('maleLead').value.trim();
  const femaleLead = document.getElementById('femaleLead').value.trim();
  const episodes   = document.getElementById('episodes').value || 0;
  const startDate  = document.getElementById('startDate').value;
  const endDate    = document.getElementById('endDate').value;
  const genres     = (window['getGenreTags_genreWrapper'] && window['getGenreTags_genreWrapper']()) || [];

  if (!title) { showToast('Sila masukkan tajuk drama!', 'warning'); return; }
  if (genres.length === 0) { showToast('Sila masukkan sekurang-kurangnya satu genre!', 'warning'); return; }

  // Check duplicate
  const isDup = await checkDuplicate(title, selectedType);
  if (isDup) { showDuplicatePopup(title); return; }

  const payload = { title, drama_type: selectedType, genres: genres.join(', '), male_lead: maleLead, female_lead: femaleLead, episodes: parseInt(episodes), start_date: startDate, end_date: endDate };

  const res = await fetch('api/add_drama.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await res.json();

  if (data.success) {
    addedDramas.push(title);
    showSuccessPopup(title);

    currentDramaIndex++;
    if (currentDramaIndex < howManyDramas) {
      clearDramaForm();
      updateDramaFormHeader();
    } else {
      showStep(4);
      const summary = document.getElementById('addSummary');
      if (summary) summary.innerHTML = addedDramas.map(t => `<li>✅ ${t}</li>`).join('');
    }
  } else {
    showToast('Gagal menambah drama: ' + (data.error || ''), 'error');
  }
}

function clearDramaForm() {
  ['dramaTitle','maleLead','femaleLead','episodes','startDate','endDate'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });
  // Reset genres
  const wrapper = document.getElementById('genreWrapper');
  if (wrapper) {
    wrapper.querySelectorAll('.genre-tag').forEach(t => t.remove());
    const hidden = wrapper.querySelector('input[type="hidden"]');
    if (hidden) hidden.value = '';
    if (window['getGenreTags_genreWrapper']) {
      // Hacky but works: reinit
      initGenreInput('genreWrapper');
    }
  }
}

/* ---- Edit Drama ---- */
async function submitEditForm() {
  const id         = document.getElementById('editId').value;
  const title      = document.getElementById('editTitle').value.trim();
  const maleLead   = document.getElementById('editMaleLead').value.trim();
  const femaleLead = document.getElementById('editFemaleLead').value.trim();
  const episodes   = document.getElementById('editEpisodes').value || 0;
  const startDate  = document.getElementById('editStartDate').value;
  const endDate    = document.getElementById('editEndDate').value;
  const genres     = (window['getGenreTags_editGenreWrapper'] && window['getGenreTags_editGenreWrapper']()) || [];

  if (!title)          { showToast('Sila masukkan tajuk!', 'warning'); return; }
  if (!genres.length)  { showToast('Sila masukkan genre!', 'warning'); return; }

  const payload = { id, title, genres: genres.join(', '), male_lead: maleLead, female_lead: femaleLead, episodes: parseInt(episodes), start_date: startDate, end_date: endDate };

  const res  = await fetch('api/update_drama.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
  const data = await res.json();

  if (data.success) {
    showToast('Drama berjaya dikemaskini!', 'success');
    setTimeout(() => window.location.href = 'drama_list.php', 1200);
  } else {
    showToast('Gagal kemaskini: ' + (data.error || ''), 'error');
  }
}

/* ---- Actor Gallery ---- */
async function submitActorForm() {
  const name    = document.getElementById('actorName').value.trim();
  const photo   = document.getElementById('actorPhoto').value.trim();
  const type    = document.getElementById('actorType').value;
  const link    = document.getElementById('actorLink').value.trim();
  const role    = document.getElementById('actorRole').value;

  if (!name) { showToast('Sila masukkan nama pelakon!', 'warning'); return; }

  const payload = { name, photo_url: photo, drama_type: type, official_link: link, role_type: role };
  const res  = await fetch('api/save_actor.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload) });
  const data = await res.json();

  if (data.success) {
    closeActorModal();
    showToast('Pelakon berjaya ditambah!', 'success');
    setTimeout(() => location.reload(), 1000);
  } else {
    showToast('Gagal: ' + (data.error || ''), 'error');
  }
}

function openActorModal() {
  document.getElementById('actorModal').classList.add('show');
}

function closeActorModal() {
  document.getElementById('actorModal').classList.remove('show');
  ['actorName','actorPhoto','actorLink'].forEach(id => { const el=document.getElementById(id); if(el) el.value=''; });
}

async function deleteActor(id, name) {
  if (!confirm(`Padam pelakon "${name}"?`)) return;
  const res  = await fetch('api/delete_actor.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({id}) });
  const data = await res.json();
  if (data.success) { showToast('Pelakon dipadam!','success'); setTimeout(()=>location.reload(),900); }
}

/* ---- Filter Actors by type ---- */
function filterActors(type) {
  document.querySelectorAll('.actor-card').forEach(card => {
    card.style.display = (type === 'all' || card.dataset.type === type) ? '' : 'none';
  });
  document.querySelectorAll('.actor-filter-btn').forEach(b => b.classList.remove('btn-primary'));
  const active = document.querySelector(`.actor-filter-btn[data-type="${type}"]`);
  if (active) active.classList.add('btn-primary');
}

/* ---- Animate counter numbers ---- */
function animateCounter(el, target, duration = 1200) {
  let start = 0;
  const step = (timestamp) => {
    if (!start) start = timestamp;
    const progress = Math.min((timestamp - start) / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    el.textContent = Math.round(eased * target);
    if (progress < 1) requestAnimationFrame(step);
    else el.textContent = target;
  };
  requestAnimationFrame(step);
}

function initCounters() {
  document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseInt(el.dataset.count);
    if (!isNaN(target)) animateCounter(el, target);
  });
}

/* ---- Init on page load ---- */
document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initFontSize();
  startClock();
  initBear();
  initCounters();

  // Expose theme toggle globally
  const themeBtn = document.getElementById('themeBtn');
  if (themeBtn) themeBtn.addEventListener('click', toggleTheme);

  const fontUp   = document.getElementById('fontUp');
  const fontDown = document.getElementById('fontDown');
  if (fontUp)   fontUp.addEventListener('click', () => changeFontSize(1));
  if (fontDown) fontDown.addEventListener('click', () => changeFontSize(-1));

  // Close modals on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) overlay.classList.remove('show');
    });
  });
});
