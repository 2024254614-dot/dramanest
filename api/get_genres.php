<?php
// api/get_genres.php
header('Content-Type: application/json');
require_once '../config.php';

// Save a new genre if passed
if (!empty($_GET['save'])) {
    $g = $conn->real_escape_string(trim($_GET['save']));
    if ($g) $conn->query("INSERT IGNORE INTO saved_genres (genre_name) VALUES ('$g')");
}

$r = $conn->query("SELECT genre_name FROM saved_genres ORDER BY genre_name ASC");
$genres = [];
while ($row = $r->fetch_assoc()) $genres[] = $row['genre_name'];

echo json_encode(['genres' => $genres]);
