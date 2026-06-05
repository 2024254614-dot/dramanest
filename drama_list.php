<?php
// drama_list.php — Senarai Drama dengan sort & filter
require_once 'config.php';

$types = ['melayu','chinese','indo','jepun','korea','thailand','taiwan'];
$flags = ['melayu'=>'🇲🇾','chinese'=>'🇨🇳','indo'=>'🇮🇩','jepun'=>'🇯🇵','korea'=>'🇰🇷','thailand'=>'🇹🇭','taiwan'=>'🇹🇼'];

// Fetch all dramas grouped by type
$allDramas = [];
foreach ($types as $t) {
    $r = $conn->query("SELECT * FROM dramas WHERE drama_type='$t' ORDER BY title ASC");
    $allDramas[$t] = [];
    while ($row = $r->fetch_assoc()) $allDramas[$t][] = $row;
}

$totalCount = array_sum(array_map('count', $allDramas));
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Senarai Drama — DramaNest</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .section-title {
      font-family: var(--font-accent);
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--text);
      margin: 32px 0 12px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .section-title span.count {
      font-size: 0.8rem;
      background: var(--primary);
      color: #fff;
      padding: 2px 10px;
      border-radius: 99px;
      font-weight: 700;
    }
    .genre-overflow { margin-top: 4px; }
    .no-drama { color: var(--text3); font-size: 0.88rem; padding: 20px; text-align: center; }
  </style>
</head>
<body>

<nav class="navbar">
  <a href="index.php" class="navbar-brand">🎬 DramaNest</a>
  <ul class="navbar-links">
    <li><a href="index.php">🏠 Utama</a></li>
    <li><a href="drama_list.php" class="active">📋 Senarai Drama</a></li>
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

  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:24px;">
    <div>
      <h2 style="font-family:var(--font-accent);font-size:1.6rem;font-weight:800;">📋 Senarai Drama</h2>
      <p style="color:var(--text3);font-size:0.88rem;">Jumlah: <strong><?= $totalCount ?></strong> drama</p>
    </div>
    <a href="add_drama.php" class="btn btn-primary">➕ Tambah Drama</a>
  </div>

  <!-- Type tabs (counter mini-cards) -->
  <div class="counter-grid">
    <div class="counter-mini-card active" data-type="all" onclick="filterByType('all')">
      <span class="type-flag">🌏</span>
      <div class="type-name">SEMUA</div>
      <div class="type-count" data-count="<?= $totalCount ?>">0</div>
    </div>
    <?php foreach ($types as $t): ?>
    <div class="counter-mini-card" data-type="<?= $t ?>" onclick="filterByType('<?= $t ?>')">
      <span class="type-flag"><?= $flags[$t] ?></span>
      <div class="type-name"><?= strtoupper($t) ?></div>
      <div class="type-count" data-count="<?= count($allDramas[$t]) ?>">0</div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Sort Bar -->
  <div class="filter-bar">
    <label>Susun mengikut:</label>
    <button class="btn btn-outline btn-sm sort-btn" data-sort="alpha"  onclick="sortTable('alpha')">🔤 A–Z Tajuk</button>
    <button class="btn btn-outline btn-sm sort-btn" data-sort="male"   onclick="sortTable('male')">👨 Pelakon Lelaki</button>
    <button class="btn btn-outline btn-sm sort-btn" data-sort="female" onclick="sortTable('female')">👩 Pelakon Wanita</button>
    <button class="btn btn-outline btn-sm sort-btn" data-sort="year"   onclick="sortTable('year')">📅 Tahun (Lama→Baru)</button>
  </div>

  <!-- Drama Sections per type -->
  <?php foreach ($types as $t):
    $dramas = $allDramas[$t];
  ?>
  <div class="drama-section" data-type="<?= $t ?>">
    <div class="section-title">
      <?= $flags[$t] ?> Drama <?= ucfirst($t) ?>
      <span class="count"><?= count($dramas) ?></span>
    </div>

    <?php if (empty($dramas)): ?>
      <div class="card"><div class="card-body no-drama">Tiada drama <?= ucfirst($t) ?> lagi. <a href="add_drama.php">Tambah sekarang</a></div></div>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Tajuk</th>
            <th>Genre</th>
            <th>Pelakon Lelaki</th>
            <th>Pelakon Wanita</th>
            <th>Episod</th>
            <th>Tarikh Mula</th>
            <th>Tarikh Tamat</th>
            <th>Tindakan</th>
          </tr>
        </thead>
        <tbody id="dramaTableBody">
        <?php $i=1; foreach ($dramas as $row):
          $genres = array_map('trim', explode(',', $row['genres']));
        ?>
          <tr data-title="<?= htmlspecialchars($row['title']) ?>"
              data-male="<?= htmlspecialchars($row['male_lead']) ?>"
              data-female="<?= htmlspecialchars($row['female_lead']) ?>"
              data-start="<?= $row['start_date'] ?>">
            <td><?= $i++ ?></td>
            <td><strong><?= htmlspecialchars($row['title']) ?></strong></td>
            <td>
              <div class="genre-list">
                <?php foreach(array_slice($genres,0,3) as $g): ?>
                  <span class="genre-pill"><?= htmlspecialchars($g) ?></span>
                <?php endforeach; ?>
              </div>
              <?php if (count($genres) > 3): ?>
              <div class="genre-overflow genre-list" style="margin-top:4px;">
                <?php foreach(array_slice($genres,3) as $g): ?>
                  <span class="genre-pill" style="opacity:0.75;"><?= htmlspecialchars($g) ?></span>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($row['male_lead']): ?>
                <a href="https://www.google.com/search?q=<?= urlencode($row['male_lead']) ?>+actor+official"
                   target="_blank" class="actor-link">
                  <?= htmlspecialchars($row['male_lead']) ?> 🔗
                </a>
              <?php else: ?>-<?php endif; ?>
            </td>
            <td>
              <?php if ($row['female_lead']): ?>
                <a href="https://www.google.com/search?q=<?= urlencode($row['female_lead']) ?>+actress+official"
                   target="_blank" class="actor-link">
                  <?= htmlspecialchars($row['female_lead']) ?> 🔗
                </a>
              <?php else: ?>-<?php endif; ?>
            </td>
            <td><?= $row['episodes'] ?: '-' ?></td>
            <td><?= $row['start_date'] ? date('d/m/Y', strtotime($row['start_date'])) : '-' ?></td>
            <td><?= $row['end_date']   ? date('d/m/Y', strtotime($row['end_date']))   : '-' ?></td>
            <td>
              <div style="display:flex;gap:6px;">
                <a href="edit_drama.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
                <button class="btn btn-danger btn-sm"
                  onclick="confirmDelete(<?= $row['id'] ?>, '<?= addslashes(htmlspecialchars($row['title'])) ?>')">
                  🗑️ Padam
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>

</div>

<!-- Delete Confirm Modal -->
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

<div class="toast-container" id="toastContainer"></div>

<div class="bear-widget">
  <div class="bear-bubble" id="bearBubble"></div>
  <span class="bear-avatar" onclick="bearClick()">🐻</span>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>
