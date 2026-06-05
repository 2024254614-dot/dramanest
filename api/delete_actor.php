<?php
// api/delete_actor.php
header('Content-Type: application/json');
require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$id   = intval($data['id'] ?? 0);

if (!$id) { echo json_encode(['success'=>false,'error'=>'ID tidak sah']); exit; }

if ($conn->query("DELETE FROM actors WHERE id=$id")) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false,'error'=>$conn->error]);
}
