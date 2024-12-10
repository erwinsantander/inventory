<?php
require_once('includes/config.php'); // Include your database configuration
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['forgot_email'];

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50));
        $token_expired_at = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

        // Update the database with the token and expiration time
        $stmt = $pdo->prepare("UPDATE users SET token = :token, reset_token_at = :reset_token_at WHERE email = :email");
        $stmt->execute(['token' => $token, 'reset_token_at' => $token_expired_at, 'email' => $email]);

        // Send the reset email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'montgomeryaurelia06@gmail.com';  
            $mail->Password   = 'oylq mpnj adlw iuod';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('your-email@gmail.com', 'Your Name');
            $mail->addAddress($email);

            // Content
            $resetLink = "https://ancminimart.com/new_password.php?token=$token";
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the following link to reset your password: <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            echo 'An email has been sent to your email address with instructions to reset your password.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "No account found with that email address.";
    }
}
?>