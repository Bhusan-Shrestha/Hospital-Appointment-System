<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }
}


function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect_by_role() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
    $role = $_SESSION['role'];
    switch ($role) {
        case 'admin':
            header('Location: admin/index.php');
            break;
        case 'doctor':
            header('Location: doctor/index.php');
            break;
        case 'receptionist':
            header('Location: receptionist/index.php');
            break;
        default:
            header('Location: login.php');
    }
    exit;
}

function login_user($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        //  Verify hashed password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}


function check_role($required_role) {
    if (!is_logged_in() || $_SESSION['role'] != $required_role) {
        header('Location: ../login.php');
        exit;
    }
}
if (session_status() === PHP_SESSION_NONE) session_start();

/** CSRF helpers */
function csrf_token() {
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}
function csrf_verify($token) {
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token ?? '');
}

/** Change password (verify old, set new hash) */
function change_password(mysqli $conn, int $user_id, string $old_password, string $new_password): void {
  // fetch current hash
  $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $stmt->bind_result($hash);
  if (!$stmt->fetch()) { $stmt->close(); throw new Exception("User not found."); }
  $stmt->close();

  // verify old password
  if (!password_verify($old_password, $hash)) {
    throw new Exception("Current password is incorrect.");
  }

  // basic policy (tweak as you like)
  if (strlen($new_password) < 8) {
    throw new Exception("New password must be at least 8 characters.");
  }

  // update
  $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
  $stmt->bind_param("si", $new_hash, $user_id);
  if (!$stmt->execute()) {
    throw new Exception("Failed to update password.");
  }
}
?>
