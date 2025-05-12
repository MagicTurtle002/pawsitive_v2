<?php
require_once __DIR__ . '/../config/dbh.inc.php';

function fetchData($pdo, $query, $params = []) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        return [];
    }
}

function getTotalRecords($pdo) {
    $query = "SELECT COUNT(*) AS TotalRecords FROM Pets";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['TotalRecords'] ?? 0;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return 0;
    }
}

function getAppointmentStats($pdo, $interval, $groupFormat, $groupAlias) {
    $query = "
        SELECT $groupFormat AS label, COUNT(*) AS count
        FROM Appointments
        WHERE AppointmentDate >= DATE_SUB(CURDATE(), INTERVAL $interval)
        GROUP BY label
        ORDER BY label
    ";
    return fetchData($pdo, $query);
}

function getUpcomingAppointments($pdo) {
    return fetchData($pdo, "
        SELECT a.AppointmentId, a.AppointmentDate, a.AppointmentTime, p.PetId, p.Name AS PetName, a.Status, s.ServiceName
        FROM Appointments AS a
        JOIN Pets AS p ON a.PetId = p.PetId
        JOIN Services AS s ON a.ServiceId = s.ServiceId
        WHERE a.Status IN ('Pending', 'Confirmed', 'Done')
        ORDER BY a.AppointmentDate ASC
        LIMIT 5
    ");
}

function getOverdueAppointments($pdo) {
    return fetchData($pdo, "
        SELECT * FROM Appointments
        WHERE AppointmentDate < CURDATE() AND Status = 'Pending'
    ");
}

function getRecentActivities($pdo) {
    return fetchData($pdo, "
        SELECT UserName, Role, PageAccessed, ActionDetails, CreatedAt
        FROM ActivityLog
        ORDER BY CreatedAt DESC
        LIMIT 5
    ");
}

function getSpeciesStats($pdo) {
    $speciesData = fetchData($pdo, "
        SELECT s.SpeciesName, COUNT(p.PetId) AS PetCount
        FROM Species s
        LEFT JOIN Pets p ON s.Id = p.SpeciesId
        GROUP BY s.SpeciesName
        ORDER BY PetCount DESC
    ");
    return [
        'labels' => array_column($speciesData, 'SpeciesName'),
        'counts' => array_column($speciesData, 'PetCount'),
    ];
}