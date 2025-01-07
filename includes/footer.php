<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h3>Về SnackHaven</h3>
            <p>SnackHaven tự hào là điểm đến ẩm thực độc đáo tại TP. Hồ Chí Minh, mang đến trải nghiệm ẩm thực tuyệt vời với đa dạng món ăn và đồ uống chất lượng.</p>
        </div>
        
        <div class="footer-section">
            <h3>Thông Tin Liên Hệ</h3>
            <p><i class="fas fa-map-marker-alt"></i> 123 Đường Lê Lợi, Quận 1, TP. Hồ Chí Minh</p>
            <p><i class="fas fa-phone-alt"></i> Hotline: (028) 3823 xxxx</p>
            <p><i class="fas fa-mobile-alt"></i> Di động: 090 xxx xxxx</p>
            <p><i class="fas fa-envelope"></i> Email: info@snackhaven.com</p>
            <p><i class="fas fa-clock"></i> Giờ mở cửa: 10:00 - 22:00 (Cả tuần)</p>
        </div>

        <div class="footer-section">
            <h3>Kết Nối Với Chúng Tôi</h3>
            <div class="social-icons">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-links">
            <a href="<?php echo url('about'); ?>">Về Chúng Tôi</a>
            <a href="<?php echo url('menu'); ?>">Thực Đơn</a>
            <a href="<?php echo url('contact'); ?>">Liên Hệ</a>
            <a href="#">Chính Sách Bảo Mật</a>
            <a href="#">Điều Khoản Sử Dụng</a>
        </div>
        <p>&copy; <?php echo date('Y'); ?> SnackHaven. Tất cả quyền được bảo lưu.</p>
    </div>
</footer>

<style>
/* Cập nhật style cho footer */
footer {
    font-family: "Times New Roman", Times, serif;
    background-color: #333;
    color: #fff;
    padding: 4rem 1rem 1rem;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.footer-section h3 {
    font-family: "Times New Roman", Times, serif;
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
    color: #ff6b6b;
}

.footer-section p {
    margin-bottom: 1rem;
    line-height: 1.6;
}

.footer-section i {
    margin-right: 10px;
    color: #ff6b6b;
}

.social-icons {
    display: flex;
    gap: 1.5rem;
}

.social-icons a {
    color: #fff;
    font-size: 1.8rem;
    transition: all 0.3s ease;
}

.social-icons a:hover {
    color: #ff6b6b;
    transform: translateY(-3px);
}

.footer-bottom {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #444;
    text-align: center;
}

.footer-links {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.footer-links a {
    color: #fff;
    text-decoration: none;
    transition: color 0.3s;
}

.footer-links a:hover {
    color: #ff6b6b;
}

@media (max-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr;
    }
    
    .footer-links {
        gap: 1rem;
    }
}
</style> 