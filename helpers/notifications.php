<?php
require_once __DIR__ . '/../config/dbh.inc.php';

// Get unread notifications count from database
function getUnreadNotificationsCount($pdo)
{
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE IsRead = 0");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0; // Return 0 on error
    }
}

// Get recent notifications
function getRecentNotifications($pdo, $limit = 5)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM notifications ORDER BY CreatedAt DESC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return []; // Return empty array on error
    }
}