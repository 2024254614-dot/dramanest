<?php
// api/update_drama.php
header('Content-Type: application/json');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$id         = intval($data['id']          ?? 0);
$title      = $conn->real_escape_string(trim($data['title']      ?? ''));
$genres     = $conn->real_escape_string(trim($data['genres']     ?? ''));
$male       = $conn->real_escape_string(trim($data['male_lead']  ?? ''));
$female     = $conn->real_escape_string(trim($data['female_lead']?? ''));
$episodes   = intval($data['episodes']  ?? 0);
$start_date = $conn->real_escape_string($data['start_date'] ?? '');
$end_date   = $conn->real_escape_string($data['end_date']   ?? '');

if (!$id || !$title || !$genres) {
    echo json_encode(['success'=>false,'error'=>'Data tidak lengkap']); exit;
}

$startVal = $start_date ? "'$start_date'" : 'NULL';
$endVal   = $end_date   ? "'$end_date'"   : 'NULL';

$sql = "UPDATE dramas SET
          title='$title', genres='$genres', male_lead='$male', female_lead='$female',
          episodes=$episodes, start_date=$startVal, end_date=$endVal
        WHERE id=$id";

if ($conn->query($sql)) {
    // Save genres
    foreach (array_map('trim', explode(',', $genres)) as $g) {
        if ($g) {
            $ge = $conn->real_escape_string($g);
            $conn->query("INSERT IGNORE INTO saved_genres (genre_name) VALUES ('$ge')");
        }
    }
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$conn->error]);
}
