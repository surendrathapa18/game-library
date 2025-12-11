<?php
session_start();
require_once "db_config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? "User";
$db = $mysqli;

// Total Games
$stmt = $db->prepare("SELECT COUNT(*) FROM games WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($totalGames);
$stmt->fetch();
$stmt->close();

// Wishlist Count
$stmt = $db->prepare("SELECT COUNT(*) FROM games WHERE user_id = ? AND is_wishlist = 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($wishlistCount);
$stmt->fetch();
$stmt->close();

// Recent Games
$stmt = $db->prepare("SELECT title, platform, genre, created_at 
                      FROM games 
                      WHERE user_id = ? 
                      ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recentResult = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">

<style>
/* Dark Background */
body {
    background: #141414;
    color: #fff;
    margin: 0;
}

/* Netflix Top Bar */
.topbar {
    background: #000;
    padding: 18px 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #222;
}

.topbar h1 {
    color: #e50914;
    font-size: 28px;
    font-weight: bold;
}

.topbar a {
    color: #ccc;
    margin-right: 20px;
    text-decoration: none;
    font-size: 16px;
}

.topbar a:hover { color: #fff; }

#logoutBtn {
    background: #e50914;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    color: white;
    font-weight: bold;
    cursor: pointer;
}
#logoutBtn:hover { background: #b20710; }

/* Dashboard Container */
.container {
    width: 92%;
    margin: 25px auto;
}

/* Stat Cards (Netflix Tiles) */
.stats-grid {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
}

.card {
    background: #1f1f1f;
    padding: 25px;
    border-radius: 10px;
    flex: 1;
    min-width: 260px;
    border-left: 5px solid #e50914;
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

.card h2 {
    margin: 0 0 10px 0;
    color: #e50914;
}

.big-number {
    font-size: 3rem;
    font-weight: bold;
    margin-top: 10px;
}

/* Recent Games Table */
.table-card {
    margin-top: 35px;
    background: #1f1f1f;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
}

th {
    background: #222;
    padding: 12px;
    color: #e50914;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #333;
    color: #ddd;
}

tr:hover {
    background: #2a2a2a;
}
</style>

</head>
<body>

<header class="topbar">
    <h1>User Dashboard</h1>
    <div>
        <a href="dashboard.php" style="font-weight:bold;">Dashboard</a>
        <a href="library.html">Add/Edit Games</a>
        <a href="my_games.html">My Games</a>
        <span style="font-weight:bold;">Hi, <?= htmlspecialchars($username) ?></span>
        <button id="logoutBtn">Logout</button>
    </div>
</header>

<div class="container">

    <!-- STAT BOXES -->
    <div class="stats-grid">

        <div class="card">
            <h2>Total Games</h2>
            <p class="big-number"><?= $totalGames ?></p>
        </div>

        <div class="card">
            <h2>Wishlist Items</h2>
            <p class="big-number"><?= $wishlistCount ?></p>
        </div>

        <div class="card">
            <h2>Profile</h2>
            <p><strong>Username:</strong> <?= htmlspecialchars($username) ?></p>
            <p><strong>User ID:</strong> <?= $user_id ?></p>
        </div>

    </div>

    <!-- RECENT GAMES TABLE -->
    <div class="table-card">
        <h2>Recently Added Games</h2>

        <table>
            <tr>
                <th>Title</th>
                <th>Platform</th>
                <th>Genre</th>
                <th>Date Added</th>
            </tr>

            <?php while ($row = $recentResult->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['platform']) ?></td>
                <td><?= htmlspecialchars($row['genre']) ?></td>
                <td><?= $row['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</div>

<script>
document.getElementById("logoutBtn").onclick = () => {
    window.location.href = "logout.php";
};
</script>

</body>
</html>
