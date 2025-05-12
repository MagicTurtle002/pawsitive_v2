<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/dbh.inc.php';

session_start();

if (empty($_SESSION['csrf_token']) || time() - ($_SESSION['token_time'] ?? 0) > 3600) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['token_time'] = time();
}

$remembered_email = '';
if (isset($_COOKIE['pawsitive_remembered_email'])) {
    $remembered_email = htmlspecialchars($_COOKIE['pawsitive_remembered_email']);
}

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? '';
unset($_SESSION['errors'], $_SESSION['success']);

$email = $_SESSION['temp_email'] ?? $remembered_email;
unset($_SESSION['temp_email']);

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com 'unsafe-inline'; style-src 'self' https://fonts.googleapis.com https://cdnjs.cloudflare.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:; img-src 'self' data:;");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("Referrer-Policy: strict-origin-when-cross-origin");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Secure login portal for Pawsitive staff members">
    <meta name="keywords" content="Pawsitive, Pet Management, Staff Login, Veterinary Software">
    <meta name="author" content="Pawsitive">
    <meta name="theme-color" content="#156f77">

    <title>Staff Login | Pawsitive</title>

    <link rel="icon" type="image/x-icon" href="assets/images/logo/LOGO.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'pawsitive': {
                            50: '#e6f7f8',
                            100: '#cceff2',
                            200: '#a8ebf0',
                            300: '#71dee7',
                            400: '#3dccd9',
                            500: '#25b2c0',
                            600: '#1a8f9c',
                            700: '#156f77', // Primary brand color
                            800: '#114f54',
                            900: '#0a2f32',
                        },
                    },
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    animation: {
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                }
            }
        }
    </script>
</head>

<body class="font-poppins bg-gray-50 min-h-screen">
    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include 'components/left-panel.php'; ?>

        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-4 sm:px-8 md:px-16 py-12 relative">
            <!-- Mobile Logo (shown only on smaller screens) -->
            <div class="lg:hidden flex justify-center mb-8">
                <img src="assets/images/logo/LOGO.png" alt="Pawsitive Logo" class="h-16">
            </div>

            <div class="max-w-md w-full mx-auto">
                <div class="mb-10">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Staff Login</h2>
                    <p class="text-gray-600">Please sign in to access your dashboard</p>
                </div>

                <?php include 'components/alert-components.php'; ?>

                <!-- Login Form -->
                <form id="loginForm" action="src/staff_login_handler.php" method="POST" class="space-y-6" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                            Email Address<span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email"
                                class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pawsitive-500 focus:border-pawsitive-500 sm:text-sm"
                                placeholder="name@pawsitive.com" value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Password<span class="text-red-500">*</span>
                            </label>
                            <a href="public/forgot_password.php"
                                class="text-xs font-medium text-pawsitive-600 hover:text-pawsitive-500 transition-colors">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input type="password" id="password" name="password"
                                class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:ring-pawsitive-500 focus:border-pawsitive-500 sm:text-sm"
                                placeholder="••••••••" required>
                            <button type="button"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePassword('password', this)" aria-label="Toggle password visibility">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-pawsitive-600 focus:ring-pawsitive-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember my email
                        </label>
                    </div>

                    <!-- Login Button -->
                    <div>
                        <button type="submit"
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-pawsitive-700 hover:bg-pawsitive-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pawsitive-500 transition-colors">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <i
                                    class="fas fa-sign-in-alt text-pawsitive-300 group-hover:text-pawsitive-200 transition-colors"></i>
                            </span>
                            Sign in
                        </button>
                    </div>

                    <!-- Help Link -->
                    <div class="text-sm text-center">
                        <span class="text-gray-600">Need help? </span>
                        <a href="contact_support.php"
                            class="font-medium text-pawsitive-600 hover:text-pawsitive-500 transition-colors">
                            Contact support
                        </a>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <?php include 'components/footer.php'; ?>
        </div>
    </div>
    <script src="assets/js/login.js"></script>
</body>

</html>