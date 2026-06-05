<?php
// api/check_duplicate.php
header('Content-Type: application/json');
require_once '../config.php';

$title = $conn->real_escape_string(trim($_GET['title'] ?? ''));
$type  = $conn->real_escape_string(trim($_GET['type']  ?? ''));

if (!$title) { echo json_encode(['exists'=>false]); exit; }

$where = $type ? "AND drama_type='$type'" : '';
$r = $conn->query("SELECT id FROM dramas WHERE title='$title' $where LIMIT 1");
echo json_encode(['exists' => ($r->num_rows > 0)]);
