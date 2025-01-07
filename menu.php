<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Thực Đơn</h1>
    
    <!-- Danh mục -->
    <div class="categories mb-4">
        <ul class="nav nav-pills justify-content-center" id="menuTab" role="tablist">
            <?php
            $sql = "SELECT * FROM categories ORDER BY id";
            $result = $conn->query($sql);
            $first = true;
            while ($category = $result->fetch_assoc()) {
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
            ?>
        </ul>
    </div>

    <!-- Khu vực hiển thị sản phẩm -->
    <div class="tab-content" id="menuTabContent">
        <?php
        $result->data_seek(0);
        $first = true;
        while ($category = $result->fetch_assoc()) {
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
        ?>
    </div>
</div>

<!-- Template cho sản phẩm -->
<template id="product-template">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <img src="" class="card-img-top product-image" alt="">
            <div class="card-body">
                <h5 class="card-title product-name"></h5>
                <p class="card-text product-description"></p>
                <p class="card-text product-price"></p>
                <button class="btn btn-primary add-to-cart">Thêm vào giỏ hàng</button>
            </div>
        </div>
    </div>
</template>

<script>
const PRODUCTS_PER_PAGE = 9;

function loadProducts(categoryId, page) {
    fetch(`api/get_products.php?category_id=${categoryId}&page=${page}&per_page=${PRODUCTS_PER_PAGE}`)
        .then(response => response.json())
        .then(data => {
            displayProducts(categoryId, data.products);
            displayPagination(categoryId, data.total_pages, page);
        })
        .catch(error => console.error('Error:', error));
}

function displayProducts(categoryId, products) {
    const container = document.getElementById(`products-${categoryId}`);
    container.innerHTML = '';
    const template = document.getElementById('product-template');

    products.forEach(product => {
        const clone = template.content.cloneNode(true);
        
        clone.querySelector('.product-image').src = product.image_url;
        clone.querySelector('.product-image').alt = product.name;
        clone.querySelector('.product-name').textContent = product.name;
        clone.querySelector('.product-description').textContent = product.description;
        clone.querySelector('.product-price').textContent = new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(product.price);

        clone.querySelector('.add-to-cart').onclick = () => addToCart(product.id);
        container.appendChild(clone);
    });
}

function displayPagination(categoryId, totalPages, currentPage) {
    const container = document.getElementById(`pagination-${categoryId}`);
    container.innerHTML = '';
    
    const ul = document.createElement('ul');
    ul.className = 'pagination justify-content-center';

    // Nút Previous
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<button class="page-link" onclick="loadProducts(${categoryId}, ${currentPage - 1})">Trước</button>`;
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
    nextLi.innerHTML = `<button class="page-link" onclick="loadProducts(${categoryId}, ${currentPage + 1})">Sau</button>`;
    ul.appendChild(nextLi);

    container.appendChild(ul);
}

function addToCart(productId) {
    // TODO: Implement add to cart functionality
    console.log('Add to cart:', productId);
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

<?php require_once 'includes/footer.php'; ?> 