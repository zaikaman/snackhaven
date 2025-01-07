<?php
require_once __DIR__ . '/../includes/config.php';

// Lấy product_id từ URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

<style>
.product-detail {
    font-family: "Times New Roman", Times, serif;
    margin-top: 10px;
    padding: 20px 0;
}

.product-image {
    width: 100%;
    height: 400px;
    object-fit: cover;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.product-info {
    padding: 20px;
}

.product-category {
    font-family: "Times New Roman", Times, serif;
    color: #ff6b6b;
    font-size: 1.1rem;
    margin-bottom: 10px;
}

.product-title {
    font-family: "Times New Roman", Times, serif;
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 20px;
    color: #333;
}

.product-description {
    font-family: "Times New Roman", Times, serif;
    font-size: 1.1rem;
    color: #666;
    line-height: 1.8;
    margin-bottom: 30px;
}

.product-price {
    font-family: "Times New Roman", Times, serif;
    font-size: 2rem;
    color: #ff6b6b;
    font-weight: bold;
    margin-bottom: 30px;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.quantity-btn {
    background: #fff;
    border: 2px solid #ff6b6b;
    color: #ff6b6b;
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quantity-btn:hover {
    background: #ff6b6b;
    color: #fff;
}

.quantity-input {
    width: 35px;
    height: 35px;
    text-align: center;
    border: 2px solid #ff6b6b;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 500;
    color: #ff6b6b;
    padding: 0;
}

.quantity-input:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(255,107,107,0.15);
}

/* Ẩn mũi tên tăng giảm mặc định của input number */
.quantity-input::-webkit-outer-spin-button,
.quantity-input::-webkit-inner-spin-button {
    appearance: none;
    -webkit-appearance: none;
    margin: 0;
}

.quantity-input[type=number] {
    appearance: textfield;
    -moz-appearance: textfield;
}

.add-to-cart-btn {
    font-family: "Times New Roman", Times, serif;
    background: #ff6b6b;
    color: white;
    border: none;
    padding: 15px 40px;
    font-size: 1.1rem;
    border-radius: 30px;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-bottom: 20px;
}

.add-to-cart-btn:hover {
    background: #ff5252;
    transform: translateY(-2px);
}

.related-products {
    padding: 50px 0;
    background: #f8f9fa;
}

.related-products h2 {
    font-family: "Times New Roman", Times, serif;
    text-align: center;
    margin-bottom: 30px;
    color: #333;
}

.related-product-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.related-product-card:hover {
    transform: translateY(-5px);
}

.related-product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.related-product-info {
    padding: 20px;
}

.related-product-title {
    font-family: "Times New Roman", Times, serif;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.related-product-price {
    font-family: "Times New Roman", Times, serif;
    color: #ff6b6b;
    font-weight: bold;
}

.breadcrumb {
    margin-bottom: 30px;
}

.breadcrumb-item a {
    color: #ff6b6b;
    text-decoration: none;
}

.breadcrumb-item.active {
    color: #666;
}

.product-link {
    text-decoration: none;
}

.related-product-title {
    font-family: "Times New Roman", Times, serif;
    font-size: 1.1rem;
    font-weight: bold;
    margin-bottom: 10px;
    color: #333;
}

.related-product-info {
    padding: 20px;
}

@media (max-width: 768px) {
    .product-image {
        height: 300px;
        margin-bottom: 20px;
    }
    
    .product-title {
        font-size: 2rem;
    }
    
    .product-price {
        font-size: 1.5rem;
    }
}

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
</style>

<!-- Loading Spinner -->
<div class="loading-container" id="loadingSpinner">
    <div class="loading-spinner"></div>
</div>

<!-- Product Content -->
<div id="productContent" style="display: none;">
    <div class="product-detail">
        <div class="container">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo url(); ?>">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo url('menu'); ?>">Thực đơn</a></li>
                    <li class="breadcrumb-item active product-category-text" aria-current="page"></li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-md-6">
                    <img src="" alt="" class="product-image" id="productImage">
                </div>
                <div class="col-md-6 product-info">
                    <div class="product-category"></div>
                    <h1 class="product-title"></h1>
                    <p class="product-description"></p>
                    <div class="product-price"></div>
                    
                    <div class="quantity-control">
                        <button class="quantity-btn" onclick="updateQuantity(-1)">-</button>
                        <input type="number" class="quantity-input" value="1" min="1" max="10" id="quantity">
                        <button class="quantity-btn" onclick="updateQuantity(1)">+</button>
                    </div>
                    
                    <button class="add-to-cart-btn">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Thêm vào giỏ hàng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sản phẩm liên quan -->
    <div class="related-products">
        <div class="container">
            <h2>Sản phẩm liên quan</h2>
            <div class="row" id="relatedProducts"></div>
        </div>
    </div>
</div>

<!-- Template cho sản phẩm liên quan -->
<template id="related-product-template">
    <div class="col-md-3 mb-4">
        <div class="related-product-card">
            <a href="" class="product-link">
                <img src="" alt="" class="related-product-image">
                <div class="related-product-info">
                    <h3 class="related-product-title"></h3>
                    <div class="related-product-price"></div>
                </div>
            </a>
        </div>
    </div>
</template>

<script>
// Lấy thông tin sản phẩm khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if (productId) {
        loadProductDetails(productId);
    }
});

