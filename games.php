<?php
session_start();
header('Content-Type: application/json');
require_once 'db_config.php';

/*
   AUTH CHECK
 */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$method  = $_SERVER['REQUEST_METHOD'];

/* 
   FILTER VALUES (platform, genre, year)
*/
if (isset($_GET['filters'])) {

    $platforms = [];
    $genres    = [];
    $years     = [];
    $rating     = [];

    $q1 = $mysqli->query("SELECT DISTINCT platform FROM games WHERE user_id=$user_id ORDER BY platform");
    while ($row = $q1->fetch_assoc()) $platforms[] = $row['platform'];

    $q2 = $mysqli->query("SELECT DISTINCT genre FROM games WHERE user_id=$user_id ORDER BY genre");
    while ($row = $q2->fetch_assoc()) $genres[] = $row['genre'];

    $q3 = $mysqli->query("SELECT DISTINCT release_year FROM games WHERE user_id=$user_id ORDER BY release_year DESC");
    while ($row = $q3->fetch_assoc()) $years[] = $row['release_year'];


    echo json_encode([
        "status"    => "success",
        "platforms" => $platforms,
        "genres"    => $genres,
        "years"     => $years
    ]);
    exit;
}

/* 
   GET ALL GAMES (for library.js edit mode)
 */
if (isset($_GET['all'])) {

    $sql = "SELECT id, title, platform, genre, release_year, rating, comment,
                   is_wishlist, created_at
            FROM games
            WHERE user_id = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Stats
    $total = count($games);
    $sum = 0;
    $count = 0;

    foreach ($games as $g) {
        if ((int)$g['rating'] > 0) {
            $sum += (int)$g['rating'];
            $count++;
        }
    }

    $avg = $count ? round($sum / $count, 2) : 0;

    echo json_encode([
        "status" => "success",
        "games"  => $games,
        "stats"  => [
            "total"      => $total,
            "avg_rating" => $avg
        ]
    ]);
    exit;
}

/* 
   ADD GAME
 */
if ($method === 'POST') {

    $title        = trim($_POST['title'] ?? '');
    $platform     = trim($_POST['platform'] ?? '');
    $genre        = trim($_POST['genre'] ?? '');
    $release_year = (int)($_POST['release_year'] ?? 0);
    $rating       = (int)($_POST['rating'] ?? 0);
    $comment      = trim($_POST['comment'] ?? '');
    $is_wishlist  = isset($_POST['is_wishlist']) ? 1 : 0;

    if ($title === '') {
        echo json_encode(['status' => 'error', 'message' => 'Title required']);
        exit;
    }

    $stmt = $mysqli->prepare("
        INSERT INTO games
        (user_id, title, platform, genre, release_year, rating, comment, is_wishlist)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssiisi",
        $user_id, $title, $platform, $genre, $release_year, $rating, $comment, $is_wishlist
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'id' => $mysqli->insert_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
    }

    exit;
}

/* 
   DELETE GAME
*/
if ($method === 'DELETE') {

    $raw  = file_get_contents("php://input");
    $data = json_decode($raw, true);
    $id   = (int)($data['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Game ID']);
        exit;
    }

    $stmt = $mysqli->prepare("DELETE FROM games WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
    }

    exit;
}

/*
   UPDATE GAME
 */
if ($method === 'PUT') {

    parse_str(file_get_contents("php://input"), $data);

    $id = (int)($data['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Game ID']);
        exit;
    }

    $title        = trim($data['title'] ?? '');
    $platform     = trim($data['platform'] ?? '');
    $genre        = trim($data['genre'] ?? '');
    $release_year = (int)($data['release_year'] ?? 0);
    $rating       = (int)($data['rating'] ?? 0);
    $comment      = trim($data['comment'] ?? '');
    $is_wishlist  = isset($data['is_wishlist']) ? 1 : 0;

    $stmt = $mysqli->prepare("
        UPDATE games
        SET title=?, platform=?, genre=?, release_year=?, rating=?, comment=?, is_wishlist=?
        WHERE id=? AND user_id=?
    ");

    $stmt->bind_param(
        "sssisiisi",
        $title, $platform, $genre, $release_year, $rating, $comment, $is_wishlist, $id, $user_id
    );

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
    }

    exit;
}

/* 
   GET GAMES (FILTER + SORT)
 */
if ($method === 'GET') {

    $search   = "%" . ($mysqli->real_escape_string($_GET['search'] ?? '')) . "%";
    $platform = $_GET['platform'] ?? '';
    $genre    = $_GET['genre'] ?? '';
    $year     = $_GET['year'] ?? '';
    $rating   = $_GET['rating'] ?? '';
    $sort     = $_GET['sort'] ?? "recent";
    $is_wishlist = isset($_GET['is_wishlist']) ? (int)$_GET['is_wishlist'] : null;

    $where  = "WHERE user_id=?";
    $params = [$user_id];
    $types  = "i";

    if (trim($search, '%') !== '') {
        $where .= " AND title LIKE ?";
        $params[] = $search;
        $types   .= "s";
    }

    if ($platform !== '') {
        $where .= " AND platform=?";
        $params[] = $platform;
        $types   .= "s";
    }

    if ($genre !== '') {
        $where .= " AND genre=?";
        $params[] = $genre;
        $types   .= "s";
    }

    if ($year !== '') {
        $where .= " AND release_year=?";
        $params[] = (int)$year;
        $types   .= "i";
    }

    /*  RATING FILTER  */
    if ($rating !== '') {
        $where .= " AND rating >= ?";
        $params[] = (int)$rating;
        $types   .= "i";
    }

    if ($is_wishlist !== null) {
        $where .= " AND is_wishlist=?";
        $params[] = $is_wishlist;
        $types   .= "i";
    }

    // Sort options
    switch ($sort) {
        case "az":     $order = "ORDER BY title ASC"; break;
        case "rating": $order = "ORDER BY rating DESC"; break;
        case "year":   $order = "ORDER BY release_year DESC"; break;
        default:       $order = "ORDER BY created_at DESC"; break;
    }

    $sql = "
        SELECT id, title, platform, genre, release_year, rating, comment, is_wishlist, created_at
        FROM games
        $where
        $order
    ";

    $stmt = $mysqli->prepare($sql);

    // dynamic bind
    $bind = [$types];
    foreach ($params as $i => $p) {
        $var = "p$i";
        $$var = $p;
        $bind[] = &$$var;
    }

    call_user_func_array([$stmt, 'bind_param'], $bind);

    $stmt->execute();
    $games = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    echo json_encode(['status' => 'success', 'games' => $games]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
?>
