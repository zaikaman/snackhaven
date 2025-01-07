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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <?php
    // Include nội dung trang tương ứng
    switch($page) {
        case 'home':
            include 'pages/home.php';
            break;
        case 'about':
            include 'pages/about.php';
            break;
        case 'menu':
            include 'pages/menu.php';
            break;
        case 'contact':
            include 'pages/contact.php';
            break;
        default:
            include 'pages/404.php';
    }
    ?>

    <?php include 'includes/footer.php'; ?>

    <!-- Bootstrap JS và các dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
    <script src="<?php echo url('assets/js/main.js'); ?>"></script>
</body>
</html> 