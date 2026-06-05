<?php
// add_drama.php — Tambah Drama (Multi-step)
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Drama — DramaNest</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .add-step { animation: fadeIn 0.3s ease; }
    @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:none; } }
    .progress-text { font-size:0.82rem; color:var(--text3); margin-bottom:8px; }
    .sort-btn.active { background: var(--primary) !important; color: #fff !important; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <a href="index.php" class="navbar-brand">🎬 DramaNest</a>
  <ul class="navbar-links">
    <li><a href="index.php">🏠 Utama</a></li>
    <li><a href="drama_list.php">📋 Senarai Drama</a></li>
    <li><a href="add_drama.php" class="active">➕ Tambah Drama</a></li>
    <li><a href="actor_gallery.php">👤 Galeri Pelakon</a></li>
  </ul>
  <div class="navbar-actions">
    <div class="font-controls">
      <button id="fontDown">A-</button>
      <span id="fontSizeDisplay">15px</span>
      <button id="fontUp">A+</button>
    </div>
    <button id="themeBtn" class="btn-theme">🌙 Gelap</button>
  </div>
</nav>

<div class="container page-wrap">
  <div style="max-width:700px;margin:0 auto;">

    <h2 style="font-family:var(--font-accent);font-size:1.6rem;font-weight:800;margin-bottom:6px;">➕ Tambah Drama Baru</h2>
    <p style="color:var(--text3);font-size:0.88rem;margin-bottom:24px;">Ikut langkah-langkah di bawah untuk tambah drama ke koleksi kamu.</p>

    <!-- Step Indicator -->
    <div class="step-indicator">
      <div class="step-item active" data-step-label="1">1. Jenis</div>
      <div class="step-item"       data-step-label="2">2. Bilangan</div>
      <div class="step-item"       data-step-label="3">3. Maklumat</div>
      <div class="step-item"       data-step-label="4">4. Selesai</div>
    </div>

    <!-- STEP 1: Pilih Jenis Drama -->
    <div class="add-step card" data-step="1">
      <div class="card-header">🎭 Langkah 1: Pilih Jenis Drama</div>
      <div class="card-body">
        <p style="color:var(--text3);font-size:0.88rem;margin-bottom:16px;">Pilih jenis drama dengan klik kad atau tekan nombor pada papan kekunci.</p>
        <div class="type-select-grid">
          <?php
          $typeData = [
            1 => ['melayu',  '🇲🇾', 'Melayu'],
            2 => ['chinese', '🇨🇳', 'Chinese'],
            3 => ['indo',    '🇮🇩', 'Indonesia'],
            4 => ['jepun',   '🇯🇵', 'Jepun'],
            5 => ['korea',   '🇰🇷', 'Korea'],
            6 => ['thailand','🇹🇭', 'Thailand'],
            7 => ['taiwan',  '🇹🇼', 'Taiwan'],
          ];
          foreach ($typeData as $num => [$slug, $flag, $label]): ?>
          <div class="type-select-card" data-type="<?= $slug ?>" onclick="selectType('<?= $slug ?>', <?= $num ?>)">
            <span class="type-number"><?= $num ?></span>
            <span class="type-flag-big"><?= $flag ?></span>
            <div class="type-name-big"><?= $label ?></div>
          </div>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:24px;text-align:right;">
          <button class="btn btn-primary btn-lg" onclick="proceedFromType()">Seterusnya →</button>
        </div>
      </div>
    </div>

    <!-- STEP 2: Berapa banyak drama -->
    <div class="add-step card" data-step="2" style="display:none;">
      <div class="card-header">🔢 Langkah 2: Berapa Drama Nak Tambah?</div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">Bilangan Drama <span>*</span></label>
          <input type="number" id="howMany" class="form-control" min="1" max="50" value="1" placeholder="Contoh: 3">
          <small style="color:var(--text3);font-size:0.8rem;">Maksimum 50 drama sekaligus.</small>
        </div>
        <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:16px;">
          <button class="btn btn-outline" onclick="showStep(1)">← Kembali</button>
          <button class="btn btn-primary btn-lg" onclick="proceedFromCount()">Seterusnya →</button>
        </div>
      </div>
    </div>

    <!-- STEP 3: Isi maklumat drama -->
    <div class="add-step card" data-step="3" style="display:none;">
      <div class="card-header" id="dramaFormHeader">📝 Langkah 3: Maklumat Drama 1</div>
      <div class="card-body">

        <div class="form-group">
          <label class="form-label">Tajuk Drama <span>*</span></label>
          <input type="text" id="dramaTitle" class="form-control" placeholder="Contoh: Crash Landing on You">
        </div>

        <!-- Genre -->
        <div class="form-group">
          <label class="form-label">Genre <span>*</span> <small style="color:var(--text3);font-weight:400;">(Taip dan tekan Enter atau koma untuk tambah)</small></label>
          <div class="genre-input-wrap" id="genreWrapper">
            <div class="genre-tags">
              <input type="text" class="genre-input-field" placeholder="Taip genre...">
            </div>
            <div class="genre-suggestions"></div>
            <input type="hidden" name="genres_hidden" id="genresHidden">
          </div>
        </div>

        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">Pelakon Lelaki Utama</label>
            <input type="text" id="maleLead" class="form-control" placeholder="Nama pelakon lelaki">
          </div>
          <div class="form-group">
            <label class="form-label">Pelakon Wanita Utama</label>
            <input type="text" id="femaleLead" class="form-control" placeholder="Nama pelakon wanita">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Bilangan Episod</label>
          <input type="number" id="episodes" class="form-control" min="0" placeholder="Contoh: 16">
        </div>

        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">Tarikh Mula</label>
            <input type="date" id="startDate" class="form-control">
          </div>
          <div class="form-group">
            <label class="form-label">Tarikh Tamat</label>
            <input type="date" id="endDate" class="form-control">
          </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
          <button class="btn btn-outline" onclick="showStep(2)">← Kembali</button>
          <button class="btn btn-primary btn-lg" onclick="submitDramaForm()">💾 Simpan Drama</button>
        </div>
      </div>
    </div>

    <!-- STEP 4: Selesai -->
    <div class="add-step card" data-step="4" style="display:none;">
      <div class="card-header">✅ Selesai! Drama Berjaya Ditambah</div>
      <div class="card-body" style="text-align:center;">
        <div style="font-size:4rem;margin-bottom:16px;">🎉🐻🎬</div>
        <h3 style="font-family:var(--font-accent);color:var(--success);margin-bottom:12px;">Syabas! Semua drama telah disimpan!</h3>
        <ul id="addSummary" style="text-align:left;max-width:300px;margin:0 auto 24px;padding-left:0;list-style:none;"></ul>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
          <a href="drama_list.php" class="btn btn-primary btn-lg">📋 Lihat Senarai Drama</a>
          <a href="add_drama.php" class="btn btn-outline btn-lg" onclick="location.reload()">➕ Tambah Lagi</a>
          <a href="index.php" class="btn btn-outline btn-lg">🏠 Laman Utama</a>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modals (shared) -->
<div class="modal-overlay popup-success" id="successPopup">
  <div class="modal">
    <span class="modal-icon">🎉</span>
    <h3 id="successTitle">Berjaya!</h3>
    <p>Drama ditambah ke koleksi kamu! 🐻✨</p>
    <div class="modal-actions">
      <button class="btn btn-success" onclick="closeSuccessPopup()">Terima Kasih!</button>
    </div>
  </div>
</div>

<div class="modal-overlay popup-duplicate" id="duplicatePopup">
  <div class="modal">
    <span class="modal-icon">⚠️</span>
    <h3>Drama Sudah Ada!</h3>
    <p id="duplicateMsg">Drama ini sudah ada dalam senarai.</p>
    <div class="modal-actions">
      <button class="btn btn-warning" onclick="closeDuplicatePopup()">Faham</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<div class="bear-widget">
  <div class="bear-bubble" id="bearBubble"></div>
  <span class="bear-avatar" onclick="bearClick()">🐻</span>
</div>

<script src="assets/js/main.js"></script>
<script>
  // Init add form on load
  document.addEventListener('DOMContentLoaded', () => {
    initAddForm();
    // Keyboard shortcut: press 1-7 to select type
    document.addEventListener('keydown', (e) => {
      if (addDramaStep === 1) {
        const num = parseInt(e.key);
        if (num >= 1 && num <= 7) {
          const types = ['melayu','chinese','indo','jepun','korea','thailand','taiwan'];
          selectType(types[num-1], num);
        }
        if (e.key === 'Enter') proceedFromType();
      }
    });
  });
</script>
</body>
</html>
