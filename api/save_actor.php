<?php
// api/save_actor.php
header('Content-Type: application/json');
require_once '../config.php';

$data  = json_decode(file_get_contents('php://input'), true);
$name  = $conn->real_escape_string(trim($data['name']         ?? ''));
$photo = $conn->real_escape_string(trim($data['photo_url']    ?? ''));
$type  = $conn->real_escape_string(trim($data['drama_type']   ?? ''));
$link  = $conn->real_escape_string(trim($data['official_link']?? ''));
$role  = in_array($data['role_type']??'',['male','female']) ? $data['role_type'] : 'male';

if (!$name) { echo json_encode(['success'=>false,'error'=>'Nama diperlukan']); exit; }

$sql = "INSERT INTO actors (name, photo_url, drama_type, official_link, role_type)
        VALUES ('$name','$photo','$type','$link','$role')";

if ($conn->query($sql)) {
    echo json_encode(['success'=>true,'id'=>$conn->insert_id]);
} else {
    echo json_encode(['success'=>false,'error'=>$conn->error]);
}
