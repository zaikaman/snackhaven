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

// Xử lý thêm sản phẩm
if(isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Xử lý upload hình ảnh
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        
        // Kiểm tra file hợp lệ
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if(in_array($file_extension, $allowed_types)) {
            try {
                // Upload ảnh lên ImgBB
                $image_url = $imgbb->upload($_FILES["image"]["tmp_name"]);
                
                // Lưu thông tin sản phẩm vào database
                $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category_id, $price, $description, $image_url]);
                
                header('Location: products.php?success=added');
                exit();
            } catch(Exception $e) {
                header('Location: products.php?error=' . urlencode($e->getMessage()));
                exit();
            }
        } else {
            header('Location: products.php?error=invalid_file');
            exit();
        }
    } else {
        header('Location: products.php?error=no_image');
        exit();
    }
}

// Xử lý sửa sản phẩm
if(isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    try {
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if(in_array($file_extension, $allowed_types)) {
                // Upload ảnh mới lên ImgBB
                $image_url = $imgbb->upload($_FILES["image"]["tmp_name"]);
                
                // Cập nhật với hình ảnh mới
                $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, description = ?, image_url = ? WHERE id = ?");
                $stmt->execute([$name, $category_id, $price, $description, $image_url, $product_id]);
            }
        } else {
            // Cập nhật không có hình ảnh mới
            $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $category_id, $price, $description, $product_id]);
        }
        
        header('Location: products.php?success=updated');
        exit();
    } catch(Exception $e) {
        header('Location: products.php?error=' . urlencode($e->getMessage()));
        exit();
    }
} 