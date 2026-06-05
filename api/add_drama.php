<?php
// api/add_drama.php
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

$title      = $conn->real_escape_string(trim($data['title']       ?? ''));
$type       = $conn->real_escape_string(trim($data['drama_type']  ?? ''));
$genres     = $conn->real_escape_string(trim($data['genres']      ?? ''));
$male       = $conn->real_escape_string(trim($data['male_lead']   ?? ''));
$female     = $conn->real_escape_string(trim($data['female_lead'] ?? ''));
$episodes   = intval($data['episodes']   ?? 0);
$start_date = $conn->real_escape_string($data['start_date'] ?? '');
$end_date   = $conn->real_escape_string($data['end_date']   ?? '');

if (!$title || !$type || !$genres) {
    echo json_encode(['success'=>false,'error'=>'Data tidak lengkap']); exit;
}

$startVal = $start_date ? "'$start_date'" : 'NULL';
$endVal   = $end_date   ? "'$end_date'"   : 'NULL';

$sql = "INSERT INTO dramas (title, drama_type, genres, male_lead, female_lead, episodes, start_date, end_date)
        VALUES ('$title','$type','$genres','$male','$female',$episodes,$startVal,$endVal)";

if ($conn->query($sql)) {
    $newId = $conn->insert_id;

    // Save genres to autocomplete table
    foreach (array_map('trim', explode(',', $genres)) as $g) {
        if ($g) {
            $ge = $conn->real_escape_string($g);
            $conn->query("INSERT IGNORE INTO saved_genres (genre_name) VALUES ('$ge')");
        }
    }

    // Log activity
    logActivity($conn, 'add', $title);

    echo json_encode(['success'=>true,'id'=>$newId]);
} else {
    echo json_encode(['success'=>false,'error'=>$conn->error]);
}
