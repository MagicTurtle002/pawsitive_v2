<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require __DIR__ . '/../config/dbh.inc.php';
require __DIR__ . '/../src/helpers/auth_helpers.php';
require __DIR__ . '/../src/helpers/log_helpers.php';


$userId = $_SESSION['UserId'];
$userName = isset($_SESSION['FirstName']) ? $_SESSION['FirstName'] . ' ' . $_SESSION['LastName'] : 'Staff';
$role = $_SESSION['Role'] ?? 'Role';

function respondWithJson($data, $statusCode = 200) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondWithJson(['success' => false, 'message' => 'Invalid request method. Only POST is allowed.'], 405);
}

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if ($data === null) {
    respondWithJson(['success' => false, 'message' => 'Invalid JSON format.'], 400);
}

$appointmentId = $data['appointment_id'] ?? null;
$status = $data['status'] ?? null;
$petId = $data['pet_id'] ?? null;
$reason = $data['reason'] ?? null;

if (!$appointmentId || !$status || !$petId) {
    respondWithJson(['success' => false, 'message' => 'Missing or invalid input.'], 400);
}

if ($status === "Declined" && empty($reason)) {
    respondWithJson(['success' => false, 'message' => 'A reason for declining is required.'], 400);
}

try {
    $checkStmt = $pdo->prepare("SELECT Status FROM Appointments WHERE AppointmentId = :AppointmentId AND PetId = :PetId");
    $checkStmt->execute([
        ':AppointmentId' => $appointmentId,
        ':PetId' => $petId,
    ]);

    $currentStatus = $checkStmt->fetchColumn();

    if ($currentStatus === false) {
        respondWithJson(['success' => false, 'message' => 'Appointment not found.'], 404);
    }

    if (in_array($currentStatus, ['Done', 'Paid', 'Declined']) && $status === 'Declined') {
        echo json_encode(['success' => false, 'message' => '❌ Cannot decline a locked appointment.']);
        exit;
    }

    $query = "UPDATE Appointments SET Status = :Status";
    $params = [
        ':Status' => $status,
        ':AppointmentId' => $appointmentId,
        ':PetId' => $petId,
    ];

    if ($status === "Declined") {
        $query .= ", Reason = :Reason";
        $params[':Reason'] = $reason;
    }

    $query .= " WHERE AppointmentId = :AppointmentId AND PetId = :PetId";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    if ($stmt->rowCount() > 0) {
        $actionDetails = "$userName updated Appointment #$appointmentId to '$status'.";

        logActivity($pdo, $userId, $userName, $role, 'Update Appointment', $actionDetails);

        respondWithJson(['success' => true, 'message' => "Appointment status updated to {$status}."]);
    } else {
        respondWithJson(['success' => false, 'message' => 'Failed to update appointment status.'], 200);
    }
} catch (PDOException $e) {
    error_log('Database error in update_appointment.php: ' . $e->getMessage());

    respondWithJson(['success' => false, 'message' => 'An internal error occurred. Please try again later.'], 500);
}