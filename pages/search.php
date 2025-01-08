<?php
require_once __DIR__ . '/../includes/config.php';

// Lấy từ khóa tìm kiếm
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$priceRange = isset($_GET['priceRange']) ? $_GET['priceRange'] : '';
?>

<style>
/* Style cho form tìm kiếm */
.search-form-container {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 30px;
}

.search-form-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
    gap: 10px;
}

.search-form-header i {
    color: #ff6b6b;
    font-size: 1.2rem;
}

.search-form-header span {
    font-size: 1.1rem;
    color: #333;
}

.search-form-content {
    display: grid;
    grid-template-columns: 1fr 1fr auto auto;
    gap: 15px;
    align-items: start;
}

.form-group {
    margin: 0;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #666;
    font-size: 0.9rem;
}

.form-select {
    width: 100%;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
    color: #333;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-select:focus {
    border-color: #ff6b6b;
    box-shadow: 0 0 0 2px rgba(255,107,107,0.1);
    outline: none;
}

.btn-reset, .btn-filter {
    height: 42px;
    padding: 0 20px;
    border-radius: 8px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    white-space: nowrap;
    margin-top: 32px;
}

.btn-reset {
    background: #fff;
    border: 1px solid #ddd;
    color: #666;
}

.btn-reset:hover {
    border-color: #ff6b6b;
    color: #ff6b6b;
    background: #fff;
}

.btn-filter {
    background: #ff6b6b;
    border: none;
    color: white;
}

.btn-filter:hover {
    background: #ff5252;
    transform: translateY(-1px);
}

@media (max-width: 992px) {
    .search-form-content {
        grid-template-columns: 1fr 1fr;
    }
}

@media (max-width: 576px) {
    .search-form-content {
        grid-template-columns: 1fr;
    }
    
    .btn-reset, .btn-filter {
        width: 100%;
    }
}

.filter-section {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
}

.form-label {
    color: #666;
    font-weight: 500;
    margin-bottom: 5px;
}

.form-select {
    border: 1px solid #ddd;
    padding: 8px 12px;
    border-radius: 6px;
    width: 100%;
    margin-bottom: 10px;
}

.form-select:focus {
    border-color: #ff6b6b;
    box-shadow: 0 0 0 0.2rem rgba(255,107,107,0.15);
}

.btn-filter {
    background: #ff6b6b;
    border: none;
    padding: 8px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 6px;
}

.btn-filter:hover {
    background: #ff5252;
    transform: translateY(-1px);
}

.btn-reset {
    background: #fff;
    border: 1px solid #ddd;
    color: #666;
    padding: 8px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 6px;
}

.btn-reset:hover {
    background: #f8f9fa;
    border-color: #ff6b6b;
    color: #ff6b6b;
}

.search-keyword {
    background: #fff;
    border-radius: 6px;
    padding: 10px 15px;
    margin-bottom: 15px;
    color: #666;
    font-size: 0.95rem;
}

.search-keyword i {
    color: #ff6b6b;
}

.search-keyword strong {
    color: #333;
}

.card {
    border: none;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    margin-bottom: 20px;
    border-radius: 10px;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 180px;
    object-fit: cover;
}

.card-body {
    padding: 15px;
}

.card-title {
    font-family: "Times New Roman", Times, serif;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 8px;
    color: #333;
    height: 40px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.product-price {
    font-family: "Times New Roman", Times, serif;
    color: #ff6b6b;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.category-name {
    color: #666;
    font-size: 0.85rem;
    margin-bottom: 8px;
    background: #f8f9fa;
    padding: 3px 8px;
    border-radius: 12px;
    display: inline-block;
}

.btn-primary {
    background: #ff6b6b;
    border: none;
    padding: 8px 15px;
    transition: all 0.3s ease;
    border-radius: 6px;
    width: 100%;
    font-size: 0.95rem;
}

.btn-primary:hover {
    background: #ff5252;
    transform: translateY(-1px);
}

/* Pagination styles */
.pagination {
    gap: 3px;
    margin: 1.5rem auto;
}

.pagination .page-link {
    color: #ff6b6b;
    border: 1px solid #ff6b6b;
    padding: 6px 12px;
    min-width: 35px;
    height: 35px;
    font-size: 0.9rem;
    border-radius: 6px;
}

.pagination .page-link:hover {
    background: #ff6b6b;
    color: white;
}

.pagination .active .page-link {
    background: #ff6b6b;
    border-color: #ff6b6b;
    color: white;
}

.pagination .disabled .page-link {
    color: #ccc;
    border-color: #eee;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.spinner-border {
    width: 2.5rem;
    height: 2.5rem;
    color: #ff6b6b;
}

#searchResults {
    margin: 0 -10px;
}

#searchResults .col-md-3 {
    padding: 0 10px;
}

