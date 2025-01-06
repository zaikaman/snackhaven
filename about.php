<?php
require_once 'includes/url_config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu - SnackHaven</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
</head>
<body>
    <?php include 'includes/header.php'; ?>

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

    <?php include 'includes/footer.php'; ?>
</body>
</html> 