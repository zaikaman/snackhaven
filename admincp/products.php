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

// Xử lý hiện lại sản phẩm
if(isset($_POST['restore_product'])) {
    $product_id = $_POST['product_id'];
    try {
        $stmt = $pdo->prepare("UPDATE products SET active = 1 WHERE id = ?");
        $stmt->execute([$product_id]);
        $success = "Đã hiện lại sản phẩm thành công";
    } catch(PDOException $e) {
        $error = "Lỗi khi xử lý sản phẩm: " . $e->getMessage();
    }
}

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

        <!-- Form tìm kiếm -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-11">
                        <input type="text" class="form-control" id="searchKeyword" placeholder="Nhập tên sản phẩm để tìm kiếm...">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
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
                        <tbody id="productsTableBody">
                            <!-- Dữ liệu sẽ được load bằng AJAX -->
                        </tbody>
                    </table>
                    <!-- Phân trang -->
                    <nav aria-label="Product pagination" class="d-flex justify-content-center mt-4">
                        <ul class="pagination" id="pagination">
                            <!-- Các nút phân trang sẽ được tạo bằng JavaScript -->
                        </ul>
                    </nav>
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
    // Biến để lưu trữ trang hiện tại
    let currentPage = 1;

    // Biến lưu trữ các filter
    let filters = {
        page: 1,
        keyword: ''
    };

    // Hàm load sản phẩm
    function loadProducts(page = null) {
        if (page) filters.page = page;
        
        const queryParams = new URLSearchParams({
            page: filters.page,
            keyword: filters.keyword
        });

        fetch(`get_products_paginated.php?${queryParams}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('productsTableBody');
                    let html = '';
                    
                    if (data.products.length === 0) {
                        html = `
                            <tr>
                                <td colspan="7" class="text-center">Không tìm thấy sản phẩm nào</td>
                            </tr>
                        `;
                    } else {
                        data.products.forEach(product => {
                            html += `
                                <tr ${!product.active ? 'class="table-secondary"' : ''}>
                                    <td>${product.id}</td>
                                    <td>
                                        <img src="${product.image_url}" 
                                             alt="${product.name}"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        ${product.name}
                                        ${!product.active ? '<span class="badge bg-secondary">Đã ẩn</span>' : ''}
                                    </td>
                                    <td>${product.category_name}</td>
                                    <td>${new Intl.NumberFormat('vi-VN').format(product.price)}đ</td>
                                    <td>${product.description}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-product" 
                                                data-id="${product.id}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editProductModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        ${!product.active ? `
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="product_id" value="${product.id}">
                                                <button type="submit" name="restore_product" class="btn btn-sm btn-success" title="Hiện lại sản phẩm">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </form>
                                        ` : ''}
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('${product.active ? 'Bạn có chắc muốn xóa sản phẩm này?' : 'Sản phẩm này đã bị ẩn. Bạn có chắc muốn xóa hoàn toàn?'}');">
                                            <input type="hidden" name="product_id" value="${product.id}">
                                            <button type="submit" name="delete_product" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    
                    tbody.innerHTML = html;

                    // Cập nhật phân trang
                    updatePagination(data.pagination);
                    
                    // Gán lại event listeners cho các nút sửa
                    attachEditEventListeners();
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Hàm cập nhật phân trang
    function updatePagination(pagination) {
        const paginationElement = document.getElementById('pagination');
        let html = '';
        
        // Nút Previous
        html += `
            <li class="page-item ${pagination.current_page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        `;
        
        // Các nút số trang
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (
                i === 1 || // Trang đầu
                i === pagination.total_pages || // Trang cuối
                (i >= pagination.current_page - 2 && i <= pagination.current_page + 2) // 2 trang trước và sau trang hiện tại
            ) {
                html += `
                    <li class="page-item ${pagination.current_page === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            } else if (
                i === pagination.current_page - 3 ||
                i === pagination.current_page + 3
            ) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Nút Next
        html += `
            <li class="page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        `;
        
        paginationElement.innerHTML = html;
        
        // Thêm event listeners cho các nút phân trang
        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                if (!isNaN(page) && page > 0) {
                    currentPage = page;
                    loadProducts(page);
                }
            });
        });
    }

    // Hàm gán event listeners cho các nút sửa
    function attachEditEventListeners() {
        document.querySelectorAll('.edit-product').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.id;
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
    }

    // Load sản phẩm khi trang được tải
    document.addEventListener('DOMContentLoaded', () => {
        loadProducts(1);
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

    // Hàm áp dụng filter
    function applyFilters() {
        filters.keyword = document.getElementById('searchKeyword').value;
        filters.page = 1; // Reset về trang 1 khi lọc
        loadProducts();
    }

    // Thêm debounce cho input tìm kiếm
    let searchTimeout;
    document.getElementById('searchKeyword').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });
    </script>
</body>
</html> 