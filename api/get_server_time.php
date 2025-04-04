<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

try {
    // Lấy thời gian từ database (UTC+7)
    $stmt = $pdo->query("SELECT NOW() as server_time");
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'server_time' => $result['server_time']
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 