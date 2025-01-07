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

/* Loading Spinner */
.loading-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 300px;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #ff6b6b;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.menu-container {
    opacity: 0;
    transition: opacity 0.3s ease;
}

.menu-container.loaded {
    opacity: 1;
}
</style>

<div class="container main-content">
    <h1 class="text-center mb-5">Thực Đơn</h1>
    
    <!-- Loading Spinner -->
    <div class="loading-container" id="loadingSpinner">
        <div class="loading-spinner"></div>
    </div>

    <!-- Menu Content -->
    <div class="menu-container" id="menuContent" style="display: none;">
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
    
    // Hiển thị loading spinner trong container sản phẩm
    const container = document.getElementById(`products-${categoryId}`);
    container.innerHTML = `
        <div class="col-12">
            <div class="loading-container">
                <div class="loading-spinner"></div>
            </div>
        </div>
    `;
    
    // Ẩn phân trang khi đang loading
    const paginationContainer = document.getElementById(`pagination-${categoryId}`);
    paginationContainer.style.display = 'none';

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
                container.innerHTML = `<div class="col-12 text-center py-5">
                    <h3 class="text-danger">Có lỗi xảy ra: ${data.error}</h3>
                </div>`;
                return;
            }
            displayProducts(categoryId, data.products);
            // Hiển thị lại phân trang sau khi load xong
            paginationContainer.style.display = 'block';
            displayPagination(categoryId, data.total_pages, page);
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = `<div class="col-12 text-center py-5">
                <h3 class="text-danger">Không thể tải sản phẩm. Vui lòng thử lại sau.</h3>
                <p>${error.message}</p>
            </div>`;
            paginationContainer.style.display = 'none';
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
        
        const addToCartBtn = clone.querySelector('.add-to-cart');
        addToCartBtn.onclick = (e) => handleAddToCart(e, product);
        
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

// Khởi tạo giỏ hàng
let cart = JSON.parse(localStorage.getItem('cart')) || [];
updateCartDisplay();

function handleAddToCart(event, product) {
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

    // Thêm sản phẩm vào giỏ hàng
    const existingItem = cart.find(item => item.id === product.id);
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            image_url: product.image_url,
            quantity: 1
        });
    }

    // Lưu giỏ hàng vào localStorage
    localStorage.setItem('cart', JSON.stringify(cart));

    // Cập nhật hiển thị giỏ hàng
    updateCartDisplay();

    // Hiển thị thông báo thành công
    Swal.fire({
        title: 'Thành công!',
        text: 'Đã thêm sản phẩm vào giỏ hàng',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

function updateCartDisplay() {
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');
    const cartBadge = document.getElementById('cartBadge');

    // Kiểm tra xem các phần tử có tồn tại không
    if (!cartItems || !cartTotal || !cartBadge) {
        console.warn('Một số phần tử DOM cần thiết không tồn tại');
        return;
    }

    // Cập nhật số lượng trên badge
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartBadge.textContent = totalItems;

    // Cập nhật nội dung giỏ hàng
    cartItems.innerHTML = cart.map(item => `
        <div class="cart-item">
            <img src="${item.image_url}" alt="${item.name}">
            <div class="cart-item-details">
                <h4>${item.name}</h4>
                <p>${new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(item.price)}</p>
                <div class="cart-item-quantity">
                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                    <span>${item.quantity}</span>
                    <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                    <button class="quantity-btn" onclick="removeItem(${item.id})">&times;</button>
                </div>
            </div>
        </div>
    `).join('');

    // Cập nhật tổng tiền
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    cartTotal.textContent = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(total);
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            cart = cart.filter(item => item.id !== productId);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartDisplay();
    }
}

function removeItem(productId) {
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartDisplay();
}

// Load sản phẩm cho category đầu tiên khi trang được tải
document.addEventListener('DOMContentLoaded', () => {
    const firstCategoryTab = document.querySelector('#menuTab .nav-link');
    if (firstCategoryTab) {
        const categoryId = firstCategoryTab.id.split('-')[1];
        loadProducts(categoryId, 1);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo giỏ hàng
    updateCartDisplay();
});

// Simulate loading time and show/hide elements
document.addEventListener('DOMContentLoaded', function() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const menuContent = document.getElementById('menuContent');
    
    // Show content after a short delay (you can remove this setTimeout if you want it instant)
    setTimeout(() => {
        loadingSpinner.style.display = 'none';
        menuContent.style.display = 'block';
        // Add a small delay before adding the 'loaded' class for smooth fade in
        setTimeout(() => {
            menuContent.classList.add('loaded');
        }, 50);
    }, 800); // Adjust this time as needed
});
</script> 