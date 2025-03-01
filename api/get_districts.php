<?php
require_once '../includes/vietnam_cities.php';
header('Content-Type: application/json');

$city = isset($_GET['city']) ? $_GET['city'] : '';

if (empty($city) || !isset($vietnam_cities[$city])) {
    echo json_encode([
        'success' => false,
        'message' => 'Thành phố không hợp lệ'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'districts' => $vietnam_cities[$city]
]); 