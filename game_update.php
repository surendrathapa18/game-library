<?php
// game_update.php
session_start();
require_once "db_config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Not logged in"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id       = isset($data['id']) ? (int)$data['id'] : 0;
$platform = trim($data['platform'] ?? '');
$rating   = isset($data['rating']) ? (int)$data['rating'] : 0;
$comment  = trim($data['comment'] ?? '');

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid game id"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("
    UPDATE games 
    SET platform = ?, rating = ?, comment = ?
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("sisii", $platform, $rating, $comment, $id, $user_id);
$stmt->execute();

if ($stmt->affected_rows >= 0) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Update failed"]);
}
$stmt->close();
