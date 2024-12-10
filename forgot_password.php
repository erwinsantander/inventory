<?php
require_once('includes/load.php');
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

// Create a MySQLi connection
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['forgot_email'];

    // Check if the email exists in the database
    $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $token_expired_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

        // Update the database with the token and expiration time
        $stmt = $mysqli->prepare("UPDATE users SET token = ?, reset_token_at = ? WHERE email = ?");
        $stmt->bind_param('sss', $token, $token_expired_at, $email);
        $stmt->execute();

        // Send the reset email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'montgomeryaurelia06@gmail.com';
            $mail->Password = 'oylq mpnj adlw iuod';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('montgomeryaurelia06@gmail.com', 'Inventory Management System');
            $mail->addAddress($email);

            // Content
            $resetLink = "https://ancminimart.com/new_password.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            $_SESSION['message'] = json_encode([
                'type' => 'success',
                'text' => 'An email has been sent to your email address with instructions to reset your password.'
            ]);
        } catch (Exception $e) {
            $_SESSION['message'] = json_encode([
                'type' => 'error',
                'text' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
            ]);
        }
    } else {
        $_SESSION['message'] = json_encode([
            'type' => 'error',
            'text' => 'No account found with that email address.'
        ]);
    }

    // Redirect back to the main page
    header('Location: index.php'); // Change 'index.php' to your main page
    exit();
}

$mysqli->close();
?>