function loadProductDetails(productId) {
    const baseUrl = window.location.origin + '/snackhaven/';
    
    // Hiển thị loading spinner và ẩn nội dung
    document.getElementById('loadingSpinner').style.display = 'flex';
    document.getElementById('productContent').style.display = 'none';
    
    fetch(`${baseUrl}api/get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Đảm bảo tất cả dữ liệu được hiển thị trước
                displayProductDetails(data.product);
                displayRelatedProducts(data.related_products);
                
                // Đợi một chút để đảm bảo hình ảnh đã được tải
                const productImage = document.getElementById('productImage');
                productImage.onload = function() {
                    // Ẩn loading spinner và hiển thị nội dung chỉ khi hình ảnh đã tải xong
                    document.getElementById('loadingSpinner').style.display = 'none';
                    document.getElementById('productContent').style.display = 'block';
                };
                
                // Fallback nếu hình ảnh lỗi hoặc không tải được
                productImage.onerror = function() {
                    document.getElementById('loadingSpinner').style.display = 'none';
                    document.getElementById('productContent').style.display = 'block';
                };
            } else {
                document.getElementById('loadingSpinner').style.display = 'none';
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: data.error
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('loadingSpinner').style.display = 'none';
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Không thể tải thông tin sản phẩm. Vui lòng thử lại sau.'
            });
        });
}

function displayProductDetails(product) {
    // Cập nhật breadcrumb
    document.querySelector('.product-category-text').textContent = product.category_name;
    
    // Cập nhật thông tin sản phẩm
    document.querySelector('.product-category').textContent = product.category_name;
    document.querySelector('.product-title').textContent = product.name;
    document.querySelector('.product-description').textContent = product.description;
    
    // Cập nhật giá và lưu giá gốc vào data-price
    const priceElement = document.querySelector('.product-price');
    priceElement.setAttribute('data-price', product.price);
    priceElement.textContent = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(product.price);
    
    // Cập nhật hình ảnh
    const productImage = document.getElementById('productImage');
    productImage.src = product.image_url;
    productImage.alt = product.name;
    
    // Cập nhật title của trang
    document.title = `${product.name} - SnackHaven`;
}

function displayRelatedProducts(products) {
    const container = document.getElementById('relatedProducts');
    const template = document.getElementById('related-product-template');
    
    container.innerHTML = '';
    
    products.forEach(product => {
        const clone = template.content.cloneNode(true);
        
        const link = clone.querySelector('.product-link');
        link.href = `<?php echo url('product'); ?>?id=${product.id}`;
        
        const image = clone.querySelector('.related-product-image');
        image.src = product.image_url;
        image.alt = product.name;
        
        clone.querySelector('.related-product-title').textContent = product.name;
        clone.querySelector('.related-product-price').textContent = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(product.price);
        
        container.appendChild(clone);
    });
}

function updateQuantity(change) {
    const input = document.getElementById('quantity');
    let value = parseInt(input.value) + change;
    
    // Giới hạn số lượng từ 1 đến 10
    value = Math.max(1, Math.min(10, value));
    input.value = value;
}

// Xử lý thêm vào giỏ hàng
document.querySelector('.add-to-cart-btn').addEventListener('click', function() {
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
                window.location.href = '<?php echo url("auth/login.php"); ?>';
            }
        });
        return;
    <?php endif; ?>

    const quantity = parseInt(document.getElementById('quantity').value);
    const productId = parseInt(new URLSearchParams(window.location.search).get('id'));
    
    // Lấy thông tin sản phẩm từ các element trên trang
    const productName = document.querySelector('.product-title').textContent;
    const productPrice = parseFloat(document.querySelector('.product-price').getAttribute('data-price'));
    const productImage = document.querySelector('.product-image').src;

    // Lấy giỏ hàng từ localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    const existingItem = cart.find(item => item.id === productId);
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: productPrice,
            image_url: productImage,
            quantity: quantity
        });
    }

    // Lưu giỏ hàng vào localStorage
    localStorage.setItem('cart', JSON.stringify(cart));

    // Cập nhật hiển thị giỏ hàng
    updateCartDisplay();

    // Hiển thị thông báo thành công
    Swal.fire({
        icon: 'success',
        title: 'Thành công!',
        text: 'Đã thêm sản phẩm vào giỏ hàng',
        showConfirmButton: false,
        timer: 1500
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const productContent = document.getElementById('productContent');
    
    // Hiển thị nội dung sau khi tải xong
    setTimeout(() => {
        loadingSpinner.style.display = 'none';
        productContent.style.display = 'block';
    }, 3500);
});
</script> 