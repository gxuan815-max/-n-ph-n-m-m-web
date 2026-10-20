<?php
require 'includes/config.php';
unset($_SESSION['kh']);
flash('Đã đăng xuất.'); redirect('index.php');
