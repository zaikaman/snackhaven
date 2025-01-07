<?php
require_once __DIR__ . '/../includes/config.php';
?>

<div class="hero-section">
    <h1>Thưởng thức ẩm thực tuyệt vời tại SnackHaven</h1>
    <a href="<?php echo url('menu'); ?>" class="order-now">Đặt hàng ngay</a>
</div>

<section class="featured-categories">
    <h2>Danh Mục Nổi Bật</h2>
    <div class="category-grid">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY id");
            while($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="category-item">';
                echo '<img src="' . htmlspecialchars($category['image_url']) . '" alt="' . htmlspecialchars($category['name']) . '">';
                echo '<h3>' . htmlspecialchars($category['name']) . '</h3>';
                echo '</div>';
            }
        } catch(PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>
</section>

<section class="promotion">
    <div class="promo-content">
        <h2>Giảm 50% Cho Tất Cả Các Combo Trong Tuần Này!</h2>
        <a href="<?php echo url('menu'); ?>" class="view-deals" style="text-decoration: none;">Xem Thực Đơn</a>
    </div>
</section>

<section class="testimonials">
    <h2>Đánh Giá Từ Khách Hàng</h2>
    <div class="testimonial-grid">
        <div class="testimonial-item">
            <img src="https://images.unsplash.com/photo-1573007974656-b958089e9f7b" alt="Khách hàng">
            <p>"Trải nghiệm ẩm thực tuyệt vời nhất! Những chiếc burger thật tuyệt!"</p>
            <div class="rating">★★★★★</div>
        </div>
        <div class="testimonial-item">
            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a" alt="Khách hàng">
            <p>"Pizza ở đây thật sự rất ngon! Phô mai béo ngậy và hương vị tuyệt hảo!"</p>
            <div class="rating">★★★★★</div>
        </div>
        <div class="testimonial-item">
            <img src="https://images.unsplash.com/photo-1545167622-3a6ac756afa4" alt="Khách hàng">
            <p>"Đồ uống đa dạng, là sự kết hợp hoàn hảo với các món ăn!"</p>
            <div class="rating">★★★★★</div>
        </div>
    </div>
</section> 