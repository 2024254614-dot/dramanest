<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
// index.php — DramaNest Homepage
require_once 'config.php';

// Total counter
$total = $conn->query("SELECT COUNT(*) as c FROM dramas")->fetch_assoc()['c'];

// -----------------------------------------------
// FIX: $typeCounts was used in the template but never defined.
// Build it here — one query, grouped by drama_type.
// -----------------------------------------------
$typeCounts = [];
$types      = ['melayu','chinese','indo','jepun','korea','thailand','taiwan'];
foreach ($types as $t) {
    $typeCounts[$t] = 0; // default to 0 so stat cards always render
}
$tcResult = $conn->query("SELECT drama_type, COUNT(*) as c FROM dramas GROUP BY drama_type");
while ($tcRow = $tcResult->fetch_assoc()) {
    $typeCounts[$tcRow['drama_type']] = (int) $tcRow['c'];
}

// Latest 5 dramas
$latest = $conn->query("SELECT * FROM dramas ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DramaNest 🎬</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <span class="navbar-brand">🎬 DramaNest</span>
  <ul class="navbar-links">
    <li><a href="index.php" class="active">🏠 Utama</a></li>
    <li><a href="drama_list.php">📋 Senarai Drama</a></li>
    <li><a href="add_drama.php">➕ Tambah Drama</a></li>
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

<!-- HERO -->
<div class="hero">
  <div class="datetime-display" style="position:absolute;top:20px;right:24px;font-size:0.8rem;">
    <span>📅 <span id="liveDate"></span></span>
    <span>⏰ <span id="liveClock" class="time"></span></span>
  </div>

  <h1>🎬 DramaNest</h1>
  <p>Koleksi drama peribadi kamu — Melayu, Korea, Cina dan lebih banyak lagi!</p>

  <div class="hero-stats">
    <div class="stat-card">
      <span class="stat-number" id="totalCounter" data-count="<?= $total ?>">0</span>
      <span class="stat-label">📺 Jumlah Drama</span>
    </div>
    <?php
    $flags = ['melayu'=>'🇲🇾','chinese'=>'🇨🇳','indo'=>'🇮🇩','jepun'=>'🇯🇵','korea'=>'🇰🇷','thailand'=>'🇹🇭','taiwan'=>'🇹🇼'];
    foreach ($typeCounts as $type => $count): ?>
    <div class="stat-card">
      <span class="stat-number" data-count="<?= $count ?>">0</span>
      <span class="stat-label"><?= $flags[$type] ?> <?= ucfirst($type) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- MAIN CONTENT -->
<div class="container page-wrap">

  <!-- Quick Actions -->
  <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:32px;">
    <a href="add_drama.php"     class="btn btn-primary btn-lg">➕ Tambah Drama Baru</a>
    <a href="drama_list.php"    class="btn btn-outline  btn-lg">📋 Lihat Semua Drama</a>
    <a href="actor_gallery.php" class="btn btn-outline  btn-lg">👤 Galeri Pelakon</a>
  </div>

  <!-- Latest Dramas -->
  <div class="card">
    <div class="card-header">🆕 Drama Terbaru Ditambah</div>
    <div class="card-body" style="padding:0;">
      <?php if ($latest->num_rows > 0): ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Tajuk</th>
              <th>Jenis</th>
              <th>Genre</th>
              <th>Pelakon Lelaki</th>
              <th>Pelakon Wanita</th>
              <th>Episod</th>
              <th>Tarikh Mula</th>
			  <th>Tarikh Tamat</th>
            </tr>
          </thead>
          <tbody>
          <?php $i = 1; while ($row = $latest->fetch_assoc()): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
              <td>
                <span class="badge badge-<?= $row['drama_type'] ?>">
                  <?= $flags[$row['drama_type']] ?> <?= ucfirst($row['drama_type']) ?>
                </span>
              </td>
              <td>
                <div class="genre-list">
                  <?php foreach (array_slice(explode(',', $row['genres']), 0, 3) as $g): ?>
                    <span class="genre-pill"><?= trim(htmlspecialchars($g)) ?></span>
                  <?php endforeach; ?>
                </div>
              </td>
              <td><?= htmlspecialchars($row['male_lead'])   ?: '-' ?></td>
              <td><?= htmlspecialchars($row['female_lead']) ?: '-' ?></td>
              <td><?= $row['episodes'] ?: '-' ?></td>
              <td><?= $row['start_date'] ? date('d/m/Y', strtotime($row['start_date'])) : '-' ?></td>
			  <td><?= $row['end_date'] ? date('d/m/Y', strtotime($row['end_date'])) : '-' ?></td>
            </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php else: ?>
      <div class="empty-state">
        <span class="empty-icon">📭</span>
        <h3>Tiada drama lagi</h3>
        <p>Mula tambah drama kesayangan kamu!</p>
        <br>
        <a href="add_drama.php" class="btn btn-primary">➕ Tambah Sekarang</a>
      </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- ======= MODALS ======= -->

<!-- Success Popup -->
<div class="modal-overlay popup-success" id="successPopup">
  <div class="modal">
    <span class="modal-icon">🎉</span>
    <h3 id="successTitle">Drama berjaya ditambah!</h3>
    <p>Koleksi drama kamu semakin bertambah! 🐻✨</p>
    <div class="modal-actions">
      <button class="btn btn-success" onclick="closeSuccessPopup()">Terima Kasih!</button>
    </div>
  </div>
</div>

<!-- Delete Confirm -->
<div class="modal-overlay popup-delete" id="deletePopup">
  <div class="modal">
    <span class="modal-icon">🗑️</span>
    <h3>Padam Drama?</h3>
    <p id="deleteMsg">Anda pasti ingin memadam drama ini?</p>
    <div class="modal-actions">
      <button class="btn btn-danger" id="deleteConfirmBtn">Ya, Padam</button>
      <button class="btn btn-outline" onclick="closeDeletePopup()">Batal</button>
    </div>
  </div>
</div>

<!-- Duplicate Warning -->
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

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Bear Widget -->
<div class="bear-widget">
  <div class="bear-bubble" id="bearBubble"></div>
  <span class="bear-avatar" onclick="bearClick()" title="Klik bear!">🐻</span>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
