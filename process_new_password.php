<?php
require_once('includes/load.php');
session_start();

// Create a MySQLi connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Check if token is provided
if (!isset($_GET['token'])) {
    $_SESSION['message'] = json_encode([
        'type' => 'error',
        'text' => 'Invalid request.'
    ]);
    header('Location: index.php');
    exit();
}

$token = $_GET['token'];

// Verify the token
$stmt = $mysqli->prepare("SELECT id, reset_token_at FROM users WHERE token = ?");
$stmt->bind_param('s', $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || new DateTime() > new DateTime($user['reset_token_at'])) {
    $_SESSION['message'] = json_encode([
        'type' => 'error',
        'text' => 'Invalid or expired token.'
    ]);
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_new_password'];

    if ($newPassword !== $confirmPassword) {
        $_SESSION['message'] = json_encode([
            'type' => 'error',
            'text' => 'Passwords do not match.'
        ]);
        header('Location: new_password.php?token=' . urlencode($token));
        exit();
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password in the database and remove the token
        $stmt = $mysqli->prepare("UPDATE users SET password = ?, token = NULL, reset_token_at = NULL WHERE id = ?");
        $stmt->bind_param('si', $hashedPassword, $user['id']);
        $stmt->execute();

        $_SESSION['message'] = json_encode([
            'type' => 'success',
            'text' => 'Password has been reset successfully.'
        ]);
        header('Location: index.php');
        exit();
    }
}

$mysqli->close();
?>