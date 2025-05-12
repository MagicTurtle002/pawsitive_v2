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

// Get notification ID from POST request
$data = json_decode(file_get_contents('php://input'), true);
$notificationId = isset($data['id']) ? intval($data['id']) : 0;

if ($notificationId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
    exit;
}

try {
    // Update notification status
    $stmt = $pdo->prepare("UPDATE notifications SET IsRead = 1 WHERE NotificationId = :id");
    $stmt->bindParam(':id', $notificationId, PDO::PARAM_INT);
    $success = $stmt->execute();

    // Get updated unread count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE IsRead = 0");
    $countStmt->execute();
    $unreadCount = $countStmt->fetchColumn();

    echo json_encode([
        'success' => $success,
        'unreadCount' => $unreadCount
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>