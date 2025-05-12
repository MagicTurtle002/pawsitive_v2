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

// Get last check timestamp from session or set default
$lastCheck = isset($_SESSION['lastNotificationCheck']) ? $_SESSION['lastNotificationCheck'] : date('Y-m-d H:i:s', strtotime('-1 hour'));

try {
    // Check for new notifications since last check
    $stmt = $pdo->prepare("
        SELECT * FROM notifications 
        WHERE CreatedAt > :lastCheck
        ORDER BY CreatedAt DESC
        LIMIT 10
    ");
    $stmt->bindParam(':lastCheck', $lastCheck, PDO::PARAM_STR);
    $stmt->execute();
    $newNotifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get updated unread count
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE IsRead = 0");
    $countStmt->execute();
    $unreadCount = $countStmt->fetchColumn();
    
    // Update last check timestamp
    $_SESSION['lastNotificationCheck'] = date('Y-m-d H:i:s');
    
    echo json_encode([
        'success' => true,
        'hasNew' => count($newNotifications) > 0,
        'notifications' => $newNotifications,
        'unreadCount' => $unreadCount
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>