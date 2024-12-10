<?php
require_once('config.php');
session_start();

// Check if token is provided
if (!isset($_GET['token'])) {
    die('Invalid request.');
}

$token = $_GET['token'];

// Verify the token
$stmt = $pdo->prepare("SELECT id, reset_token_at FROM users WHERE token = :token");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch();

if (!$user || new DateTime() > new DateTime($user['reset_token_at'])) {
    die('Invalid or expired token.');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_new_password'];

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: new_password.php?token=' . urlencode($token));
        exit;
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update the password in the database and remove the token
        $stmt = $pdo->prepare("UPDATE users SET password = :password, token = NULL, reset_token_at = NULL WHERE id = :id");
        $stmt->execute(['password' => $hashedPassword, 'id' => $user['id']]);

        // Redirect or show success message
        $_SESSION['success'] = 'Password has been reset successfully.';
        header('Location: login.php');
        exit;
    }
}
?>