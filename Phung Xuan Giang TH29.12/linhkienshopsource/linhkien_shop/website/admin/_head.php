<?php
require_once __DIR__ . '/../includes/config.php';
require_admin();
$title = $title ?? 'Quản trị';
?>
<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> - Admin</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<header class="header"><div class="container header-row">
    <a class="logo" href="index.php">🔧 Quản trị TechStore</a>
    <nav><a href="index.php">Tổng quan</a><a href="san-pham.php">Sản phẩm</a><a href="danh-muc.php">Danh mục</a>
        <a href="don-hang.php">Đơn hàng</a><a href="khach-hang.php">Khách hàng</a><a href="danh-gia.php">Đánh giá</a>
        <a href="lien-he.php">Liên hệ</a><a href="../index.php">Xem website</a><a href="logout.php">Đăng xuất</a></nav>
</div></header><main class="container">
<?php if ($f = flash()): ?><div class="alert <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
