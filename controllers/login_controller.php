<?php
// login_controller.php
session_start();
require_once '../config/database.php';
require_once '../services/LoginService.php';
require_once '../utils/security.php';
require_once '../utils/logging.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginService = new LoginService($pdo);
    $response = $loginService->handleLogin($_POST, $_SERVER['REMOTE_ADDR']);

    // Set any needed session values for displaying messages
    if (!$response['success']) {
        $_SESSION['errors'] = $response['errors'];
        // Also store the email to refill the form
        $_SESSION['form_email'] = htmlspecialchars($_POST['email']);

        // For debugging - log the actual error
        error_log("Login failed: " . implode(", ", $response['errors']));
    } else {
        // Clear any previous errors
        unset($_SESSION['errors']);
        unset($_SESSION['form_email']);

        // Handle "remember me" functionality
        if (isset($_POST['remember']) && $_POST['remember'] === 'on') {
            // Set a secure cookie that lasts 30 days
            $email = htmlspecialchars($_POST['email']);
            setcookie('remembered_email', $email, time() + (86400 * 30), "/", "", true, true);
        }
    }

    // Redirect based on login result
    header("Location: " . ($response['success'] ? $response['redirect'] : "../index.php"));
    exit();
} else {
    // If someone tries to access this file directly without POST
    header("Location: ../index.php");
    exit();
}
?>