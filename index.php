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
    case 'product':
        $page = 'product';
        $title = 'Chi tiết sản phẩm - SnackHaven';
        break;
    case 'profile':
        // Kiểm tra đăng nhập trước khi cho phép truy cập trang profile
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . url('auth/login.php'));
            exit;
        }
        $page = 'profile';
        $title = 'Thông tin cá nhân - SnackHaven';
        break;
    default:
        header("HTTP/1.0 404 Not Found");
        $page = '404';
        $title = '404 - Không tìm thấy trang';
}

// Bắt đầu output buffering để tránh lỗi header
ob_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <?php
        // Include nội dung trang tương ứng
        $page_path = 'pages/' . $page . '.php';
        if (file_exists($page_path)) {
            include $page_path;
        } else {
            include 'pages/404.php';
        }
        ?>
    </main>

    <?php include 'includes/footer.php'; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo url('assets/js/main.js'); ?>"></script>
</body>
</html>
<?php
ob_end_flush();
?> 