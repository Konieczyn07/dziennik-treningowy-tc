<?php
header('Content-Type: application/json; charset=UTF-8');

$health = ['status' => 'ok', 'service' => 'dziennik-treningowy-api'];

if (getenv('HEALTH_CHECK_DB') === 'true') {
    try {
        include_once 'config/database.php';
        $database = new Database();
        $conn = $database->getConnection();
        $conn->query('SELECT 1');
        $health['database'] = 'connected';
    } catch (Throwable $e) {
        http_response_code(503);
        $health['status'] = 'degraded';
        $health['database'] = 'unavailable';
    }
}

echo json_encode($health);
