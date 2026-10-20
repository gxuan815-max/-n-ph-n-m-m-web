SET NAMES utf8mb4;
-- =====================================================
-- CSDL: WEBSITE BÁN LINH KIỆN MÁY TÍNH
-- Hệ quản trị: MySQL 5.7+ / MariaDB
-- =====================================================
DROP DATABASE IF EXISTS linhkien_shop;
CREATE DATABASE linhkien_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE linhkien_shop;

-- 1. Danh mục sản phẩm
CREATE TABLE danh_muc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_danh_muc VARCHAR(100) NOT NULL,
    mo_ta VARCHAR(255) NULL
) ENGINE=InnoDB;

-- 2. Nhà sản xuất (hãng)
CREATE TABLE nha_san_xuat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_hang VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 3. Sản phẩm
CREATE TABLE san_pham (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_sp VARCHAR(200) NOT NULL,
    danh_muc_id INT NOT NULL,
    hang_id INT NOT NULL,
    gia BIGINT NOT NULL CHECK (gia >= 0),
    so_luong_ton INT NOT NULL DEFAULT 0,
    mo_ta TEXT NULL,
    bieu_tuong VARCHAR(10) DEFAULT '📦',
    hinh_anh VARCHAR(255) NULL,
    ngay_tao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (danh_muc_id) REFERENCES danh_muc(id),
    FOREIGN KEY (hang_id) REFERENCES nha_san_xuat(id),
    INDEX idx_gia (gia),
    INDEX idx_danhmuc (danh_muc_id)
) ENGINE=InnoDB;

-- 4. Khách hàng
CREATE TABLE khach_hang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    so_dien_thoai VARCHAR(15) NULL,
    dia_chi VARCHAR(255) NULL,
    ngay_dang_ky DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 5. Tài khoản quản trị
CREATE TABLE quan_tri (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_dang_nhap VARCHAR(50) NOT NULL UNIQUE,
    mat_khau VARCHAR(255) NOT NULL,
    ho_ten VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 6. Đơn hàng
CREATE TABLE don_hang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    khach_hang_id INT NULL,
    ho_ten_nhan VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    so_dien_thoai VARCHAR(15) NOT NULL,
    dia_chi_giao VARCHAR(255) NOT NULL,
    ghi_chu VARCHAR(255) NULL,
    tong_tien BIGINT NOT NULL DEFAULT 0,
    trang_thai ENUM('cho_xac_nhan','da_xac_nhan','dang_giao','da_giao','da_huy') DEFAULT 'cho_xac_nhan',
    ngay_dat DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (khach_hang_id) REFERENCES khach_hang(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. Chi tiết đơn hàng
CREATE TABLE chi_tiet_don_hang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_hang_id INT NOT NULL,
    san_pham_id INT NOT NULL,
    so_luong INT NOT NULL,
    don_gia BIGINT NOT NULL,
    FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id)
) ENGINE=InnoDB;

-- 8. Đánh giá sản phẩm
CREATE TABLE danh_gia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    san_pham_id INT NOT NULL,
    ten_nguoi_danh_gia VARCHAR(100) NOT NULL,
    so_sao TINYINT NOT NULL CHECK (so_sao BETWEEN 1 AND 5),
    binh_luan TEXT NOT NULL,
    ngay_danh_gia DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Liên hệ
CREATE TABLE lien_he (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    tieu_de VARCHAR(150) NULL,
    noi_dung TEXT NOT NULL,
    ngay_gui DATETIME DEFAULT CURRENT_TIMESTAMP,
    da_xu_ly TINYINT(1) DEFAULT 0
) ENGINE=InnoDB;

-- Tài khoản admin mặc định: admin / admin123
INSERT INTO quan_tri (ten_dang_nhap, mat_khau, ho_ten)
VALUES ('admin', '$2y$10$gdqPNC3Af7Ms1VUo6RDcKezlq2dP71hthRrWcA9zimBoyu3kGUxea', 'Quản trị viên');
