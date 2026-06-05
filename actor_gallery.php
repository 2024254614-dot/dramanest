<?php
// actor_gallery.php — Galeri Pelakon
require_once 'config.php';

$actors = $conn->query("SELECT * FROM actors ORDER BY drama_type, name ASC");
$actorList = [];
while ($row = $actors->fetch_assoc()) $actorList[] = $row;

$flags = ['melayu'=>'🇲🇾','chinese'=>'🇨🇳','indo'=>'🇮🇩','jepun'=>'🇯🇵','korea'=>'🇰🇷','thailand'=>'🇹🇭','taiwan'=>'🇹🇼',''=>'🌏'];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Galeri Pelakon — DramaNest</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="navbar-brand">🎬 DramaNest</a>
  <ul class="navbar-links">
    <li><a href="index.php">🏠 Utama</a></li>
    <li><a href="drama_list.php">📋 Senarai Drama</a></li>
    <li><a href="add_drama.php">➕ Tambah Drama</a></li>
    <li><a href="actor_gallery.php" class="active">👤 Galeri Pelakon</a></li>
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

  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
      <h2 style="font-family:var(--font-accent);font-size:1.6rem;font-weight:800;">👤 Galeri Pelakon</h2>
      <p style="color:var(--text3);font-size:0.88rem;"><?= count($actorList) ?> pelakon dalam koleksi</p>
    </div>
    <button class="btn btn-primary" onclick="openActorModal()">➕ Tambah Pelakon</button>
  </div>

  <!-- Filter by type -->
  <div class="type-tabs" style="margin-bottom:24px;">
    <button class="type-tab active actor-filter-btn" data-type="all" onclick="filterActors('all')">🌏 Semua</button>
    <?php foreach (['melayu','chinese','indo','jepun','korea','thailand','taiwan'] as $t): ?>
    <button class="type-tab actor-filter-btn" data-type="<?= $t ?>" onclick="filterActors('<?= $t ?>')">
      <?= $flags[$t] ?> <?= ucfirst($t) ?>
    </button>
    <?php endforeach; ?>
  </div>

  <?php if (empty($actorList)): ?>
  <div class="empty-state">
    <span class="empty-icon">👤</span>
    <h3>Tiada pelakon lagi</h3>
    <p>Tambah pelakon drama kesayangan kamu!</p>
    <br>
    <button class="btn btn-primary" onclick="openActorModal()">➕ Tambah Pelakon</button>
  </div>
  <?php else: ?>
  <div class="gallery-grid" id="actorGrid">
    <?php foreach ($actorList as $actor): ?>
    <div class="actor-card" data-type="<?= $actor['drama_type'] ?>">
      <div class="actor-photo-wrap">
        <?php if ($actor['photo_url']): ?>
          <img src="<?= htmlspecialchars($actor['photo_url']) ?>" alt="<?= htmlspecialchars($actor['name']) ?>" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
          <div class="actor-no-photo" style="display:none;">👤</div>
        <?php else: ?>
          <div class="actor-no-photo">👤</div>
        <?php endif; ?>
      </div>
      <div class="actor-info">
        <div class="actor-name">
          <?php if ($actor['official_link']): ?>
            <a href="<?= htmlspecialchars($actor['official_link']) ?>" target="_blank" class="actor-link">
              <?= htmlspecialchars($actor['name']) ?> 🔗
            </a>
          <?php else: ?>
            <?= htmlspecialchars($actor['name']) ?>
          <?php endif; ?>
        </div>
        <div class="actor-type">
          <?= ($flags[$actor['drama_type']] ?? '🌏') ?> <?= ucfirst($actor['drama_type'] ?: 'Umum') ?>
        </div>
        <div>
          <span class="actor-badge <?= $actor['role_type'] ?>">
            <?= $actor['role_type'] === 'male' ? '👨 Lelaki' : '👩 Wanita' ?>
          </span>
        </div>
        <div style="margin-top:10px;">
          <button class="btn btn-danger btn-sm" onclick="deleteActor(<?= $actor['id'] ?>,'<?= addslashes(htmlspecialchars($actor['name'])) ?>')">🗑️ Padam</button>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

</div>

<!-- Add Actor Modal -->
<div class="modal-overlay" id="actorModal">
  <div class="modal" style="max-width:520px;text-align:left;">
    <h3 style="margin-bottom:20px;font-family:var(--font-accent);">➕ Tambah Pelakon Baru</h3>

    <div class="form-group">
      <label class="form-label">Nama Pelakon <span>*</span></label>
      <input type="text" id="actorName" class="form-control" placeholder="Contoh: Hyun Bin">
    </div>

    <div class="form-group">
      <label class="form-label">URL Foto</label>
      <input type="url" id="actorPhoto" class="form-control" placeholder="https://...">
      <small style="color:var(--text3);font-size:0.78rem;">Link gambar dari internet</small>
    </div>

    <div class="form-row form-row-2">
      <div class="form-group">
        <label class="form-label">Jenis Drama</label>
        <select id="actorType" class="form-control">
          <option value="">-- Pilih --</option>
          <option value="melayu">🇲🇾 Melayu</option>
          <option value="chinese">🇨🇳 Chinese</option>
          <option value="indo">🇮🇩 Indo</option>
          <option value="jepun">🇯🇵 Jepun</option>
          <option value="korea">🇰🇷 Korea</option>
          <option value="thailand">🇹🇭 Thailand</option>
          <option value="taiwan">🇹🇼 Taiwan</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Jantina</label>
        <select id="actorRole" class="form-control">
          <option value="male">👨 Lelaki</option>
          <option value="female">👩 Wanita</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Pautan Rasmi</label>
      <input type="url" id="actorLink" class="form-control" placeholder="https://www.instagram.com/...">
      <small style="color:var(--text3);font-size:0.78rem;">Contoh: Instagram, laman web rasmi, IMDb, MyDramaList</small>
    </div>

    <div class="modal-actions">
      <button class="btn btn-primary" onclick="submitActorForm()">💾 Simpan Pelakon</button>
      <button class="btn btn-outline" onclick="closeActorModal()">Batal</button>
    </div>
  </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<div class="bear-widget">
  <div class="bear-bubble" id="bearBubble"></div>
  <span class="bear-avatar" onclick="bearClick()">🐻</span>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
