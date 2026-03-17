-- ============================================================
-- JBM BENGKEL MOBIL — Full Database Schema
-- Database: jbm_bengkel
-- Engine: InnoDB | Charset: utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS `jbm_bengkel`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `jbm_bengkel`;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- TABLE: users
-- PK: id_user  (User_model, Auth.php use WHERE id_user = ?)
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
  `id_user`     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(100) NOT NULL,
  `email`       VARCHAR(150) NOT NULL UNIQUE,
  `phone`       VARCHAR(20)  DEFAULT NULL,
  `password`    VARCHAR(255) NOT NULL,
  `role`        ENUM('admin','kasir','mekanik','customer') NOT NULL DEFAULT 'customer',
  `avatar`      VARCHAR(255) DEFAULT NULL,
  `address`     TEXT         DEFAULT NULL,
  `reset_token` VARCHAR(100) DEFAULT NULL,
  `reset_expiry` DATETIME    DEFAULT NULL,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_email`       (`email`),
  INDEX `idx_role`        (`role`),
  INDEX `idx_reset_token` (`reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: vehicles
-- PK: id  (Vehicle_model uses WHERE id = ?)
-- FK: user_id → users.id_user
-- ============================================================
CREATE TABLE IF NOT EXISTS `vehicles` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED NOT NULL,
  `brand`       VARCHAR(50)  NOT NULL,
  `model`       VARCHAR(100) NOT NULL,
  `year`        YEAR         NOT NULL,
  `plate_number` VARCHAR(20) NOT NULL,
  `color`       VARCHAR(50)  DEFAULT NULL,
  `fuel_type`   ENUM('bensin','diesel','hybrid','listrik') DEFAULT 'bensin',
  `is_deleted`  TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_vehicle_user`    (`user_id`),
  INDEX `idx_vehicle_deleted` (`is_deleted`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: services
-- ============================================================
CREATE TABLE IF NOT EXISTS `services` (
  `id_service`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(100) NOT NULL,
  `description`  TEXT         DEFAULT NULL,
  `base_price`   DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `duration_min` INT          NOT NULL DEFAULT 60 COMMENT 'Estimasi durasi dalam menit',
  `icon`         VARCHAR(100) DEFAULT NULL,
  `image`        VARCHAR(255) DEFAULT NULL,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: time_slots
-- PK: id  (Slot_model uses WHERE id = ?)
-- ============================================================
CREATE TABLE IF NOT EXISTS `time_slots` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `label`        VARCHAR(20)  NOT NULL,
  `start_time`   TIME         NOT NULL,
  `end_time`     TIME         NOT NULL,
  `max_bookings` INT          NOT NULL DEFAULT 3,
  `is_active`    TINYINT(1)   NOT NULL DEFAULT 1,
  INDEX `idx_slot_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: bookings
-- PK: id  (Booking_model uses WHERE id = ?)
-- FKs use simple style: user_id, vehicle_id, service_id, slot_id
-- ============================================================
CREATE TABLE IF NOT EXISTS `bookings` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_code` VARCHAR(20)  NOT NULL UNIQUE,
  `user_id`      INT UNSIGNED NOT NULL,
  `vehicle_id`   INT UNSIGNED NOT NULL,
  `service_id`   INT UNSIGNED NOT NULL,
  `slot_id`      INT UNSIGNED NOT NULL,
  `booking_date` DATE         NOT NULL,
  `notes`        TEXT         DEFAULT NULL,
  `complaint`    TEXT         DEFAULT NULL,
  `status`       ENUM('pending','confirmed','in_progress','waiting_payment','completed','cancelled') NOT NULL DEFAULT 'pending',
  `mechanic_id`  INT UNSIGNED DEFAULT NULL,
  `mechanic_notes` TEXT       DEFAULT NULL,
  `cancel_reason` TEXT        DEFAULT NULL,
  `created_at`   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_booking_user`   (`user_id`),
  INDEX `idx_booking_vehicle`(`vehicle_id`),
  INDEX `idx_booking_service`(`service_id`),
  INDEX `idx_booking_slot`   (`slot_id`),
  INDEX `idx_booking_date`   (`booking_date`),
  INDEX `idx_booking_status` (`status`),
  INDEX `idx_booking_code`   (`booking_code`),
  FOREIGN KEY (`user_id`)      REFERENCES `users`(`id_user`)       ON DELETE CASCADE,
  FOREIGN KEY (`vehicle_id`)   REFERENCES `vehicles`(`id`)         ON DELETE CASCADE,
  FOREIGN KEY (`service_id`)   REFERENCES `services`(`id_service`) ON DELETE RESTRICT,
  FOREIGN KEY (`slot_id`)      REFERENCES `time_slots`(`id`)       ON DELETE RESTRICT,
  FOREIGN KEY (`mechanic_id`)  REFERENCES `users`(`id_user`)       ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: spare_parts
