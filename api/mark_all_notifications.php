<?php
require __DIR__ . '/../config/init.php';
require __DIR__ . '/../config/dbh.inc.php';

header('Content-Type: application/json');

// Check if user is logged in
session_start();
if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

try {
    // Mark all notifications as read
    $stmt = $pdo->prepare("UPDATE notifications SET IsRead = 1 WHERE IsRead = 0");
    $success = $stmt->execute();
    
    echo json_encode([
        'success' => $success
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>