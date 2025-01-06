<?php
// Đọc file .env nếu tồn tại
function loadEnv($path = '.env') {
    if(file_exists($path)) {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Xóa dấu ngoặc kép nếu có
                if (strpos($value, '"') === 0) {
                    $value = trim($value, '"');
                }
                
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }
}

// Load biến môi trường từ file .env nếu có
loadEnv();

// Lấy URL database từ biến môi trường
$db_url = getenv('DB_URL');
if (!$db_url) {
    die('DB_URL environment variable is not set');
}

$db = parse_url($db_url);

try {
    // Tạo DSN cho MySQL với SSL
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s",
        $db['host'],
        $db['port'],
        ltrim($db['path'], '/')
    );

    // Thêm options cho SSL với file ca.pem
    $options = array(
        PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../ca.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    );

    // Kết nối database
    $pdo = new PDO($dsn, $db['user'], $db['pass'], $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET NAMES utf8mb4");
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?> 