<?php
// auth.php - FINAL FIXED VERSION (No Email, No Auto-Login)
session_start();
header('Content-Type: application/json');

require_once 'db_config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_POST['action'] ?? $_GET['action'] ?? null;

// REGISTER
if ($method === 'POST' && $action === 'register') {
    $username = trim($_POST['username'] ?? '');
    $username_confirm = trim($_POST['username_confirm'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // 1. Server-side validation and confirmation checks
    if (empty($username) || empty($username_confirm) || empty($password) || empty($password_confirm)) {
        echo json_encode(['status'=>'error','message'=>'All fields are required.']);
        exit;
    }
    
    if ($username !== $username_confirm) {
        echo json_encode(['status' => 'error', 'message' => 'Usernames do not match.']);
        exit;
    }

    if ($password !== $password_confirm) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }
    
    // Basic length checks
    if (strlen($username) < 4) {
        echo json_encode(['status'=>'error','message'=>'Username must be at least 4 characters.']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['status'=>'error','message'=>'Password must be at least 6 characters.']);
        exit;
    }
    
    // 2. Check if username already exists
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo json_encode(['status'=>'error','message'=>'This username is already taken.']);
        $stmt->close();
        exit;
    }
    $stmt->close();

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 3. Insert new user (without email)
    $stmt = $mysqli->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param('ss', $username, $password_hash);
    
    if ($stmt->execute()) {
        // --- FIXED: Removed automatic login to force redirect to index.html ---
        echo json_encode(['status'=>'success', 'message'=>'Registration successful. Please log in.']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Registration failed: ' . $mysqli->error]);
    }
    $stmt->close();
    exit;
}

//  LOGIN 
if ($method === 'POST' && $action === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo json_encode(['status'=>'error','message'=>'All fields required']);
        exit;
    }

    $stmt = $mysqli->prepare("SELECT id, password_hash FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($id, $password_hash_db);

    if ($stmt->fetch() && $password_hash_db !== null) {

        if (password_verify($password, $password_hash_db)) {
            // Success
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            echo json_encode(['status'=>'success']);
        } else {
            echo json_encode(['status'=>'error','message'=>'Wrong username or password. Try again.']);
        }

    } else {
        echo json_encode(['status'=>'error','message'=>'Wrong username or password. Try again.']);
    }

    $stmt->close();
    exit;
}


// LOGOUT 
if ($method === 'POST' && $action === 'logout') {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        setcookie(session_name(), '', time()-42000, '/');
    }
    session_destroy();
    echo json_encode(['status'=>'success']);
    exit;
}

//  CHECK SESSION 
if ($method === 'GET' && $action === 'me') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(['status'=>'success','user'=>['id'=>$_SESSION['user_id'],'username'=>$_SESSION['username']]]);
    } else {
        echo json_encode(['status'=>'error','message'=>'Not authenticated']);
    }
    exit;
}

echo json_encode(['status'=>'error','message'=>'Invalid request']);
?>