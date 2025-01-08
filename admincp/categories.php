<?php
include_once '../includes/config.php';
include_once 'includes/header.php';

// Xử lý thêm danh mục
if (isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $image_url = $_POST['image_url'];
    
    $sql = "INSERT INTO categories (name, image_url) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    
    try {
        if ($stmt->execute([$name, $image_url])) {
            echo "<script>alert('Thêm danh mục thành công!');</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Có lỗi xảy ra: " . $e->getMessage() . "');</script>";
    }
}

// Xử lý xóa danh mục
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    $sql = "DELETE FROM categories WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    try {
        if ($stmt->execute([$id])) {
            echo "<script>alert('Xóa danh mục thành công!');</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Có lỗi xảy ra: " . $e->getMessage() . "');</script>";
    }
}

// Xử lý cập nhật danh mục
if (isset($_POST['update_category'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $image_url = $_POST['image_url'];
    
    $sql = "UPDATE categories SET name = ?, image_url = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    
    try {
        if ($stmt->execute([$name, $image_url, $id])) {
            echo "<script>alert('Cập nhật danh mục thành công!');</script>";
        }
    } catch(PDOException $e) {
        echo "<script>alert('Có lỗi xảy ra: " . $e->getMessage() . "');</script>";
    }
}

// Lấy danh sách danh mục
try {
    $sql = "SELECT * FROM categories ORDER BY id DESC";
    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll();
} catch(PDOException $e) {
    echo "<script>alert('Có lỗi xảy ra khi lấy danh sách danh mục: " . $e->getMessage() . "');</script>";
    $categories = [];
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Quản lý danh mục</h1>

    <!-- Form thêm danh mục -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thêm danh mục mới</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Tên danh mục:</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                    <label>URL hình ảnh:</label>
                    <input type="text" class="form-control" name="image_url" required>
                </div>
                <button type="submit" name="add_category" class="btn btn-primary">Thêm danh mục</button>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách danh mục -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách danh mục</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên danh mục</th>
                            <th>Hình ảnh</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $row): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td>
                                <img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['name']; ?>" style="max-width: 100px;">
                            </td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                    Sửa
                                </button>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">Xóa</a>
                            </td>
                        </tr>

                        <!-- Modal sửa danh mục -->
                        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Sửa danh mục</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="">
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <div class="form-group">
                                                <label>Tên danh mục:</label>
                                                <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>URL hình ảnh:</label>
                                                <input type="text" class="form-control" name="image_url" value="<?php echo $row['image_url']; ?>" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" name="update_category" class="btn btn-primary">Lưu thay đổi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 