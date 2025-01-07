<?php
require_once __DIR__ . '/../includes/config.php';
?>

<style>
.main-content {
    font-family: "Times New Roman", Times, serif;
    margin-top: 100px;
}

.main-content h1 {
    font-family: "Times New Roman", Times, serif;
    font-weight: bold;
}

.categories .nav-pills {
    gap: 10px;
    margin-bottom: 30px;
}

.categories .nav-link {
    font-family: "Times New Roman", Times, serif;
    color: #666;
    background: #f8f9fa;
    border-radius: 25px;
    padding: 10px 25px;
    transition: all 0.3s ease;
}

.categories .nav-link:hover {
    background: #ff6b6b;
    color: white;
}

.categories .nav-link.active {
    background: #ff6b6b;
    color: white;
}

.card {
    border: none;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 200px;
    object-fit: cover;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}

.card-body {
    padding: 20px;
}

.card-title {
    font-family: "Times New Roman", Times, serif;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.product-description {
    font-family: "Times New Roman", Times, serif;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 15px;
    height: 60px;
    overflow: hidden;
}

.product-price {
    font-family: "Times New Roman", Times, serif;
    color: #ff6b6b;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 15px;
}

.add-to-cart {
    font-family: "Times New Roman", Times, serif;
    background: #ff6b6b;
    border: none;
    width: 100%;
    padding: 10px;
    transition: background 0.3s ease;
}

.add-to-cart:hover {
    background: #ff5252;
}

.pagination {
    gap: 5px;
}

.pagination .page-link {
    font-family: "Times New Roman", Times, serif;
    color: #ff6b6b;
    border: 1px solid #ff6b6b;
    padding: 8px 16px;
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

.product-link {
    text-decoration: none;
}
</style>

<div class="main-content">
    <div class="container">
        <h1 class="text-center mb-4">Thực Đơn</h1>
        
        <!-- Danh mục -->
        <div class="categories mb-4">
            <ul class="nav nav-pills justify-content-center" id="menuTab" role="tablist">
                <?php
                try {
                    $sql = "SELECT * FROM categories ORDER BY id";
                    $stmt = $pdo->query($sql);
                    $first = true;
                    while ($category = $stmt->fetch()) {
                        $activeClass = $first ? 'active' : '';
                        echo "
                        <li class='nav-item' role='presentation'>
                            <button class='nav-link {$activeClass}' 
                                    id='cat-{$category['id']}-tab' 
                                    data-bs-toggle='pill'
                                    data-bs-target='#cat-{$category['id']}'
                                    type='button'
                                    role='tab'
                                    onclick='loadProducts({$category['id']}, 1)'>
                                {$category['name']}
                            </button>
                        </li>";
                        $first = false;
                    }
                } catch(PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </ul>
        </div>

        <!-- Khu vực hiển thị sản phẩm -->
        <div class="tab-content" id="menuTabContent">
            <?php
            try {
                $stmt->execute(); // Thực thi lại câu query để lấy dữ liệu từ đầu
                $first = true;
                while ($category = $stmt->fetch()) {
                    $activeClass = $first ? 'show active' : '';
                    echo "
                    <div class='tab-pane fade {$activeClass}' 
                         id='cat-{$category['id']}' 
                         role='tabpanel'>
                        <div class='products-container row' id='products-{$category['id']}'></div>
                        <div class='pagination-container text-center mt-4' id='pagination-{$category['id']}'></div>
                    </div>";
                    $first = false;
                }
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </div>
    </div>
</div>

<!-- Template cho sản phẩm -->
<template id="product-template">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <a href="<?php echo url('product'); ?>?id=" class="product-link">
                <img src="" class="card-img-top product-image" alt="">
                <div class="card-body">
                    <h5 class="card-title product-name"></h5>
                    <p class="card-text product-description"></p>
                    <p class="card-text product-price"></p>
                </div>
            </a>
            <div class="card-footer bg-white border-0">
                <button class="btn btn-primary add-to-cart w-100" onclick="handleAddToCart(event)">
                    <i class="fas fa-shopping-cart me-2"></i>
                    Thêm vào giỏ hàng
                </button>
            </div>
        </div>
    </div>
</template>

<script>
const PRODUCTS_PER_PAGE = 9;

function loadProducts(categoryId, page) {
    const baseUrl = window.location.origin + '/snackhaven/';
    const url = `${baseUrl}api/get_products.php?category_id=${categoryId}&page=${page}&per_page=${PRODUCTS_PER_PAGE}`;
    console.log('Fetching products from:', url);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data);
            if (!data.success) {
                console.error('API Error:', data.error);
                const container = document.getElementById(`products-${categoryId}`);
                container.innerHTML = `<div class="col-12 text-center py-5">
                    <h3 class="text-danger">Có lỗi xảy ra: ${data.error}</h3>
                </div>`;
                return;
            }
            displayProducts(categoryId, data.products);
            displayPagination(categoryId, data.total_pages, page);
        })
        .catch(error => {
            console.error('Error:', error);
            const container = document.getElementById(`products-${categoryId}`);
            container.innerHTML = `<div class="col-12 text-center py-5">
                <h3 class="text-danger">Không thể tải sản phẩm. Vui lòng thử lại sau.</h3>
                <p>${error.message}</p>
            </div>`;
        });
}

function displayProducts(categoryId, products) {
    const container = document.getElementById(`products-${categoryId}`);
    container.innerHTML = '';
    const template = document.getElementById('product-template');

    if (products.length === 0) {
        container.innerHTML = '<div class="col-12 text-center py-5"><h3>Không có sản phẩm nào trong danh mục này</h3></div>';
        return;
    }

    products.forEach(product => {
        const clone = template.content.cloneNode(true);
        
        const link = clone.querySelector('.product-link');
        link.href = `<?php echo url('product'); ?>?id=${product.id}`;
        
        const image = clone.querySelector('.product-image');
        image.src = product.image_url;
        image.alt = product.name;
        
        clone.querySelector('.product-name').textContent = product.name;
        clone.querySelector('.product-description').textContent = product.description;
        clone.querySelector('.product-price').textContent = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(product.price);
        
        container.appendChild(clone);
    });
}

function displayPagination(categoryId, totalPages, currentPage) {
    const container = document.getElementById(`pagination-${categoryId}`);
    container.innerHTML = '';
    
    if (totalPages <= 1) return;

    const ul = document.createElement('ul');
    ul.className = 'pagination justify-content-center';

    // Nút Previous
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<button class="page-link" onclick="loadProducts(${categoryId}, ${currentPage - 1})">
        <i class="fas fa-chevron-left"></i>
    </button>`;
    ul.appendChild(prevLi);

    // Các nút số trang
    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === currentPage ? 'active' : ''}`;
        li.innerHTML = `<button class="page-link" onclick="loadProducts(${categoryId}, ${i})">${i}</button>`;
        ul.appendChild(li);
    }

    // Nút Next
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<button class="page-link" onclick="loadProducts(${categoryId}, ${currentPage + 1})">
        <i class="fas fa-chevron-right"></i>
    </button>`;
    ul.appendChild(nextLi);

    container.appendChild(ul);
}

function handleAddToCart(event) {
    event.preventDefault();
    <?php if (!isset($_SESSION['user_id'])): ?>
        Swal.fire({
            title: 'Thông báo',
            text: 'Vui lòng đăng nhập để đặt hàng!',
            icon: 'warning',
            confirmButtonText: 'Đăng nhập',
            showCancelButton: true,
            cancelButtonText: 'Đóng'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'http://localhost/snackhaven/auth/login.php';
            }
        });
        return;
    <?php endif; ?>
    // Xử lý thêm vào giỏ hàng ở đây nếu đã đăng nhập
}

// Load sản phẩm cho category đầu tiên khi trang được tải
document.addEventListener('DOMContentLoaded', () => {
    const firstCategoryTab = document.querySelector('#menuTab .nav-link');
    if (firstCategoryTab) {
        const categoryId = firstCategoryTab.id.split('-')[1];
        loadProducts(categoryId, 1);
    }
});
</script> 