-- PK: id  (Spare_part_model uses WHERE id = ?)
-- Prices: purchase_price, selling_price  (as used in Admin.php)
-- Must come before service_orders (which has FK → spare_parts.id)
-- ============================================================
CREATE TABLE IF NOT EXISTS `spare_parts` (
  `id`             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sku`            VARCHAR(50)   NOT NULL UNIQUE,
  `name`           VARCHAR(150)  NOT NULL,
  `brand`          VARCHAR(100)  DEFAULT NULL,
  `category`       VARCHAR(100)  DEFAULT NULL,
  `unit`           VARCHAR(20)   NOT NULL DEFAULT 'pcs',
  `purchase_price` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `selling_price`  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `stock`          INT           NOT NULL DEFAULT 0,
  `min_stock`      INT           NOT NULL DEFAULT 5,
  `description`    TEXT          DEFAULT NULL,
  `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_part_sku`    (`sku`),
  INDEX `idx_part_stock`  (`stock`),
  INDEX `idx_part_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: service_orders
-- Line-item table for parts & labor per booking
-- PK: id  (Admin.php uses WHERE id = ?)
-- ============================================================
CREATE TABLE IF NOT EXISTS `service_orders` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_id`    INT UNSIGNED NOT NULL,
  `spare_part_id` INT UNSIGNED DEFAULT NULL,
  `type`          ENUM('jasa','parts') NOT NULL DEFAULT 'jasa',
  `description`   VARCHAR(255) NOT NULL,
  `quantity`      INT          NOT NULL DEFAULT 1,
  `unit_price`    DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `unit`          VARCHAR(20)  NOT NULL DEFAULT 'pcs',
  `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_order_booking`    (`booking_id`),
  INDEX `idx_order_spare_part` (`spare_part_id`),
  FOREIGN KEY (`booking_id`)    REFERENCES `bookings`(`id`)     ON DELETE CASCADE,
  FOREIGN KEY (`spare_part_id`) REFERENCES `spare_parts`(`id`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: booking_status_logs
-- Booking_model._log() uses: booking_id, status, actor_id, note
-- ============================================================
CREATE TABLE IF NOT EXISTS `booking_status_logs` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `booking_id` INT UNSIGNED NOT NULL,
  `status`     VARCHAR(50)  NOT NULL,
  `actor_id`   INT UNSIGNED DEFAULT NULL,
  `note`       TEXT         DEFAULT NULL,
  `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_log_booking` (`booking_id`),
  INDEX `idx_log_actor`   (`actor_id`),
  FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`)    ON DELETE CASCADE,
  FOREIGN KEY (`actor_id`)   REFERENCES `users`(`id_user`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: payments
-- PK: id  (Payment_model uses WHERE id = ?)
-- FK: booking_id, confirmed_by
-- Status matches Payment_model: unpaid/waiting_confirmation/paid/rejected
-- ============================================================
CREATE TABLE IF NOT EXISTS `payments` (
  `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `invoice_number`    VARCHAR(30)  NOT NULL UNIQUE,
  `booking_id`        INT UNSIGNED NOT NULL,
  `total_amount`      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `method`            ENUM('cash','transfer') DEFAULT NULL,
  `status`            ENUM('unpaid','waiting_confirmation','paid','rejected') NOT NULL DEFAULT 'unpaid',
  `proof_file`        VARCHAR(255) DEFAULT NULL,
  `proof_uploaded_at` DATETIME     DEFAULT NULL,
  `paid_at`           DATETIME     DEFAULT NULL,
  `confirmed_by`      INT UNSIGNED DEFAULT NULL,
  `reject_note`       TEXT         DEFAULT NULL,
  `created_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_payment_booking` (`booking_id`),
  INDEX `idx_payment_status`  (`status`),
  INDEX `idx_payment_invoice` (`invoice_number`),
  FOREIGN KEY (`booking_id`)   REFERENCES `bookings`(`id`)    ON DELETE CASCADE,
  FOREIGN KEY (`confirmed_by`) REFERENCES `users`(`id_user`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: payment_items
-- PK: id, FK: payment_id
-- Payment_model inserts: payment_id, description, type, quantity, unit_price
-- ============================================================
CREATE TABLE IF NOT EXISTS `payment_items` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `payment_id`  INT UNSIGNED NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `type`        VARCHAR(30)  NOT NULL DEFAULT 'jasa' COMMENT 'jasa or parts',
  `quantity`    INT          NOT NULL DEFAULT 1,
  `unit_price`  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  INDEX `idx_item_payment` (`payment_id`),
  FOREIGN KEY (`payment_id`) REFERENCES `payments`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: stock_mutations
-- PK: id, FK: spare_part_id → spare_parts.id
-- Spare_part_model._mutate() uses: spare_part_id, type, quantity,
-- stock_before, stock_after, reason, note, created_by
-- ============================================================
CREATE TABLE IF NOT EXISTS `stock_mutations` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `spare_part_id` INT UNSIGNED NOT NULL,
  `type`          ENUM('in','out','adjustment') NOT NULL,
  `quantity`      INT          NOT NULL,
  `stock_before`  INT          NOT NULL DEFAULT 0,
  `stock_after`   INT          NOT NULL DEFAULT 0,
  `reason`        VARCHAR(100) DEFAULT NULL COMMENT 'purchase, usage, return, adjustment',
  `note`          TEXT         DEFAULT NULL,
  `created_by`    INT UNSIGNED DEFAULT NULL,
  `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_mutation_part`    (`spare_part_id`),
  INDEX `idx_mutation_created` (`created_by`),
  FOREIGN KEY (`spare_part_id`) REFERENCES `spare_parts`(`id`)   ON DELETE CASCADE,
  FOREIGN KEY (`created_by`)    REFERENCES `users`(`id_user`)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: notifications
-- NOTE: Api.php, Admin.php, Customer.php all use `user_id`
-- Notification_lib uses `id_user` — fixed in Notification_lib.php
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `title`      VARCHAR(200) NOT NULL,
  `message`    TEXT         NOT NULL,
  `type`       ENUM('booking','payment','service','stock','system') NOT NULL DEFAULT 'system',
  `ref_id`     INT UNSIGNED DEFAULT NULL,
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `url`        VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_notif_user`    (`user_id`),
  INDEX `idx_notif_is_read` (`is_read`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id_user`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: testimonials
-- ============================================================
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id_testimonial` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_user`        INT UNSIGNED DEFAULT NULL,
  `name`           VARCHAR(100) NOT NULL,
  `avatar`         VARCHAR(255) DEFAULT NULL,
  `rating`         TINYINT(1)   NOT NULL DEFAULT 5,
  `content`        TEXT         NOT NULL,
  `is_approved`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at`     TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_testimonial_approved`(`is_approved`),
  FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: gallery
-- ============================================================
CREATE TABLE IF NOT EXISTS `gallery` (
  `id_gallery`  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(150) NOT NULL,
  `image`       VARCHAR(255) NOT NULL,
  `description` TEXT         DEFAULT NULL,
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_gallery_active`    (`is_active`),
  INDEX `idx_gallery_sort_order`(`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: settings
-- ============================================================
CREATE TABLE IF NOT EXISTS `settings` (
  `id_setting`  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `key`         VARCHAR(100) NOT NULL UNIQUE,
  `value`       TEXT         DEFAULT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  INDEX `idx_setting_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CI3 SESSION TABLE (required for database session driver)
-- ============================================================
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `id`         VARCHAR(128) NOT NULL,
  `ip_address` VARCHAR(45)  NOT NULL,
  `timestamp`  INT(10) UNSIGNED DEFAULT 0 NOT NULL,
  `data`       BLOB         NOT NULL,
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin user (password: admin123)
-- Hash verified with password_verify('admin123', hash) = true
INSERT INTO `users` (`name`, `email`, `phone`, `password`, `role`, `is_active`) VALUES
('Administrator JBM',  'admin@jbmbengkel.com',   '081234567890', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'admin',    1),
('Budi Santoso',       'mekanik1@jbmbengkel.com','082233445566', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'mekanik',  1),
('Andi Prasetyo',      'mekanik2@jbmbengkel.com','083344556677', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'mekanik',  1),
('Siti Rahayu',        'siti@customer.com',       '085678901234', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'customer', 1),
('Dedi Kurniawan',     'dedi@customer.com',       '087890123456', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'customer', 1),
('Rina Wulandari',     'rina@customer.com',       '089012345678', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'customer', 1);

-- Vehicles (seed for customer users id 4,5,6)
INSERT INTO `vehicles` (`user_id`, `brand`, `model`, `year`, `plate_number`, `color`, `fuel_type`) VALUES
(4, 'Toyota',    'Avanza',  2019, 'B 1234 SRA', 'Putih',  'bensin'),
(4, 'Honda',     'Brio',    2021, 'B 5678 SRA', 'Hitam',  'bensin'),
(5, 'Suzuki',    'Ertiga',  2020, 'D 9012 KWN', 'Silver', 'bensin'),
(5, 'Daihatsu',  'Xenia',   2018, 'D 3456 KWN', 'Merah',  'bensin'),
(6, 'Mitsubishi','Pajero',  2022, 'E 7890 RWL', 'Hitam',  'diesel');

-- Services
INSERT INTO `services` (`name`, `description`, `base_price`, `duration_min`, `icon`, `is_active`) VALUES
('Ganti Oli',      'Penggantian oli mesin dengan produk berkualitas. Termasuk filter oli baru.',                75000,  60, 'ki-wrench',         1),
('Tune-Up Mesin',  'Pemeriksaan dan penyetelan mesin secara menyeluruh untuk performa optimal.',               350000, 120,'ki-setting-2',      1),
('Service AC',     'Pembersihan, pengisian freon, dan perbaikan sistem AC mobil.',                              250000, 90, 'ki-abstract-26',    1),
('Spooring',       'Penyetelan geometri roda untuk kenyamanan berkendara dan mencegah ban habis tidak merata.', 150000, 60, 'ki-arrows-circle',  1),
('Balancing',      'Penyeimbangan roda mobil untuk menghilangkan getaran dan meningkatkan kenyamanan.',        100000, 45, 'ki-loading',        1),
('Ganti Ban',      'Penggantian ban baru dengan berbagai pilihan merek dan ukuran.',                            50000,  30, 'ki-tyre',           1);

-- Time Slots (08:00 - 16:00, interval 1 jam)
INSERT INTO `time_slots` (`label`, `start_time`, `end_time`, `max_bookings`, `is_active`) VALUES
('08:00', '08:00:00', '09:00:00', 3, 1),
('09:00', '09:00:00', '10:00:00', 3, 1),
('10:00', '10:00:00', '11:00:00', 3, 1),
('11:00', '11:00:00', '12:00:00', 3, 1),
('13:00', '13:00:00', '14:00:00', 3, 1),
('14:00', '14:00:00', '15:00:00', 3, 1),
('15:00', '15:00:00', '16:00:00', 3, 1),
('16:00', '16:00:00', '17:00:00', 2, 1);

-- Spare Parts
INSERT INTO `spare_parts` (`sku`, `name`, `brand`, `unit`, `purchase_price`, `selling_price`, `stock`, `min_stock`, `is_active`) VALUES
('OLI-CAS-5W30-1L',  'Oli Mesin Castrol 5W-30 1L',       'Castrol',    'liter',  65000,  85000,  50, 10, 1),
('OLI-SHL-10W40-1L', 'Oli Mesin Shell Helix 10W-40 1L',  'Shell',      'liter',  60000,  80000,  40, 10, 1),
('FLT-OLI-TMOT',     'Filter Oli Toyota Avanza',          'Toyota',     'pcs',    35000,  55000,  30, 5,  1),
('FLT-OLI-HMOT',     'Filter Oli Honda Brio',             'Honda',      'pcs',    38000,  58000,  25, 5,  1),
('BUS-NGK-BP6ES',    'Busi NGK BP6ES',                    'NGK',        'pcs',    25000,  40000,  80, 20, 1),
('BUS-NGK-ILKAR8B7', 'Busi NGK Iridium ILKAR8B7',         'NGK',        'pcs',    85000,  130000, 30, 10, 1),
('BRK-FLT-UNIV',     'Filter Bahan Bakar Universal',      'Sakura',     'pcs',    45000,  70000,  20, 5,  1),
('AC-FREON-R134A',   'Freon AC R134A 250gr',              'Daikin',     'kaleng', 75000,  120000, 15, 5,  1),
('BAN-BRGT-185/65',  'Ban Bridgestone Ecopia 185/65 R15', 'Bridgestone','pcs',    650000, 850000, 12, 3,  1),
('OLI-TRS-ATF',      'Oli Transmisi ATF Dexron III 1L',   'Castrol',    'liter',  55000,  80000,  25, 8,  1);

-- Testimonials (approved)
INSERT INTO `testimonials` (`id_user`, `name`, `rating`, `content`, `is_approved`) VALUES
(4, 'Siti Rahayu',    5, 'Pelayanan sangat memuaskan! Mekaniknya profesional dan ramah. Mobil saya jadi lebih nyaman setelah service di JBM. Pasti akan kembali lagi!',              1),
(5, 'Dedi Kurniawan', 5, 'Ganti oli di JBM cepat dan hasilnya bagus. Harga juga transparan, tidak ada biaya tersembunyi. Sangat direkomendasikan!',                                1),
(6, 'Rina Wulandari', 4, 'Servis AC mobil saya tuntas dalam 1.5 jam. Teknisi sangat berpengalaman dan menjelaskan masalah dengan detail. Terima kasih JBM!',                       1),
(NULL, 'Bapak Hartono', 5, 'Sudah 3 tahun jadi pelanggan JBM Bengkel. Tidak pernah mengecewakan. Booking online sangat memudahkan, tidak perlu antri lama.',                      1),
(NULL, 'Ibu Dewi',     4, 'Pertama kali service mobil di sini, langsung cocok. Tempatnya bersih, tunggu nyaman, dan pengerjaannya cepat. Dijamin puas!',                          1);

-- Gallery
INSERT INTO `gallery` (`title`, `image`, `description`, `sort_order`, `is_active`) VALUES
('Area Bengkel Utama',     'gallery/bengkel-utama.jpg',   'Area utama pengerjaan kendaraan dengan peralatan modern',          1, 1),
('Ruang Tunggu Nyaman',    'gallery/ruang-tunggu.jpg',    'Ruang tunggu ber-AC dengan WiFi gratis dan refreshment',           2, 1),
('Tim Mekanik Profesional','gallery/tim-mekanik.jpg',     'Tim mekanik bersertifikat dan berpengalaman lebih dari 10 tahun',  3, 1),
('Peralatan Diagnosa',     'gallery/peralatan.jpg',       'Dilengkapi alat diagnosa komputer terkini untuk akurasi servicing', 4, 1),
('Service AC Mobil',       'gallery/service-ac.jpg',      'Proses pengisian freon dan perbaikan kompressor AC',               5, 1),
('Spooring & Balancing',   'gallery/spooring.jpg',        'Mesin spooring digital untuk hasil presisi',                       6, 1);

-- Settings
INSERT INTO `settings` (`key`, `value`, `description`) VALUES
('workshop_name',    'JBM Bengkel Mobil',                             'Nama Bengkel'),
('workshop_tagline', 'Servis Terpercaya, Kualitas Terjamin',          'Tagline Bengkel'),
('workshop_phone',   '0812-3456-7890',                                'Nomor Telepon Bengkel'),
('workshop_wa',      '6281234567890',                                  'Nomor WhatsApp (format internasional tanpa +)'),
('workshop_email',   'info@jbmbengkel.com',                           'Email Bengkel'),
('workshop_address', 'Jl. Raya Serpong No. 88, Tangerang Selatan',    'Alamat Lengkap'),
('workshop_city',    'Tangerang Selatan',                             'Kota'),
('workshop_lat',     '-6.2946',                                       'Latitude Google Maps'),
('workshop_lng',     '106.6674',                                      'Longitude Google Maps'),
('workshop_maps_embed', 'https://maps.google.com/maps?q=-6.2946,106.6674&output=embed', 'Embed Google Maps URL'),
('bank_name',        'BCA',                                           'Nama Bank Transfer'),
('bank_account',     '1234567890',                                    'Nomor Rekening'),
('bank_holder',      'JBM Bengkel Mobil',                             'Nama Pemilik Rekening'),
('operating_hours',  'Senin - Sabtu: 08.00 - 17.00 WIB',             'Jam Operasional'),
('max_booking_days', '14',                                            'Maksimal hari booking ke depan'),
('invoice_prefix',   'INV',                                           'Prefix nomor invoice'),
('booking_prefix',   'JBM',                                           'Prefix kode booking');
