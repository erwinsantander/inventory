<?php
include_once('includes/load.php');

// Your secret key
$secret_key = '6LecM5UqAAAAAFhygg3kZDc55NREG8iGR_dktKl9';

// Function to verify reCAPTCHA v3 response
function verify_recaptcha($token, $secret_key) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $secret_key,
        'response' => $token
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return false;
    }

    $result = json_decode($response, true);

    return $result;
}

// Check reCAPTCHA response
if (!isset($_POST['recaptcha_token'])) {
    $_SESSION['message'] = json_encode([
        'type' => 'error',
        'text' => 'reCAPTCHA token missing. Please try again.'
    ]);
    header('Location: index.php');
    exit();
}

$recaptcha_response = verify_recaptcha($_POST['recaptcha_token'], $secret_key);

if (!$recaptcha_response || !$recaptcha_response['success'] || $recaptcha_response['score'] < 0.5) {
    $_SESSION['message'] = json_encode([
        'type' => 'error',
        'text' => 'reCAPTCHA verification failed. Please try again.'
    ]);
    header('Location: index.php');
    exit();
}

// Initialize session variables for login attempts and lockout time if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
}

$lockout_duration = 3 * 60; // Lockout duration in seconds (3 minutes)

// Check if the user is currently locked out
if (time() < $_SESSION['lockout_time']) {
    $remaining_time = $_SESSION['lockout_time'] - time();
    $_SESSION['message'] = json_encode([
        'type' => 'error',
        'text' => 'Too many failed login attempts. Please try again in ' . ceil($remaining_time / 60) . ' minutes.'
    ]);
    header('Location: index.php');
    exit();
}

// Authentication function (unchanged, as provided in the original code)
function authenticate($username_or_email='', $password='') {
    global $db;

    $username_or_email = $db->escape($username_or_email);
    $password = $db->escape($password);

    $sql = sprintf(
        "SELECT id, username, password, user_level, status FROM users WHERE (username='%s' OR email='%s') LIMIT 1",
        $username_or_email,
        $username_or_email
    );

    $result = $db->query($sql);

    if ($db->num_rows($result)) {
        $user = $db->fetch_assoc($result);

        if ($user['status'] != 1) {
            $_SESSION['message'] = json_encode([
                'type' => 'error',
                'text' => 'Your account is inactive. Please wait for admin approval.'
            ]);
            return false;
        }

        if (password_verify($password, $user['password']) || sha1($password) === $user['password']) {
            if (sha1($password) === $user['password']) {
                $new_hash = password_hash($password, PASSWORD_BCRYPT);
                $update_sql = sprintf("UPDATE users SET password='%s' WHERE id=%d", $db->escape($new_hash), $user['id']);
                $db->query($update_sql);
            }
            return ['id' => $user['id'], 'user_level' => $user['user_level']];
        }
    }

    return false;
}

// Validate fields and process login
$req_fields = ['password'];
validate_fields($req_fields);

$username_or_email = remove_junk($_POST['username']) ?: remove_junk($_POST['email']);
$password = remove_junk($_POST['password']);

if (empty($errors)) {
    $user_data = authenticate($username_or_email, $password);

    if ($user_data) {
        $_SESSION['login_attempts'] = 0;

        $session->login($user_data['id']);
        updateLastLogIn($user_data['id']);

        $_SESSION['message'] = json_encode([
            'type' => 'success',
            'text' => 'Login successful!'
        ]);

        if ($user_data['user_level'] == 1) {
            header("Location: admin.php");
        } else if ($user_data['user_level'] == 2) {
            header("Location: cashier.php");
        }
        exit();
    } else {
        $_SESSION['login_attempts']++;

        if ($_SESSION['login_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time() + $lockout_duration;
            $_SESSION['message'] = json_encode([
                'type' => 'error',
                'text' => 'Too many failed login attempts. Please try again in 3 minutes.'
            ]);
        } else {
            $remaining_attempts = 3 - $_SESSION['login_attempts'];
            $_SESSION['message'] = json_encode([
                'type' => 'error',
                'text' => 'Incorrect username or password. Attempts remaining: ' . $remaining_attempts
            ]);
        }
        header('Location: index.php');
        exit();
    }
} else {
    $session->msg("d", $errors);
    redirect('index.php', false);
}
?>
<!-- Include SweetAlert CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>