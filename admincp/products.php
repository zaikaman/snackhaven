<?php
session_start();
require_once '../includes/config.php';

// Kiểm tra đăng nhập
if(!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Xử lý xóa sản phẩm
if(isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    try {
        // Kiểm tra xem sản phẩm đã được bán ra chưa
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as sold_count 
            FROM order_items 
            WHERE product_id = ?
        ");
        $stmt->execute([$product_id]);
        $result = $stmt->fetch();
        
        if ($result['sold_count'] > 0) {
            // Sản phẩm đã được bán ra, chỉ ẩn đi
            $stmt = $pdo->prepare("UPDATE products SET active = 0 WHERE id = ?");
            $stmt->execute([$product_id]);
            $success = "Đã ẩn sản phẩm khỏi menu (sản phẩm này đã có trong đơn hàng)";
        } else {
            // Sản phẩm chưa được bán ra, xóa hoàn toàn
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $success = "Đã xóa sản phẩm thành công";
        }
    } catch(PDOException $e) {
        $error = "Lỗi khi xử lý sản phẩm: " . $e->getMessage();
    }
}

// Lấy danh sách sản phẩm (bao gồm cả sản phẩm đã ẩn)
$stmt = $pdo->query("SELECT p.*, c.name as category_name 
                     FROM products p 
                     LEFT JOIN categories c ON p.category_id = c.id 
                     ORDER BY p.id DESC");
$products = $stmt->fetchAll();

// Lấy danh sách categories cho form thêm mới
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll();

$page_title = 'Quản lý Sản phẩm';
$current_page = 'products';
require_once 'includes/header.php';
?>

    <div class="container-fluid py-4">
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Quản lý Sản phẩm</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-lg"></i> Thêm sản phẩm
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Giá</th>
                                <th>Mô tả</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($products as $product): ?>
                            <tr <?php echo $product['active'] ? '' : 'class="table-secondary"'; ?>>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <img src="<?php echo $product['image_url']; ?>" 
                                         alt="<?php echo $product['name']; ?>"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td>
                                    <?php echo $product['name']; ?>
                                    <?php if (!$product['active']): ?>
                                        <span class="badge bg-secondary">Đã ẩn</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $product['category_name']; ?></td>
                                <td><?php echo number_format($product['price']); ?>đ</td>
                                <td><?php echo $product['description']; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-product" 
                                            data-id="<?php echo $product['id']; ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editProductModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form method="POST" class="d-inline" 
                                          onsubmit="return confirm('<?php echo $product['active'] ? 'Bạn có chắc muốn xóa sản phẩm này?' : 'Sản phẩm này đã bị ẩn. Bạn có chắc muốn xóa hoàn toàn?' ?>');">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <button type="submit" name="delete_product" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Thêm sản phẩm -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm sản phẩm mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_product.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select class="form-select" name="category_id" required>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="number" class="form-control" name="price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh</label>
                            <input type="file" class="form-control" name="image" accept="image/*" required onchange="previewImage(this, 'imagePreview')">
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img src="" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Thêm sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Sửa sản phẩm -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="process_product.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Danh mục</label>
                            <select class="form-select" name="category_id" id="edit_category_id" required>
                                <?php foreach($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="number" class="form-control" name="price" id="edit_price" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hình ảnh mới (để trống nếu không thay đổi)</label>
                            <input type="file" class="form-control" name="image" accept="image/*" onchange="previewImage(this, 'editImagePreview')">
                            <div id="editImagePreview" class="mt-2" style="display: none;">
                                <img src="" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" name="edit_product" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Xử lý khi click nút sửa
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            // Gọi API để lấy thông tin sản phẩm
            fetch(`get_product.php?id=${productId}`)
                .then(response => response.json())
                .then(product => {
                    document.getElementById('edit_product_id').value = product.id;
                    document.getElementById('edit_name').value = product.name;
                    document.getElementById('edit_category_id').value = product.category_id;
                    document.getElementById('edit_price').value = product.price;
                    document.getElementById('edit_description').value = product.description;
                });
        });
    });

    // Hàm xem trước ảnh
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const previewImg = preview.querySelector('img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            previewImg.src = '';
        }
    }
    </script>
</body>
</html> 