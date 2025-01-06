<?php
// Đọc file .env nếu tồn tại
function loadEnv() {
    // Lấy đường dẫn tuyệt đối đến thư mục gốc của project
    $rootPath = dirname(__DIR__);
    $envPath = $rootPath . '/.env';

    if(file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Bỏ qua comment
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }
}

// Load biến môi trường từ file .env
loadEnv();

// Lấy URL database từ biến môi trường
$db_url = getenv('DB_URL');
if (!$db_url) {
    die('DB_URL environment variable is not set');
}

try {
    // Parse URL database
    $db = parse_url($db_url);
    
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
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    );

    // Kết nối database
    $pdo = new PDO($dsn, $db['user'], $db['pass'], $options);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?> 