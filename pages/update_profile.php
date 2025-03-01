<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $user_city = $_POST['user_city'];
    $user_district = $_POST['user_district'];
    $user_address = $_POST['user_address'];

    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, 
                user_city = ?, user_district = ?, user_address = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $first_name, $last_name, $phone,
            $user_city, $user_district, $user_address,
            $user_id
        ]);

        $_SESSION['success_message'] = "Thông tin đã được cập nhật thành công!";
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Có lỗi xảy ra: " . $e->getMessage();
    }
}

header('Location: profile.php');
exit(); 