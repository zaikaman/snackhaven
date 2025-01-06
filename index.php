<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnackHaven - Thiên đường đồ ăn nhanh</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="hero-section">
        <h1>SnackHaven - Thiên đường ẩm thực tươi ngon, nhanh chóng và đậm đà hương vị</h1>
        <button class="order-now">Đặt Hàng Ngay</button>
    </div>

    <section class="featured-categories">
        <h2>Danh Mục Nổi Bật</h2>
        <div class="category-grid">
            <?php
            require_once 'includes/config.php';
            
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
            <button class="view-deals">Xem Ưu Đãi</button>
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

    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html> 