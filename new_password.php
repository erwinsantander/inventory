<?php
require_once('includes/load.php');


// Check if token is provided
if (!isset($_GET['token'])) {
    die('Invalid request.');
}

$token = $_GET['token'];

// Verify the token
$stmt = $pdo->prepare("SELECT id, reset_token_at FROM users WHERE token = :token");
$stmt->execute(['token' => $token]);
$user = $stmt->fetch();

$tokenValid = false;
if ($user && new DateTime() <= new DateTime($user['reset_token_at'])) {
    $tokenValid = true;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Reset Password</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-primary': '#dc2626',
                        'brand-secondary': '#16a34a'
                    }
                }
            }
        }
    </script>
    <style>
        .swal2-container {
            z-index: 9999;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-orange-200 to-red-300">

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="container mx-auto px-4 flex justify-center items-center">
        <div class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="p-6">
                <div class="text-center mb-6">
                    <h1 class="text-3xl font-bold text-brand-primary mb-2">Reset Password</h1>
                    <p class="text-gray-600">Enter your new password below</p>
                </div>

                <?php if ($tokenValid): ?>
                    <!-- New Password Form -->
                    <form method="post" action="process_new_password.php?token=<?php echo htmlspecialchars($token); ?>" id="new-password-form" class="space-y-4">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="new_password" name="new_password"
                                       class="w-full pl-10 pr-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Enter new password" required minlength="8">
                                <button type="button" id="show-new-password"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label for="confirm_new_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="confirm_new_password" name="confirm_new_password"
                                       class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Confirm new password" required>
                            </div>
                            <p id="new-password-match" class="text-sm mt-2"></p>
                        </div>

                        <button type="submit"
                                class="w-full bg-brand-secondary text-white py-2 rounded-md hover:bg-green-700 transition duration-300">
                            Reset Password
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-center">
                        <p class="text-red-500">The token is incorrect or expired.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const newPassword = document.getElementById('new_password');
        const confirmNewPassword = document.getElementById('confirm_new_password');
        const passwordMatchText = document.getElementById('new-password-match');

        function checkPasswordMatch() {
            if (newPassword.value === confirmNewPassword.value) {
                passwordMatchText.textContent = 'Passwords match';
                passwordMatchText.classList.remove('text-red-500');
                passwordMatchText.classList.add('text-green-500');
            } else {
                passwordMatchText.textContent = 'Passwords do not match';
                passwordMatchText.classList.remove('text-green-500');
                passwordMatchText.classList.add('text-red-500');
            }
        }

        newPassword.addEventListener('input', checkPasswordMatch);
        confirmNewPassword.addEventListener('input', checkPasswordMatch);

        // Password toggle functionality
        function setupPasswordToggle(passwordId, toggleButtonId) {
            const passwordField = document.getElementById(passwordId);
            const toggleButton = document.getElementById(toggleButtonId);
            const icon = toggleButton.querySelector('i');
            toggleButton.addEventListener('click', function () {
                const type = passwordField.type === 'password' ? 'text' : 'password';
                passwordField.type = type;
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }

        setupPasswordToggle('new_password', 'show-new-password');
    });
</script>

<?php include_once('layouts/footer.php'); ?>
</body>
</html>