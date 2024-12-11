<?php
ob_start();
require_once('includes/load.php');

// Redirect if user is already logged in
if ($session->isUserLoggedIn()) {
    redirect('admin.php', false);
}

// Handle URL without .php extension
$request = $_SERVER['REQUEST_URI'];
if (substr($request, -4) == '.php') {
    $new_url = substr($request, 0, -4);
    header("Location: $new_url", true, 301);
    exit();
}

// Security headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(self), microphone=()");

// Check for session messages
$message = null;
if (isset($_SESSION['message'])) {
    $message = json_decode($_SESSION['message'], true);
    unset($_SESSION['message']);
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
    <title>Inventory Management System - Login/Signup</title>
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
                    <h1 id="page-title" class="text-3xl font-bold text-brand-primary mb-2">Login</h1>
                    <p class="text-gray-600">ANC MINIMART</p>
                </div>

                <div id="form-container">
                    <!-- Login Form -->
                    <form method="post" action="auth.php" id="login-form" class="space-y-4">
                        <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                        <div id="username-field">
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" name="username"
                                       class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Enter username">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="password" name="password"
                                       class="w-full pl-10 pr-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Enter password" required>
                                <button type="button" id="show-password"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" id="login-button"
                                class="w-full bg-brand-primary text-white py-2 rounded-md hover:bg-red-700 transition duration-300">
                            Login
                        </button>
                    </form>

                    <!-- Forgot Password Form (Hidden by Default) -->
                    <form method="post" action="forgot_password.php" id="forgot-password-form" class="space-y-4 hidden">
                        <div>
                            <label for="forgot_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="forgot_email"
                                       class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Enter your email" required>
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full bg-brand-secondary text-white py-2 rounded-md hover:bg-green-700 transition duration-300">
                            Reset Password
                        </button>
                    </form>

                    <!-- Signup Form (Hidden by Default) -->
                    <form method="post" action="signup.php" id="signup-form" class="space-y-4 hidden">
                       <div>
  <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
  <div class="relative">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <i class="fas fa-id-card text-gray-400"></i>
    </div>
    <input 
      type="text" 
      id="full_name" 
      name="full_name"
      class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
      placeholder="Enter full name" 
      required
      oninput="validateFullName(this)">
  </div>
</div>

                        <div id="signup-email-field">
                            <label for="signup_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="signup_email"
                                       class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Enter email" required>
                            </div>
                        </div>

                        <div id="signup-contact-field" class="flex-1">
    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="fas fa-phone-alt text-gray-400"></i>
        </div>
        <input type="tel" name="contact_number"
               class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
               placeholder="Enter contact number" required
               pattern="[0-9]+" title="Only numbers are allowed"
               oninput="this.value = this.value.replace(/[^0-9]/g, '')">
    </div>
</div>

                        <div>
                            <label for="signup_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="signup_password" name="signup_password"
                                       class="w-full pl-10 pr-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Create password" required minlength="8">
                                <button type="button" id="show-signup-password"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400"></i>
                                </button>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full mt-2">
                                <div id="password-strength-bar" class="h-2 rounded-full"></div>
                            </div>
                            <p id="password-strength-text" class="text-sm mt-2 text-gray-500"></p>
                        </div>

                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="confirm_password" name="confirm_password"
                                       class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                       placeholder="Confirm password" required>
                            </div>
                            <p id="password-match" class="text-sm mt-2"></p>
                        </div>
                        <div class="flex items-center">
                        <input type="checkbox" id="terms_conditions" name="terms_conditions"
                                   class="h-4 w-4 text-brand-primary focus:ring-brand-primary border-gray-300 rounded" required>
                            <label for="terms_conditions" class="ml-2 block text-sm text-gray-700">
                                I agree to the
                                <a href="#" id="terms-link" class="text-brand-primary hover:underline">Terms and Conditions</a>
                            </label>
                        </div>
                        <p id="terms-error" class="text-sm mt-2 text-red-500 hidden">You must agree to the Terms and Conditions to sign up.</p>
                        <button type="submit"
                                class="w-full bg-brand-secondary text-white py-2 rounded-md hover:bg-green-700 transition duration-300">
                            Create Account
                        </button>
                    </form>
                </div>

                <!-- Terms and Conditions Modal -->
                <div id="terms-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
                    <div class="bg-white rounded-lg w-11/12 max-w-lg p-6">
                        <h2 class="text-xl font-bold mb-4">Terms and Conditions for ANC Mini Mart
Welcome to ANC Mini Mart! By using our website (ancminimart.com), you agree to the following terms and conditions. Please read them carefully.
</h2>
                        <div class="overflow-y-auto max-h-64">
                            <p class="text-gray-600 mb-4">
                            
1. General Use
By accessing our website, you confirm that you are at least 18 years old or have parental/guardian consent.
These terms apply to all users of ancminimart.com.
2. Products and Services
Products listed on the website are subject to availability.
Prices are accurate at the time of posting but may change without prior notice.
ANC Mini Mart reserves the right to limit or refuse orders at our discretion.
3. Payments
Payments must be made at checkout using the accepted methods listed on the website.
All payment details are securely processed and not stored by ANC Mini Mart.
4. Shipping and Delivery
Delivery times are estimates and may vary due to unforeseen circumstances.
Customers are responsible for providing accurate shipping details. ANC Mini Mart is not liable for failed deliveries due to incorrect addresses.
5. Returns and Refunds
Items may be returned within 14 days of delivery if they meet our return policy.
Refunds are processed after inspecting the returned item and may take 5-7 business days.
6. User Conduct
Do not misuse the website by introducing viruses or malicious software.
ANC Mini Mart reserves the right to restrict access to users who violate these terms.
7. Privacy Policy
By using ancminimart.com, you agree to our Privacy Policy, which outlines how we collect, use, and protect your data.
8. Limitation of Liability
ANC Mini Mart is not liable for any damages arising from the use of our website, including errors, interruptions, or loss of data.
9. Intellectual Property
All content on the website, including text, images, and logos, is the property of ANC Mini Mart and protected by copyright laws. Unauthorized use is prohibited.
10. Changes to Terms
ANC Mini Mart reserves the right to modify these terms at any time. Changes will be posted on this page
                            </p>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button id="close-terms" class="px-4 py-2 bg-brand-primary text-white rounded hover:bg-red-700 transition duration-300">
                                Close
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        // Show SweetAlert if there's a message
                        <?php if ($message): ?>
                        Swal.fire({
                            title: '<?php echo $message['type'] === 'success' ? '' : ''; ?>',
                            text: '<?php echo $message['text']; ?>',
                            icon: '<?php echo $message['type']; ?>',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            toast: true,
                            background: '<?php echo $message['type'] === 'success' ? '#d1f2eb' : '#f2d7d5'; ?>',
                            customClass: {
                                popup: 'swal2-toast-custom-class'
                            }
                        }).then(() => {
                            <?php if ($message['type'] === 'success'): ?>
                            window.location.href = '<?php echo $_SESSION['redirect_url'] ?? 'home.php'; ?>';
                            <?php endif; ?>
                        });
                        <?php endif; ?>

                        const termsLink = document.getElementById('terms-link');
                        const termsModal = document.getElementById('terms-modal');
                        const closeTerms = document.getElementById('close-terms');
                        const switchFormText = document.getElementById('switch-form-text');
                        const forgotPasswordText = document.getElementById('forgot-password-text');

                        // Show the terms modal
                        termsLink.addEventListener('click', function (e) {
                            e.preventDefault();
                            termsModal.classList.remove('hidden');
                        });

                        // Close the terms modal
                        closeTerms.addEventListener('click', function () {
                            termsModal.classList.add('hidden');
                        });

                        // Prevent submission if terms checkbox is not checked
                        const signupForm = document.getElementById('signup-form');
                        const termsCheckbox = document.getElementById('terms_conditions');
                        const termsError = document.getElementById('terms-error');

                        signupForm.addEventListener('submit', function (event) {
                            if (!termsCheckbox.checked) {
                                event.preventDefault();
                                termsError.classList.remove('hidden');
                            } else {
                                termsError.classList.add('hidden');
                            }
                        });

                        // Switch between Login, Signup, and Forgot Password
                        document.addEventListener('click', function (e) {
                            const switchFormLink = e.target.closest('#switch-form');
                            const forgotPasswordLink = e.target.closest('#forgot-password-link');

                            if (switchFormLink) {
                                e.preventDefault();
                                const loginForm = document.getElementById('login-form');
                                const signupForm = document.getElementById('signup-form');
                                const forgotPasswordForm = document.getElementById('forgot-password-form');
                                const pageTitle = document.getElementById('page-title');

                                if (loginForm.classList.contains('hidden')) {
                                    loginForm.classList.remove('hidden');
                                    signupForm.classList.add('hidden');
                                    forgotPasswordForm.classList.add('hidden');
                                    pageTitle.textContent = 'Login';
                                    switchFormText.innerHTML = 'Don\'t have an account? <a href="#" id="switch-form" class="text-brand-primary hover:underline">Sign Up</a>';
                                    forgotPasswordText.classList.remove('hidden');
                                } else {
                                    loginForm.classList.add('hidden');
                                    signupForm.classList.remove('hidden');
                                    pageTitle.textContent = 'Sign Up';
                                    switchFormText.innerHTML = 'Already have an account? <a href="#" id="switch-form" class="text-brand-primary hover:underline">Login</a>';
                                    forgotPasswordText.classList.add('hidden');
                                }
                            }

                            if (forgotPasswordLink) {
                                e.preventDefault();
                                const loginForm = document.getElementById('login-form');
                                const forgotPasswordForm = document.getElementById('forgot-password-form');
                                const pageTitle = document.getElementById('page-title');

                                loginForm.classList.add('hidden');
                                forgotPasswordForm.classList.remove('hidden');
                                pageTitle.textContent = 'Forgot Password';
                                switchFormText.innerHTML = '<a href="#" id="switch-form" class="text-brand-primary hover:underline">Go Back To Login</a>';
                                forgotPasswordText.classList.add('hidden');
                            }
                        });

                        // Attach reCAPTCHA token to both forms
                        function attachRecaptchaToken(formId, action) {
                            const form = document.getElementById(formId);

                            form.addEventListener('submit', function(event) {
                                event.preventDefault();
                                grecaptcha.ready(function() {
                                    grecaptcha.execute('6LecM5UqAAAAAMqYOiInHn2Q0e_GwsJ-4AELU9oF', { action: action }).then(function(token) {
                                        const recaptchaInput = document.getElementById('recaptcha_token');
                                        recaptchaInput.value = token;
                                        form.submit();
                                    });
                                });
                            });
                        }

                        attachRecaptchaToken('login-form', 'login');
                        attachRecaptchaToken('signup-form', 'signup');

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

                        setupPasswordToggle('password', 'show-password');
                        setupPasswordToggle('signup_password', 'show-signup-password');
                    });
                </script>
<script>
  function validateFullName(input) {
    const sanitizedValue = input.value.replace(/[^a-zA-Z\s]/g, ''); // Allow only letters and spaces
    if (input.value !== sanitizedValue) {
      input.value = sanitizedValue; // Update input to sanitized value
    }
  }
</script>
                <div class="text-center mt-4">
                    <p id="switch-form-text" class="text-sm text-gray-600">
                        Don't have an account?
                        <a href="#" id="switch-form" class="text-brand-primary hover:underline">Sign Up</a>
                    </p>
                    <p id="forgot-password-text" class="text-sm text-gray-600">
                        <a href="#" id="forgot-password-link" class="text-brand-primary hover:underline">Forgot Password?</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js?render=6LecM5UqAAAAAMqYOiInHn2Q0e_GwsJ-4AELU9oF"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const message = <?php echo isset($_SESSION['message']) ? $_SESSION['message'] : 'null'; ?>;
    if (message) {
        Swal.fire({
            icon: message.type,
            text: message.text,
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['message']); ?>
    }
});
</script>
    
<?php include_once('layouts/footer.php'); ?>
</body>
</html>