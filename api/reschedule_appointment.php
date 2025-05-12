<?php

require_once __DIR__ . '../config/dbh.inc.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointmentId'])) {
    $appointmentId = $_POST['appointmentId'];
    $newDate = $_POST['newDate'];
    $newTime = $_POST['newTime'];

    $sql = "UPDATE Appointments
            SET AppointmentDate = :newDate, AppointmentTime = :newTime, UpdatedAt = NOW()
            WHERE AppointmentId = :appointmentId";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':newDate' => $newDate,
            ':newTime' => $newTime,
            ':appointmentId' => $appointmentId
        ]);
        echo "Rescheduled successfully";
    } catch (PDOException $e) {
        echo "Error rescheduling appointment: " . $e->getMessage();
    }
} else {
    echo "Invalid request";
}