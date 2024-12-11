<?php
// signup.php
require_once('includes/load.php');
require_once('email_verification.php');

// Redirect if already logged in
if ($session->isUserLoggedIn()) {
    redirect('home.php', false);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Generate a random 10-digit ID
    $random_id = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);

    // Collect and sanitize input
    $name = htmlspecialchars(trim($_POST['full_name']));
    $user_level = 2; // Default to Cashier
    $email = !empty($_POST['signup_email']) ? filter_var(trim($_POST['signup_email']), FILTER_VALIDATE_EMAIL) : null;
    $password = trim($_POST['signup_password']);
    $confirm_password = trim($_POST['confirm_password']);
    $contact_number = htmlspecialchars(trim($_POST['contact_number'])); // Collect and sanitize contact number

    // Validation
    if (empty($name))
        $errors[] = "Name is required.";
    if (empty($email))
        $errors[] = "Valid email is required.";
    if (empty($password))
        $errors[] = "Password is required.";
    if ($password !== $confirm_password)
        $errors[] = "Passwords do not match.";
    if (empty($contact_number))
        $errors[] = "Contact number is required."; // Validate contact number

    // Check if email already exists with status 1
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND verified = 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $email_check_active = $stmt->get_result();
    if ($email_check_active->num_rows > 0)
        $errors[] = "Email already registered and active.";

    // Check if email exists with status 0
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND verified = 0");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $email_check_inactive = $stmt->get_result();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $verification_code = str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);
        $default_image = 'no_image.jpg';
        $status = 0;
        $verified = 0;

        if ($email_check_inactive->num_rows > 0) {
            // Update existing record
            $stmt = $db->prepare("UPDATE users SET name = ?, password = ?, user_level = ?, image = ?, code = ?, verified = ?, contact_number = ?, created_at = NOW() WHERE email = ? AND verified = 0");
            $stmt->bind_param('ssississ', $name, $hashed_password, $user_level, $default_image, $verification_code, $verified, $contact_number, $email);
        } else {
            // Check if the random ID already exists
            do {
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->bind_param('s', $random_id);
                $stmt->execute();
                $id_check = $stmt->get_result();
                if ($id_check->num_rows > 0) {
                    $random_id = str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
                }
            } while ($id_check->num_rows > 0);

            // Insert new record
            $stmt = $db->prepare("INSERT INTO users (id, name, password, user_level, image, email, status, last_login, code, verified, contact_number, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NULL, ?, ?, ?, NOW())");
            $stmt->bind_param('sssississ', $random_id, $name, $hashed_password, $user_level, $default_image, $email, $status, $verification_code, $verified, $contact_number);
        }

        if ($stmt->execute()) {
            if (sendVerificationEmail($email, $verification_code)) {
                // Generate a random token
                $token = bin2hex(random_bytes(16)); // Generates a 32-character hexadecimal token

                // Redirect with the token as a query parameter
                redirect("verify.php?token={$token}", false);
            } else {
                $_SESSION['message'] = json_encode(['type' => 'error', 'text' => 'Account created, but failed to send verification email.']);
                redirect('index.php', false);
            }
        } else {
            $_SESSION['message'] = json_encode(['type' => 'error', 'text' => 'Failed to create account. Please try again.']);
            redirect('index.php', false);
        }
    } else {
        $_SESSION['message'] = json_encode(['type' => 'error', 'text' => implode(' ', $errors)]);
        redirect('index.php', false);
    }
}
?>