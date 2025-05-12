<?php

function validateCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function hasTooManyLoginAttempts(PDO $pdo, string $ip, int $limit = 5, int $intervalMinutes = 15)
{
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM LoginAttempts 
            WHERE IPAddress = :ip AND AttemptTime > NOW() - INTERVAL :interval MINUTE
        ");
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':interval', $intervalMinutes, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() >= $limit;
    } catch (PDOException $e) {
        error_log("Database Error in hasTooManyLoginAttempts: " . $e->getMessage());
        return false;
    }
}

function recordLoginAttempt(PDO $pdo, string $ip)
{
    try {
        $stmt = $pdo->prepare("
            INSERT INTO LoginAttempts (IPAddress, AttemptTime) 
            VALUES (:ip, NOW())
        ");
        $stmt->execute([':ip' => $ip]);
    } catch (PDOException $e) {
        error_log("Database Error in recordLoginAttempt: " . $e->getMessage());
    }
}


function getUserByEmail(PDO $pdo, string $email)
{
    try {
        $stmt = $pdo->prepare("
            SELECT Users.*, Roles.RoleName 
            FROM Users 
            INNER JOIN UserRoles ON Users.UserId = UserRoles.UserId 
            INNER JOIN Roles ON UserRoles.RoleId = Roles.RoleId 
            WHERE Email = :email
        ");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database Error in getUserByEmail: " . $e->getMessage());
        return false;
    }
}