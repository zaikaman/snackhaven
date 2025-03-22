<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/ImgBB.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Khởi tạo ImgBB với API key từ .env
$imgbb = new ImgBB(getenv('IMGBB_API_KEY'));

// Xử lý thêm danh mục
if(isset($_POST['add_category'])) {
    $name = $_POST['name'];
    
    // Xử lý upload hình ảnh
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        
        // Kiểm tra file hợp lệ
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if(in_array($file_extension, $allowed_types)) {
            try {
                // Upload ảnh lên ImgBB
                $image_url = $imgbb->upload($_FILES["image"]["tmp_name"]);
                
                // Lưu thông tin danh mục vào database
                $stmt = $pdo->prepare("INSERT INTO categories (name, image_url) VALUES (?, ?)");
                $stmt->execute([$name, $image_url]);
                
                header('Location: categories.php?success=added');
                exit();
            } catch(Exception $e) {
                header('Location: categories.php?error=' . urlencode($e->getMessage()));
                exit();
            }
        } else {
            header('Location: categories.php?error=invalid_file');
            exit();
        }
    } else {
        header('Location: categories.php?error=no_image');
        exit();
    }
}

// Xử lý sửa danh mục
if(isset($_POST['update_category'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    
    try {
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if(in_array($file_extension, $allowed_types)) {
                // Upload ảnh mới lên ImgBB
                $image_url = $imgbb->upload($_FILES["image"]["tmp_name"]);
                
                // Cập nhật với hình ảnh mới
                $stmt = $pdo->prepare("UPDATE categories SET name = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$name, $image_url, $id]);
            }
        } else {
            // Cập nhật không có hình ảnh mới
            $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
            $stmt->execute([$name, $id]);
        }
        
        header('Location: categories.php?success=updated');
        exit();
    } catch(Exception $e) {
        header('Location: categories.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} 