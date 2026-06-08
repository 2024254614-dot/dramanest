<?php
// edit_drama.php — Edit Drama
require_once 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) { header('Location: drama_list.php'); exit; }

$row = $conn->query("SELECT * FROM dramas WHERE id=$id")->fetch_assoc();
if (!$row) { header('Location: drama_list.php'); exit; }

$flags = ['melayu'=>'🇲🇾','chinese'=>'🇨🇳','indo'=>'🇮🇩','jepun'=>'🇯🇵','korea'=>'🇰🇷','thailand'=>'🇹🇭','taiwan'=>'🇹🇼'];
$typeLabels = ['melayu'=>'Melayu','chinese'=>'Chinese','indo'=>'Indonesia','jepun'=>'Jepun','korea'=>'Korea','thailand'=>'Thailand','taiwan'=>'Taiwan'];
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Drama — DramaNest</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="navbar-brand">🎬 DramaNest</a>
  <ul class="navbar-links">
    <li><a href="index.php">🏠 Utama</a></li>
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

<div class="container page-wrap">
  <div style="max-width:700px;margin:0 auto;">

    <div style="margin-bottom:20px;">
      <a href="drama_list.php" style="color:var(--text3);font-size:0.85rem;">← Kembali ke Senarai Drama</a>
    </div>

    <div class="card">
      <div class="card-header">
        ✏️ Edit Drama: <?= htmlspecialchars($row['title']) ?>
        <span class="badge badge-<?= $row['drama_type'] ?>" style="margin-left:auto;">
          <?= ($flags[$row['drama_type']] ?? '') ?> <?= ($typeLabels[$row['drama_type']] ?? '') ?>
        </span>
      </div>
      <div class="card-body">
        <input type="hidden" id="editId" value="<?= $row['id'] ?>">

        <div class="form-group">
          <label class="form-label">Tajuk Drama <span>*</span></label>
          <input type="text" id="editTitle" class="form-control" value="<?= htmlspecialchars($row['title']) ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Genre <span>*</span> <small style="color:var(--text3);font-weight:400;">(Tekan Enter atau koma untuk tambah)</small></label>
          <div class="genre-input-wrap" id="editGenreWrapper" data-genres="<?= htmlspecialchars($row['genres']) ?>">
            <div class="genre-tags">
              <input type="text" class="genre-input-field" placeholder="Taip genre...">
            </div>
            <div class="genre-suggestions"></div>
            <input type="hidden" id="editGenresHidden">
          </div>
        </div>

        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">Pelakon Lelaki Utama</label>
            <input type="text" id="editMaleLead" class="form-control" value="<?= htmlspecialchars($row['male_lead']) ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Pelakon Wanita Utama</label>
            <input type="text" id="editFemaleLead" class="form-control" value="<?= htmlspecialchars($row['female_lead']) ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Bilangan Episod</label>
          <input type="number" id="editEpisodes" class="form-control" min="0" value="<?= $row['episodes'] ?>">
        </div>

        <div class="form-row form-row-2">
          <div class="form-group">
            <label class="form-label">Tarikh Mula</label>
            <input type="date" id="editStartDate" class="form-control" value="<?= $row['start_date'] ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Tarikh Tamat</label>
            <input type="date" id="editEndDate" class="form-control" value="<?= $row['end_date'] ?>">
          </div>
        </div>

        <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:8px;">
          <a href="drama_list.php" class="btn btn-outline">Batal</a>
          <button class="btn btn-primary btn-lg" onclick="submitEditForm()">💾 Simpan Perubahan</button>
        </div>
      </div>
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
  document.addEventListener('DOMContentLoaded', () => {
    initGenreInput('editGenreWrapper');
  });
</script>
</body>
</html>
