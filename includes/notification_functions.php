<?php
/**
 * Create a new notification
 *
 * @param PDO $pdo Database connection
 * @param string $title Notification title
 * @param string $message Notification message
 * @param string $type Notification type (appointment, status, system, etc.)
 * @param int|null $relatedId Related entity ID (e.g., appointment ID, pet ID)
 * @param string|null $relatedUrl URL to the related entity page
 * @return bool Success status
 */
function createNotification($pdo, $title, $message, $type = 'general', $relatedId = null, $relatedUrl = null)
{
    try {
        $stmt = $pdo->prepare("
            INSERT INTO notifications (Title, Message, Type, RelatedId, RelatedUrl, CreatedAt)
            VALUES (:title, :message, :type, :relatedId, :relatedUrl, NOW())
        ");

        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':relatedId', $relatedId, PDO::PARAM_INT);
        $stmt->bindParam(':relatedUrl', $relatedUrl, PDO::PARAM_STR);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log('Error creating notification: ' . $e->getMessage());
        return false;
    }
}

/**
 * Create an appointment notification
 *
 * @param PDO $pdo Database connection
 * @param int $appointmentId Appointment ID
 * @param string $petName Pet name
 * @param string $ownerName Owner name
 * @param string $status Appointment status
 * @param string $date Appointment date
 * @return bool Success status
 */
function createAppointmentNotification($pdo, $appointmentId, $petName, $ownerName, $status, $date)
{
    $title = "Appointment Update";
    $message = "Appointment for {$petName} ({$ownerName}) has been {$status} for {$date}.";
    $url = "appointment_details.php?id={$appointmentId}";

    return createNotification($pdo, $title, $message, 'appointment', $appointmentId, $url);
}

/**
 * Create a pet status notification
 *
 * @param PDO $pdo Database connection
 * @param int $petId Pet ID
 * @param string $petName Pet name
 * @param string $statusChange Status change description
 * @return bool Success status
 */
function createPetStatusNotification($pdo, $petId, $petName, $statusChange)
{
    $title = "Pet Status Update";
    $message = "{$petName}'s status has been updated: {$statusChange}";
    $url = "pet_details.php?id={$petId}";

    return createNotification($pdo, $title, $message, 'status', $petId, $url);
}

/**
 * Create a system notification
 *
 * @param PDO $pdo Database connection
 * @param string $title Notification title
 * @param string $message Notification message
 * @param string|null $url Related URL
 * @return bool Success status
 */
function createSystemNotification($pdo, $title, $message, $url = null)
{
    return createNotification($pdo, $title, $message, 'system', null, $url);
}

/**
 * Get all notifications with pagination
 *
 * @param PDO $pdo Database connection
 * @param int $page Page number
 * @param int $perPage Items per page
 * @return array Notifications and pagination info
 */
function getAllNotifications($pdo, $page = 1, $perPage = 20)
{
    try {
        // Calculate offset
        $offset = ($page - 1) * $perPage;

        // Get notifications
        $stmt = $pdo->prepare("
            SELECT * FROM notifications 
            ORDER BY CreatedAt DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count
        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM notifications");
        $countStmt->execute();
        $totalCount = $countStmt->fetchColumn();

        // Calculate total pages
        $totalPages = ceil($totalCount / $perPage);

        return [
            'notifications' => $notifications,
            'pagination' => [
                'currentPage' => $page,
                'perPage' => $perPage,
                'totalItems' => $totalCount,
                'totalPages' => $totalPages
            ]
        ];
    } catch (PDOException $e) {
        error_log('Error getting notifications: ' . $e->getMessage());
        return [
            'notifications' => [],
            'pagination' => [
                'currentPage' => 1,
                'perPage' => $perPage,
                'totalItems' => 0,
                'totalPages' => 0
            ]
        ];
    }
}
?>