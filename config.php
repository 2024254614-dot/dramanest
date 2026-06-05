<?php
// config/config.php — DramaNest (Railway compatible)

$mysql_url = getenv('MYSQL_URL');
if ($mysql_url) {
    $parsed = parse_url($mysql_url);
    define('DB_HOST', $parsed['host']);
    define('DB_USER', $parsed['user']);
    define('DB_PASS', $parsed['pass']);
    define('DB_NAME', ltrim($parsed['path'], '/'));
} else {
    // Fallback for local XAMPP development
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'drama_mainframe');
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

function getDramaTypeName($type) {
    $types = [
        'melayu'   => 'Melayu',
        'chinese'  => 'Chinese',
        'indo'     => 'Indonesia',
        'jepun'    => 'Jepun',
        'korea'    => 'Korea',
        'thailand' => 'Thailand',
        'taiwan'   => 'Taiwan',
    ];
    return $types[$type] ?? 'Unknown';
}

function logActivity($conn, $activity_type, $drama_title) {
    $stmt = $conn->prepare("INSERT INTO activity_log (activity_type, drama_title) VALUES (?, ?)");
    $stmt->bind_param("ss", $activity_type, $drama_title);
    $stmt->execute();
    $stmt->close();
}

function getTotalDramaCount($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM dramas");
    $row = $result->fetch_assoc();
    return (int) $row['total'];
}

function getDramaCountByType($conn, $type) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM dramas WHERE drama_type = ?");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return (int) $row['total'];
}

function checkDuplicateDrama($conn, $title, $drama_type) {
    $stmt = $conn->prepare("SELECT id FROM dramas WHERE title = ? AND drama_type = ?");
    $stmt->bind_param("ss", $title, $drama_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    return $exists;
}
?>