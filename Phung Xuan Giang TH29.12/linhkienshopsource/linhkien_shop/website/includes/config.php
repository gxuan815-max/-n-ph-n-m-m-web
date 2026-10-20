<?php
// Cấu hình kết nối MySQL - sửa lại cho đúng máy của bạn (XAMPP mặc định: root, mật khẩu rỗng)
define('DB_HOST', 'localhost');
define('DB_NAME', 'linhkien_shop');
define('DB_USER', 'root');
define('DB_PASS', '');

session_start();

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    die('Không kết nối được CSDL: ' . htmlspecialchars($e->getMessage()));
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function vnd($n) { return number_format((float)$n, 0, ',', '.') . ' ₫'; }
function redirect($url) { header('Location: ' . $url); exit; }
function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}
function cart_count() { return array_sum($_SESSION['cart'] ?? []); }
function require_admin() { if (empty($_SESSION['admin'])) redirect('login.php'); }

$TRANG_THAI = [
    'cho_xac_nhan' => 'Chờ xác nhận', 'da_xac_nhan' => 'Đã xác nhận',
    'dang_giao' => 'Đang giao', 'da_giao' => 'Đã giao', 'da_huy' => 'Đã hủy',
];
