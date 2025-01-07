# SnackHaven - Website Bán Đồ Ăn Vặt

SnackHaven là một website bán đồ ăn vặt được xây dựng bằng PHP, cho phép người dùng dễ dàng duyệt và đặt hàng các món ăn vặt yêu thích.

## Tính năng chính

- Đăng ký và đăng nhập tài khoản
- Xem danh sách sản phẩm
- Chi tiết sản phẩm
- Giỏ hàng và thanh toán
- Quản lý đơn hàng
- Trang quản trị (Admin Panel)
- Hệ thống liên hệ và phản hồi

## Yêu cầu hệ thống

- XAMPP (với PHP 7.4 trở lên)
- MySQL
- Web Browser hiện đại (Chrome, Firefox, Edge,...)

## Hướng dẫn cài đặt

1. Cài đặt XAMPP từ [trang chủ XAMPP](https://www.apachefriends.org/)

2. Clone repository này vào thư mục `htdocs` của XAMPP:
   ```
   C:\xampp\htdocs\snackhaven
   ```

3. Tạo file `.env` trong thư mục gốc của dự án với nội dung sau:
   ```
   DB_URL=your_database_url

   # SMTP Configuration
   SMTP_HOST=smtp.gmail.com
   SMTP_USERNAME=your_email
   SMTP_PASSWORD=your_app_password
   SMTP_PORT=587

   # Environment Configuration
   ENVIRONMENT=local

   IMGBB_API_KEY=your_imgbb_api_key
   ```

4. Khởi động các dịch vụ Apache và MySQL trong XAMPP Control Panel

5. Truy cập website qua địa chỉ:
   ```
   http://localhost/snackhaven
   ```

## Cấu hình môi trường

Để website hoạt động chính xác, bạn cần cấu hình các thông số sau trong file `.env`:

- `DB_URL`: URL kết nối đến database MySQL
- `SMTP_USERNAME`: Email dùng để gửi mail
- `SMTP_PASSWORD`: Mật khẩu ứng dụng của email
- `IMGBB_API_KEY`: API key của ImgBB để upload ảnh

## Cấu trúc thư mục

```
snackhaven/
├── admincp/           # Trang quản trị
├── assets/           # CSS, JavaScript, Images
├── auth/             # Xử lý đăng nhập/đăng ký
├── includes/         # Các file PHP được include
├── pages/            # Các trang của website
└── index.php         # Trang chủ
```

## Lưu ý bảo mật

- Không được commit file `.env` lên repository
- Bảo vệ thư mục `admincp/` khỏi truy cập trái phép
- Thường xuyên cập nhật các thành phần để đảm bảo an toàn

## Hỗ trợ

Nếu bạn gặp vấn đề trong quá trình cài đặt hoặc sử dụng, vui lòng tạo issue trong repository này. 