@media (max-width: 768px) {
    .btn-filter, .btn-reset {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<div class="container-fluid py-4">
    <div class="search-form-container">
        <?php if (!empty($keyword)): ?>
            <div class="search-form-header">
                <i class="fas fa-search"></i>
                <span>Kết quả tìm kiếm cho "<strong><?php echo htmlspecialchars($keyword); ?></strong>"</span>
            </div>
        <?php endif; ?>

        <form id="searchForm">
            <?php if (!empty($keyword)): ?>
                <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
            <?php endif; ?>

            <div class="search-form-content">
                <div class="form-group">
                    <label for="category">Danh mục</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Tất cả danh mục</option>
                        <?php
                        $catQuery = "SELECT * FROM categories ORDER BY name";
                        $categories = $pdo->query($catQuery)->fetchAll();
                        foreach ($categories as $cat) {
                            $selected = $category == $cat['id'] ? 'selected' : '';
                            echo "<option value='{$cat['id']}' $selected>" . htmlspecialchars($cat['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="priceRange">Khoảng giá</label>
                    <select class="form-select" id="priceRange" name="priceRange">
                        <option value="">Tất cả giá</option>
                        <option value="0-50000" <?php echo $priceRange == '0-50000' ? 'selected' : ''; ?>>Dưới 50.000đ</option>
                        <option value="50000-100000" <?php echo $priceRange == '50000-100000' ? 'selected' : ''; ?>>50.000đ - 100.000đ</option>
                        <option value="100000-200000" <?php echo $priceRange == '100000-200000' ? 'selected' : ''; ?>>100.000đ - 200.000đ</option>
                        <option value="200000-500000" <?php echo $priceRange == '200000-500000' ? 'selected' : ''; ?>>200.000đ - 500.000đ</option>
                        <option value="500000" <?php echo $priceRange == '500000' ? 'selected' : ''; ?>>Trên 500.000đ</option>
                    </select>
                </div>

                <a href="<?php echo url('search' . (!empty($keyword) ? '?keyword=' . urlencode($keyword) : '')); ?>" class="btn btn-reset">
                    <i class="fas fa-undo-alt"></i>
                    Đặt lại
                </a>

                <button type="submit" class="btn btn-filter">
                    <i class="fas fa-filter"></i>
                    Lọc kết quả
                </button>
            </div>
        </form>
    </div>

    <!-- Hiển thị kết quả -->
    <div class="container">
        <div id="searchResults" class="row">
            <!-- Kết quả sẽ được load bằng AJAX -->
        </div>

        <!-- Phân trang -->
        <div id="pagination" class="d-flex justify-content-center mt-4">
            <!-- Phân trang sẽ được load bằng AJAX -->
        </div>
    </div>

    <!-- Loading overlay -->
    <div id="loading" class="loading-overlay d-none">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
    </div>
</div>

<!-- Script phần cũ giữ nguyên -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Hàm load kết quả tìm kiếm
    function loadSearchResults(page = 1) {
        // Hiển thị loading
        $('#loading').removeClass('d-none');

        // Lấy tất cả tham số tìm kiếm
        const params = new URLSearchParams(window.location.search);
        params.set('page', page);

        // Gọi API
        $.ajax({
            url: '<?php echo url("api/search.php"); ?>',
            type: 'GET',
            data: params.toString(),
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    // Hiển thị lỗi nếu có
                    $('#searchResults').html(
                        '<div class="col-12"><div class="alert alert-danger">' + 
                        '<i class="fas fa-exclamation-circle me-2"></i>' +
                        'Có lỗi xảy ra: ' + response.message + 
                        '</div></div>'
                    );
                } else {
                    // Cập nhật URL mà không reload trang
                    const newUrl = window.location.pathname + '?' + params.toString();
                    window.history.pushState({ path: newUrl }, '', newUrl);

                    // Cập nhật kết quả
                    $('#searchResults').html(response.html);
                    $('#pagination').html(response.pagination);

                    // Hiển thị thông báo nếu không có kết quả
                    if (response.total === 0) {
                        $('#searchResults').html(
                            '<div class="col-12 text-center py-5">' +
                            '<i class="fas fa-search fa-3x text-muted mb-3"></i>' +
                            '<h5 class="text-muted">Không tìm thấy sản phẩm nào phù hợp</h5>' +
                            '<p class="text-muted">Vui lòng thử lại với từ khóa khác hoặc điều chỉnh bộ lọc</p>' +
                            '</div>'
                        );
                    }
                }

                // Ẩn loading
                $('#loading').addClass('d-none');

                // Scroll lên đầu kết quả nếu đang ở trang khác
                if (page > 1) {
                    $('html, body').animate({
                        scrollTop: $('#searchResults').offset().top - 100
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                // Ẩn loading
                $('#loading').addClass('d-none');
                
                // Hiển thị lỗi
                $('#searchResults').html(
                    '<div class="col-12"><div class="alert alert-danger">' + 
                    '<i class="fas fa-exclamation-circle me-2"></i>' +
                    'Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại sau.<br>' +
                    'Chi tiết lỗi: ' + error +
                    '</div></div>'
                );
            }
        });
    }

    // Xử lý click vào nút phân trang
    $(document).on('click', '#pagination .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            loadSearchResults(page);
        }
    });

    // Xử lý form tìm kiếm
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const params = new URLSearchParams(formData);
        const newUrl = window.location.pathname + '?' + params.toString();
        window.history.pushState({ path: newUrl }, '', newUrl);
        loadSearchResults(1);
    });

    // Load kết quả ban đầu
    const params = new URLSearchParams(window.location.search);
    const page = params.get('page') || 1;
    loadSearchResults(page);
});
</script> 