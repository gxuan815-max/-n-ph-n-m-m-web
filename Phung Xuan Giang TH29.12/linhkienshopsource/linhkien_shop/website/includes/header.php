<?php require_once __DIR__ . '/config.php'; $title = $title ?? 'TechStore'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title) ?> - TechStore</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="header"><div class="container header-row">
    <a class="logo" href="index.php">⚙️ TechStore</a>
    <nav>
        <a href="index.php">Trang chủ</a>
        <a href="lien-he.php">Liên hệ</a>
        <?php if (!empty($_SESSION['kh'])): ?>
            <a href="don-cua-toi.php">Đơn của tôi</a>
            <a href="dang-xuat.php">Đăng xuất (<?= e($_SESSION['kh']['ho_ten']) ?>)</a>
        <?php else: ?>
            <a href="dang-nhap.php">Đăng nhập</a><a href="dang-ky.php">Đăng ký</a>
        <?php endif; ?>
        <a class="cart" href="gio-hang.php">🛒 Giỏ hàng <span class="badge"><?= cart_count() ?></span></a>
        <a href="admin/index.php">🔧 Admin</a>
    </nav>
</div></header>
<main class="container">
<?php if ($f = flash()): ?><div class="alert <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
