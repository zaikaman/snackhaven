<style>
    .contact-section {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 20px;
        font-family: "Times New Roman", Times, serif;
        margin-top: 100px;
        padding: 50px 0;
    }

    .contact-hero {
        text-align: center;
        margin-bottom: 50px;
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1577563908411-5077b6dc7624');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 20px;
        border-radius: 15px;
    }

    .contact-hero h1 {
        font-size: 2.5em;
        margin-bottom: 20px;
    }

    .contact-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        margin-top: 50px;
    }

    .contact-info {
        padding: 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .contact-info h2 {
        font-family: "Times New Roman", Times, serif;
        color: #333;
        margin-bottom: 30px;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 30px;
    }

    .info-item i {
        font-size: 1.5em;
        color: #ff6b6b;
        margin-right: 15px;
        margin-top: 5px;
    }

    .info-details h3 {
        margin-bottom: 10px;
        color: #333;
    }

    .info-details p {
        font-family: "Times New Roman", Times, serif;
        color: #666;
        line-height: 1.6;
    }

    .contact-form {
        padding: 30px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .contact-form h2 {
        font-family: "Times New Roman", Times, serif;
        color: #333;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 500;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1em;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #ff6b6b;
        outline: none;
    }

    .form-group textarea {
        height: 150px;
        resize: vertical;
    }

    .btn-submit {
        background: #ff6b6b;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        font-size: 1em;
        cursor: pointer;
        transition: background 0.3s;
        font-family: "Times New Roman", Times, serif;
    }

    .btn-submit:hover {
        background: #ff5252;
    }

    .social-links {
        margin-top: 30px;
    }

    .social-links h3 {
        margin-bottom: 15px;
        color: #333;
    }

    .social-icons {
        display: flex;
        gap: 15px;
    }

    .social-icons a {
        color: #ff6b6b;
        font-size: 1.5em;
        transition: color 0.3s;
    }

    .social-icons a:hover {
        color: #ff5252;
    }

    .map-section {
        margin-top: 50px;
        margin-bottom: 50px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .map-section iframe {
        width: 100%;
        height: 400px;
        border: none;
    }

    @media (max-width: 768px) {
        .contact-content {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="contact-hero">
    <h1>Liên Hệ Với Chúng Tôi</h1>
    <p>Chúng tôi luôn sẵn sàng lắng nghe ý kiến của bạn</p>
</div>

<div class="contact-section">
    <div class="contact-content">
        <div class="contact-info">
            <h2>Thông Tin Liên Hệ</h2>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <div class="info-details">
                    <h3>Địa Chỉ</h3>
                    <p>123 Đường Lê Lợi<br>Quận 1, TP. Hồ Chí Minh</p>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-phone-alt"></i>
                <div class="info-details">
                    <h3>Điện Thoại</h3>
                    <p>Hotline: (028) 3823 xxxx<br>Di động: 090 xxx xxxx</p>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <div class="info-details">
                    <h3>Email</h3>
                    <p>info@snackhaven.com<br>support@snackhaven.com</p>
                </div>
            </div>
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <div class="info-details">
                    <h3>Giờ Mở Cửa</h3>
                    <p>Thứ 2 - Chủ nhật: 10:00 - 22:00<br>Phục vụ cả ngày lễ</p>
                </div>
            </div>

            <div class="social-links">
                <h3>Kết Nối Với Chúng Tôi</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>

        <div class="contact-form">
            <h2>Gửi Tin Nhắn</h2>
            <form id="contactForm" action="process_contact.php" method="POST">
                <div class="form-group">
                    <label for="name">Họ và tên</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="subject">Chủ đề</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Nội dung tin nhắn</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Gửi Tin Nhắn
                </button>
            </form>
        </div>
    </div>

    <div class="map-section">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.3253162668427!2d106.69233067465353!3d10.786840989318513!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f3a9d8d1bb3%3A0xd2ecb62e0d050fe9!2zMTIzIMSQxrDhu51uZyBMw6ogTOG7o2ksIELhur9uIE5naMOpLCBRdeG6rW4gMSwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5oLCBWaeG7h3QgTmFt!5e0!3m2!1svi!2s!4v1704596447749!5m2!1svi!2s" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div> 