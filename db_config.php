<?php
// db_config.php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = ''; // default XAMPP has empty password for root
$DB_NAME = 'games_library';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database connection failed: ' . $mysqli->connect_error]);
    exit;
}
$mysqli->set_charset('utf8mb4');
