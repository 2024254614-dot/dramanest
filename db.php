<?php
// config/db.php (Railway compatible)
$mysql_url = getenv('MYSQL_URL');
if ($mysql_url) {
    $parsed = parse_url($mysql_url);
    define('DB_HOST', $parsed['host']);
    define('DB_USER', $parsed['user']);
    define('DB_PASS', $parsed['pass']);
    define('DB_NAME', ltrim($parsed['path'], '/'));
} else {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'drama_mainframe');
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die(json_encode(['error' => 'Sambungan gagal: ' . $conn->connect_error]));
}
?>