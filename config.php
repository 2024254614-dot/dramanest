<?php
// config/config.php — DramaNest

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'drama_mainframe');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Set charset to UTF-8
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

// -----------------------------------------------
// FIX 1: curly/smart quotes replaced with straight quotes
// FIX 2: keys now match drama_type slugs used throughout the app
// -----------------------------------------------
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

// Function to log activity
function logActivity($conn, $activity_type, $drama_title) {
    $stmt = $conn->prepare("INSERT INTO activity_log (activity_type, drama_title) VALUES (?, ?)");
    $stmt->bind_param("ss", $activity_type, $drama_title);
    $stmt->execute();
    $stmt->close();
}

// Function to get total drama count
function getTotalDramaCount($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM dramas");
    $row = $result->fetch_assoc();
    return (int) $row['total'];
}

// -----------------------------------------------
// FIX 3: function now actually returns the count
// -----------------------------------------------
function getDramaCountByType($conn, $type) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM dramas WHERE drama_type = ?");
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
    $stmt->close();
    return (int) $row['total'];
}

// Function to check for duplicate drama
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
