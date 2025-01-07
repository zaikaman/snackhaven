<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý thêm sản phẩm
if(isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Xử lý upload hình ảnh
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/products/";
        if(!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Kiểm tra file hợp lệ
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        if(in_array($file_extension, $allowed_types)) {
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = "assets/images/products/" . $new_filename;
                
                try {
                    $stmt = $pdo->prepare("INSERT INTO products (name, category_id, price, description, image_url) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $category_id, $price, $description, $image_url]);
                    
                    header('Location: products.php?success=added');
                    exit();
                } catch(PDOException $e) {
                    header('Location: products.php?error=db_error');
                    exit();
                }
            } else {
                header('Location: products.php?error=upload_failed');
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
            // Xử lý upload hình ảnh mới
            $target_dir = "../assets/images/products/";
            if(!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
            if(in_array($file_extension, $allowed_types)) {
                if(move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    $image_url = "assets/images/products/" . $new_filename;
                    
                    // Cập nhật với hình ảnh mới
                    $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, description = ?, image_url = ? WHERE id = ?");
                    $stmt->execute([$name, $category_id, $price, $description, $image_url, $product_id]);
                }
            }
        } else {
            // Cập nhật không có hình ảnh mới
            $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, description = ? WHERE id = ?");
            $stmt->execute([$name, $category_id, $price, $description, $product_id]);
        }
        
        header('Location: products.php?success=updated');
        exit();
    } catch(PDOException $e) {
        header('Location: products.php?error=update_failed');
        exit();
    }
} 