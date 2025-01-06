<?php
require_once 'includes/url_config.php';

// Lấy URL path
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/snackhaven/', '', $path);

// Routing
switch($path) {
    case '':
    case 'index':
    case 'home':
        $page = 'home';
        $title = 'Trang chủ - SnackHaven';
        break;
    case 'about':
        $page = 'about';
        $title = 'Giới thiệu - SnackHaven';
        break;
    case 'menu':
        $page = 'menu';
        $title = 'Thực đơn - SnackHaven';
        break;
    case 'contact':
        $page = 'contact';
        $title = 'Liên hệ - SnackHaven';
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        $page = '404';
        $title = '404 - Không tìm thấy trang';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php if ($page === 'about'): ?>
    <style>
        .about-section {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .about-hero {
            text-align: center;
            margin-bottom: 50px;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 20px;
            border-radius: 15px;
        }

        .about-hero h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .story-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin: 40px 0;
            align-items: center;
        }

        .story-section img {
            width: 100%;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .story-content {
            padding: 20px;
        }

        .story-content h2 {
            color: #ff6b6b;
            margin-bottom: 20px;
            font-size: 1.8em;
        }

        .story-content p {
            line-height: 1.8;
            margin-bottom: 15px;
            color: #666;
        }

        .values-section {
            background-color: #f9f9f9;
            padding: 50px 20px;
            margin: 40px 0;
            border-radius: 15px;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        .value-item {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .value-item i {
            font-size: 2.5em;
            color: #ff6b6b;
            margin-bottom: 20px;
        }

        .value-item h3 {
            margin-bottom: 15px;
            color: #333;
        }

        .team-section {
            text-align: center;
            margin: 40px 0;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        .team-member {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 15px;
            object-fit: cover;
        }

        .team-member h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .team-member p {
            color: #666;
            font-style: italic;
        }

        @media (max-width: 768px) {
            .story-section,
            .values-grid,
            .team-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php endif; ?>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <?php if ($page === 'home'): ?>
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

    <?php elseif ($page === 'about'): ?>
    <div class="about-hero">
        <h1>Câu Chuyện Của SnackHaven</h1>
        <p>Nơi Hương Vị Truyền Thống Gặp Gỡ Sự Đổi Mới</p>
    </div>

    <div class="about-section">
        <div class="story-section">
            <div class="story-content">
                <h2>Khởi Nguồn Từ Tình Yêu Ẩm Thực</h2>
                <p>SnackHaven được thành lập vào năm 2020 bởi anh em Minh và Hoàng - hai người con của Sài Gòn với niềm đam mê mãnh liệt với ẩm thực đường phố. Lớn lên trong những con hẻm nhỏ đầy hương vị của thành phố, họ ấp ủ giấc mơ mang những món ăn đường phố yêu thích đến gần hơn với mọi người, nhưng theo một cách hiện đại và tiện lợi hơn.</p>
                <p>Từ một xe đẩy nhỏ bán hamburger tự chế ở góc phố Bùi Viện, SnackHaven đã dần phát triển thành chuỗi cửa hàng thức ăn nhanh được yêu thích, nơi hội tụ những món ăn đường phố độc đáo với phong cách phục vụ chuyên nghiệp.</p>
            </div>
            <img src="https://images.unsplash.com/photo-1512152272829-e3139592d56f" alt="Khởi nguồn SnackHaven">
        </div>

        <div class="story-section">
            <img src="https://images.unsplash.com/photo-1603064752734-4c48eff53d05" alt="Đổi mới ẩm thực">
            <div class="story-content">
                <h2>Sứ Mệnh Của Chúng Tôi</h2>
                <p>Tại SnackHaven, chúng tôi không chỉ đơn thuần bán đồ ăn nhanh - chúng tôi mang đến trải nghiệm ẩm thực độc đáo, kết hợp giữa hương vị truyền thống và sự sáng tạo hiện đại. Mỗi món ăn đều được chế biến từ những nguyên liệu tươi ngon nhất, với công thức độc quyền được phát triển qua nhiều năm nghiên cứu.</p>
                <p>Chúng tôi tin rằng đồ ăn nhanh không nhất thiết phải là đồ ăn kém lành mạnh. Đó là lý do tại sao menu của chúng tôi luôn có những lựa chọn cân bằng dinh dưỡng, phù hợp với lối sống năng động của người trẻ hiện đại.</p>
            </div>
        </div>

        <div class="values-section">
            <h2>Giá Trị Cốt Lõi</h2>
            <div class="values-grid">
                <div class="value-item">
                    <i class="fas fa-heart"></i>
                    <h3>Đam Mê</h3>
                    <p>Chúng tôi làm việc với tất cả niềm đam mê và tình yêu với ẩm thực</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-star"></i>
                    <h3>Chất Lượng</h3>
                    <p>Cam kết mang đến những món ăn chất lượng nhất cho khách hàng</p>
                </div>
                <div class="value-item">
                    <i class="fas fa-handshake"></i>
                    <h3>Trách Nhiệm</h3>
                    <p>Luôn đặt sức khỏe và sự hài lòng của khách hàng lên hàng đầu</p>
                </div>
            </div>
        </div>

        <div class="team-section">
            <h2>Đội Ngũ Sáng Lập</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e" alt="Minh Trần">
                    <h3>Minh Trần</h3>
                    <p>Đồng sáng lập & Bếp trưởng</p>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7" alt="Hoàng Trần">
                    <h3>Hoàng Trần</h3>
                    <p>Đồng sáng lập & Giám đốc điều hành</p>
                </div>
                <div class="team-member">
                    <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80" alt="Mai Phương">
                    <h3>Mai Phương</h3>
                    <p>Giám đốc phát triển sản phẩm</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php include 'includes/footer.php'; ?>

    <script src="<?php echo url('assets/js/main.js'); ?>"></script>
</body>
</html> 