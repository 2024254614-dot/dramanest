<?php
// api/delete_drama.php
header('Content-Type: application/json');
require_once '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$id   = intval($data['id'] ?? 0);

if (!$id) { echo json_encode(['success'=>false,'error'=>'ID tidak sah']); exit; }

if ($conn->query("DELETE FROM dramas WHERE id=$id")) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$conn->error]);
}
