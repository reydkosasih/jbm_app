-- ============================================================
-- JBM BENGKEL MOBIL - Seed Data
-- Run this after importing database/jbm_schema.sql
-- ============================================================

USE `jbm_bengkel`;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM `payment_items`;
DELETE FROM `payments`;
DELETE FROM `service_orders`;
DELETE FROM `booking_status_logs`;
DELETE FROM `notifications`;
DELETE FROM `stock_mutations`;
DELETE FROM `bookings`;
DELETE FROM `vehicles`;
DELETE FROM `testimonials`;
DELETE FROM `gallery`;
DELETE FROM `settings`;
DELETE FROM `spare_parts`;
DELETE FROM `time_slots`;
DELETE FROM `services`;
DELETE FROM `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- MASTER DATA
-- ============================================================

INSERT INTO `users` (`id_user`, `name`, `email`, `phone`, `password`, `role`, `avatar`, `address`, `reset_token`, `reset_expiry`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administrator JBM', 'admin@jbmbengkel.com', '081234567890', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'admin', NULL, NULL, NULL, NULL, 1, '2026-04-01 08:00:00', '2026-04-01 08:00:00'),
(2, 'Budi Santoso', 'mekanik1@jbmbengkel.com', '082233445566', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'mekanik', NULL, NULL, NULL, NULL, 1, '2026-04-01 08:00:00', '2026-04-01 08:00:00'),
(3, 'Andi Prasetyo', 'mekanik2@jbmbengkel.com', '083344556677', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'mekanik', NULL, NULL, NULL, NULL, 1, '2026-04-01 08:00:00', '2026-04-01 08:00:00'),
(4, 'Siti Rahayu', 'siti@customer.com', '085678901234', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'customer', NULL, NULL, NULL, NULL, 1, '2026-04-01 08:00:00', '2026-04-01 08:00:00'),
(5, 'Dedi Kurniawan', 'dedi@customer.com', '087890123456', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'customer', NULL, NULL, NULL, NULL, 1, '2026-04-01 08:00:00', '2026-04-01 08:00:00'),
(6, 'Rina Wulandari', 'rina@customer.com', '089012345678', '$2y$12$5JRMAHqluhIhAxm0OMfMn.CD2ZVAmPDzH/FbEnSqTOcUEnqwAOWiC', 'customer', NULL, NULL, NULL, NULL, 1, '2026-04-01 08:00:00', '2026-04-01 08:00:00');

INSERT INTO `vehicles` (`id`, `user_id`, `brand`, `model`, `year`, `plate_number`, `color`, `fuel_type`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 4, 'Toyota', 'Avanza', 2019, 'B 1234 SRA', 'Putih', 'bensin', 0, '2026-04-01 08:10:00', '2026-04-01 08:10:00'),
(2, 4, 'Honda', 'Brio', 2021, 'B 5678 SRA', 'Hitam', 'bensin', 0, '2026-04-01 08:10:00', '2026-04-01 08:10:00'),
(3, 5, 'Suzuki', 'Ertiga', 2020, 'D 9012 KWN', 'Silver', 'bensin', 0, '2026-04-01 08:12:00', '2026-04-01 08:12:00'),
(4, 5, 'Daihatsu', 'Xenia', 2018, 'D 3456 KWN', 'Merah', 'bensin', 0, '2026-04-01 08:12:00', '2026-04-01 08:12:00'),
(5, 6, 'Mitsubishi', 'Pajero', 2022, 'E 7890 RWL', 'Hitam', 'diesel', 0, '2026-04-01 08:14:00', '2026-04-01 08:14:00');

INSERT INTO `services` (`id_service`, `name`, `description`, `base_price`, `duration_min`, `icon`, `image`, `is_active`) VALUES
(1, 'Ganti Oli', 'Penggantian oli mesin dengan produk berkualitas. Termasuk filter oli baru.', 75000.00, 60, 'ki-wrench', NULL, 1),
(2, 'Tune-Up Mesin', 'Pemeriksaan dan penyetelan mesin secara menyeluruh untuk performa optimal.', 350000.00, 120, 'ki-setting-2', NULL, 1),
(3, 'Service AC', 'Pembersihan, pengisian freon, dan perbaikan sistem AC mobil.', 250000.00, 90, 'ki-abstract-26', NULL, 1),
(4, 'Spooring', 'Penyetelan geometri roda untuk kenyamanan berkendara dan mencegah ban habis tidak merata.', 150000.00, 60, 'ki-arrows-circle', NULL, 1),
(5, 'Balancing', 'Penyeimbangan roda mobil untuk menghilangkan getaran dan meningkatkan kenyamanan.', 100000.00, 45, 'ki-loading', NULL, 1),
(6, 'Ganti Ban', 'Penggantian ban baru dengan berbagai pilihan merek dan ukuran.', 50000.00, 30, 'ki-tyre', NULL, 1);

INSERT INTO `time_slots` (`id`, `label`, `start_time`, `end_time`, `max_bookings`, `is_active`) VALUES
(1, '08:00', '08:00:00', '09:00:00', 3, 1),
(2, '09:00', '09:00:00', '10:00:00', 3, 1),
(3, '10:00', '10:00:00', '11:00:00', 3, 1),
(4, '11:00', '11:00:00', '12:00:00', 3, 1),
(5, '13:00', '13:00:00', '14:00:00', 3, 1),
(6, '14:00', '14:00:00', '15:00:00', 3, 1),
(7, '15:00', '15:00:00', '16:00:00', 3, 1),
(8, '16:00', '16:00:00', '17:00:00', 2, 1);

INSERT INTO `spare_parts` (`id`, `sku`, `name`, `brand`, `category`, `unit`, `purchase_price`, `selling_price`, `stock`, `min_stock`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'OLI-CAS-5W30-1L', 'Oli Mesin Castrol 5W-30 1L', 'Castrol', 'Oli', 'liter', 65000.00, 85000.00, 49, 10, 'Oli sintetis untuk mesin bensin modern.', 1, '2026-04-01 08:20:00', '2026-04-01 08:20:00'),
(2, 'OLI-SHL-10W40-1L', 'Oli Mesin Shell Helix 10W-40 1L', 'Shell', 'Oli', 'liter', 60000.00, 80000.00, 38, 10, 'Oli untuk perawatan mesin harian.', 1, '2026-04-01 08:20:00', '2026-04-01 08:20:00'),
(3, 'FLT-OLI-TMOT', 'Filter Oli Toyota Avanza', 'Toyota', 'Filter', 'pcs', 35000.00, 55000.00, 28, 5, 'Filter oli untuk Toyota Avanza.', 1, '2026-04-01 08:21:00', '2026-04-01 08:21:00'),
(4, 'FLT-OLI-HMOT', 'Filter Oli Honda Brio', 'Honda', 'Filter', 'pcs', 38000.00, 58000.00, 25, 5, 'Filter oli untuk Honda Brio.', 1, '2026-04-01 08:21:00', '2026-04-01 08:21:00'),
(5, 'BUS-NGK-BP6ES', 'Busi NGK BP6ES', 'NGK', 'Ignition', 'pcs', 25000.00, 40000.00, 76, 20, 'Busi standar untuk tune-up mesin.', 1, '2026-04-01 08:22:00', '2026-04-01 08:22:00'),
(6, 'BUS-NGK-ILKAR8B7', 'Busi NGK Iridium ILKAR8B7', 'NGK', 'Ignition', 'pcs', 85000.00, 130000.00, 30, 10, 'Busi iridium untuk performa tinggi.', 1, '2026-04-01 08:22:00', '2026-04-01 08:22:00'),
(7, 'BRK-FLT-UNIV', 'Filter Bahan Bakar Universal', 'Sakura', 'Filter', 'pcs', 45000.00, 70000.00, 20, 5, 'Filter bahan bakar universal.', 1, '2026-04-01 08:23:00', '2026-04-01 08:23:00'),
(8, 'AC-FREON-R134A', 'Freon AC R134A 250gr', 'Daikin', 'AC', 'kaleng', 75000.00, 120000.00, 14, 5, 'Freon untuk service AC mobil.', 1, '2026-04-01 08:23:00', '2026-04-01 08:23:00'),
(9, 'BAN-BRGT-185/65', 'Ban Bridgestone Ecopia 185/65 R15', 'Bridgestone', 'Tire', 'pcs', 650000.00, 850000.00, 11, 3, 'Ban ecopia untuk pemakaian harian.', 1, '2026-04-01 08:24:00', '2026-04-01 08:24:00'),
(10, 'OLI-TRS-ATF', 'Oli Transmisi ATF Dexron III 1L', 'Castrol', 'Transmission', 'liter', 55000.00, 80000.00, 25, 8, 'Oli transmisi untuk mobil matic.', 1, '2026-04-01 08:24:00', '2026-04-01 08:24:00');

-- ============================================================
-- TRANSACTION DATA
-- ============================================================

INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `vehicle_id`, `service_id`, `slot_id`, `booking_date`, `notes`, `complaint`, `status`, `mechanic_id`, `mechanic_notes`, `cancel_reason`, `created_at`, `updated_at`) VALUES
(1, 'JBM-2026040001', 4, 1, 1, 1, '2026-04-06', 'Ganti oli, cek rem depan, dan reset indikator servis.', 'Suara mesin agak kasar saat start.', 'completed', 2, 'Pengerjaan selesai, oli dan filter sudah diganti.', NULL, '2026-04-06 08:15:00', '2026-04-06 17:35:00'),
(2, 'JBM-2026040002', 5, 3, 3, 2, '2026-04-07', 'Service AC dan cek kebocoran freon.', 'AC mulai kurang dingin saat siang.', 'waiting_payment', 3, 'Sistem AC normal setelah pengisian freon.', NULL, '2026-04-07 08:20:00', '2026-04-07 15:05:00'),
(3, 'JBM-2026040003', 6, 5, 2, 3, '2026-04-08', 'Tune-up berkala sebelum perjalanan jauh.', 'Tarikan mesin terasa berat.', 'in_progress', 2, 'Tune-up masih berlangsung dan menunggu final check.', NULL, '2026-04-08 08:40:00', '2026-04-08 11:30:00'),
(4, 'JBM-2026040004', 4, 2, 4, 4, '2026-04-09', 'Spooring untuk setir yang mulai menarik ke kiri.', NULL, 'confirmed', 3, NULL, NULL, '2026-04-09 09:00:00', '2026-04-09 10:10:00'),
(5, 'JBM-2026040005', 5, 4, 5, 5, '2026-04-10', 'Menunggu jadwal minggu ini.', 'Minta balancing sekaligus pemeriksaan ban.', 'pending', NULL, NULL, NULL, '2026-04-10 09:15:00', '2026-04-10 09:15:00'),
(6, 'JBM-2026040006', 6, 5, 6, 6, '2026-04-04', 'Booking dibatalkan karena kendaraan belum siap.', NULL, 'cancelled', NULL, NULL, 'Customer membatalkan booking sebelum datang ke bengkel.', '2026-04-04 08:30:00', '2026-04-04 09:05:00'),
(7, 'JBM-2026040007', 4, 1, 1, 7, '2026-04-11', 'Ganti oli rutin.', 'Oli sudah gelap dan mesin berisik.', 'waiting_payment', 2, 'Pekerjaan selesai, invoice sudah dibuat.', NULL, '2026-04-11 08:10:00', '2026-04-11 13:20:00'),
(8, 'JBM-2026040008', 5, 3, 6, 8, '2026-04-12', 'Ganti ban depan kanan dan balancing.', 'Ban aus pada sisi luar.', 'completed', 3, 'Ban dan balancing sudah dikerjakan, kendaraan siap diambil.', NULL, '2026-04-12 08:05:00', '2026-04-12 15:50:00');

INSERT INTO `booking_status_logs` (`id`, `booking_id`, `status`, `actor_id`, `note`, `created_at`) VALUES
(1, 1, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-06 08:15:00'),
(2, 1, 'confirmed', 1, 'Booking dikonfirmasi oleh admin.', '2026-04-06 09:00:00'),
(3, 1, 'in_progress', 2, 'Kendaraan mulai diservis.', '2026-04-06 10:00:00'),
(4, 1, 'waiting_payment', 2, 'Servis selesai, invoice dibuat.', '2026-04-06 16:45:00'),
(5, 1, 'completed', 1, 'Pembayaran dikonfirmasi. Servis selesai.', '2026-04-06 17:35:00'),
(6, 2, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-07 08:20:00'),
(7, 2, 'confirmed', 1, 'Booking dikonfirmasi oleh admin.', '2026-04-07 09:00:00'),
(8, 2, 'in_progress', 3, 'Service AC mulai dikerjakan.', '2026-04-07 10:30:00'),
(9, 2, 'waiting_payment', 3, 'Invoice dibuat, menunggu bukti transfer.', '2026-04-07 14:50:00'),
(10, 3, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-08 08:40:00'),
(11, 3, 'confirmed', 1, 'Booking dikonfirmasi oleh admin.', '2026-04-08 09:10:00'),
(12, 3, 'in_progress', 2, 'Tune-up sedang berlangsung.', '2026-04-08 10:15:00'),
(13, 4, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-09 09:00:00'),
(14, 4, 'confirmed', 1, 'Booking dikonfirmasi oleh admin.', '2026-04-09 10:10:00'),
(15, 5, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-10 09:15:00'),
(16, 6, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-04 08:30:00'),
(17, 6, 'cancelled', 6, 'Dibatalkan oleh customer.', '2026-04-04 09:05:00'),
(18, 7, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-11 08:10:00'),
(19, 7, 'confirmed', 1, 'Booking dikonfirmasi oleh admin.', '2026-04-11 09:00:00'),
(20, 7, 'in_progress', 2, 'Ganti oli sedang dikerjakan.', '2026-04-11 10:30:00'),
(21, 7, 'waiting_payment', 2, 'Servis selesai, invoice sudah dibuat.', '2026-04-11 13:20:00'),
(22, 8, 'pending', NULL, 'Booking baru dibuat oleh customer.', '2026-04-12 08:05:00'),
(23, 8, 'confirmed', 1, 'Booking dikonfirmasi oleh admin.', '2026-04-12 08:45:00'),
(24, 8, 'in_progress', 3, 'Ganti ban dan balancing sedang dikerjakan.', '2026-04-12 10:00:00'),
(25, 8, 'waiting_payment', 3, 'Invoice dibuat setelah pekerjaan selesai.', '2026-04-12 15:20:00'),
(26, 8, 'completed', 1, 'Pembayaran dikonfirmasi. Servis selesai.', '2026-04-12 15:50:00');

INSERT INTO `service_orders` (`id`, `booking_id`, `spare_part_id`, `type`, `description`, `quantity`, `unit_price`, `unit`, `created_at`) VALUES
(1, 1, NULL, 'jasa', 'Ganti Oli Mesin', 1, 75000.00, 'jasa', '2026-04-06 16:45:00'),
(2, 1, 3, 'parts', 'Filter Oli Toyota Avanza', 1, 55000.00, 'pcs', '2026-04-06 16:45:00'),
(3, 1, 1, 'parts', 'Oli Mesin Castrol 5W-30 1L', 1, 85000.00, 'liter', '2026-04-06 16:45:00'),
(4, 2, NULL, 'jasa', 'Service AC Lengkap', 1, 250000.00, 'jasa', '2026-04-07 14:50:00'),
(5, 2, 8, 'parts', 'Freon AC R134A 250gr', 1, 120000.00, 'kaleng', '2026-04-07 14:50:00'),
(6, 3, NULL, 'jasa', 'Tune-Up Mesin', 1, 350000.00, 'jasa', '2026-04-08 10:15:00'),
(7, 3, 5, 'parts', 'Busi NGK BP6ES', 4, 40000.00, 'pcs', '2026-04-08 10:15:00'),
(8, 7, NULL, 'jasa', 'Ganti Oli Mesin', 1, 75000.00, 'jasa', '2026-04-11 13:20:00'),
(9, 7, 2, 'parts', 'Oli Mesin Shell Helix 10W-40 1L', 2, 80000.00, 'liter', '2026-04-11 13:20:00'),
(10, 7, 3, 'parts', 'Filter Oli Toyota Avanza', 1, 55000.00, 'pcs', '2026-04-11 13:20:00'),
(11, 8, NULL, 'jasa', 'Ganti Ban Depan', 1, 50000.00, 'jasa', '2026-04-12 15:20:00'),
(12, 8, 9, 'parts', 'Ban Bridgestone Ecopia 185/65 R15', 1, 850000.00, 'pcs', '2026-04-12 15:20:00');

INSERT INTO `payments` (`id`, `invoice_number`, `booking_id`, `total_amount`, `method`, `status`, `proof_file`, `proof_uploaded_at`, `paid_at`, `confirmed_by`, `reject_note`, `created_at`, `updated_at`) VALUES
(1, 'INV-2026040001', 1, 215000.00, 'cash', 'paid', NULL, NULL, '2026-04-06 17:35:00', 1, NULL, '2026-04-06 16:45:00', '2026-04-06 17:35:00'),
(2, 'INV-2026040002', 2, 370000.00, 'transfer', 'waiting_confirmation', 'payment_proofs/proof-20260407-dedi.jpg', '2026-04-07 15:05:00', NULL, NULL, NULL, '2026-04-07 14:50:00', '2026-04-07 15:05:00'),
(3, 'INV-2026040003', 7, 290000.00, 'transfer', 'unpaid', NULL, NULL, NULL, NULL, NULL, '2026-04-11 13:00:00', '2026-04-11 13:00:00'),
(4, 'INV-2026040004', 8, 900000.00, 'cash', 'paid', NULL, NULL, '2026-04-12 15:50:00', 1, NULL, '2026-04-12 15:20:00', '2026-04-12 15:50:00');

INSERT INTO `payment_items` (`id`, `payment_id`, `description`, `type`, `quantity`, `unit_price`) VALUES
(1, 1, 'Ganti Oli Mesin', 'jasa', 1, 75000.00),
(2, 1, 'Filter Oli Toyota Avanza', 'parts', 1, 55000.00),
(3, 1, 'Oli Mesin Castrol 5W-30 1L', 'parts', 1, 85000.00),
(4, 2, 'Service AC Lengkap', 'jasa', 1, 250000.00),
(5, 2, 'Freon AC R134A 250gr', 'parts', 1, 120000.00),
(6, 3, 'Ganti Oli Mesin', 'jasa', 1, 75000.00),
(7, 3, 'Oli Mesin Shell Helix 10W-40 1L', 'parts', 2, 80000.00),
(8, 3, 'Filter Oli Toyota Avanza', 'parts', 1, 55000.00),
(9, 4, 'Ganti Ban Depan', 'jasa', 1, 50000.00),
(10, 4, 'Ban Bridgestone Ecopia 185/65 R15', 'parts', 1, 850000.00);

INSERT INTO `stock_mutations` (`id`, `spare_part_id`, `type`, `quantity`, `stock_before`, `stock_after`, `reason`, `note`, `created_by`, `created_at`) VALUES
(1, 1, 'in', 50, 0, 50, 'purchase', 'Stok awal', 1, '2026-04-01 08:20:00'),
(2, 1, 'out', 1, 50, 49, 'usage', 'Digunakan untuk booking JBM-2026040001', 2, '2026-04-06 16:45:00'),
(3, 2, 'in', 40, 0, 40, 'purchase', 'Stok awal', 1, '2026-04-01 08:20:00'),
(4, 2, 'out', 2, 40, 38, 'usage', 'Digunakan untuk booking JBM-2026040007', 2, '2026-04-11 13:20:00'),
(5, 3, 'in', 30, 0, 30, 'purchase', 'Stok awal', 1, '2026-04-01 08:21:00'),
(6, 3, 'out', 1, 30, 29, 'usage', 'Digunakan untuk booking JBM-2026040001', 2, '2026-04-06 16:45:00'),
(7, 3, 'out', 1, 29, 28, 'usage', 'Digunakan untuk booking JBM-2026040007', 2, '2026-04-11 13:20:00'),
(8, 5, 'in', 80, 0, 80, 'purchase', 'Stok awal', 1, '2026-04-01 08:22:00'),
(9, 5, 'out', 4, 80, 76, 'usage', 'Digunakan untuk booking JBM-2026040003', 2, '2026-04-08 11:30:00'),
(10, 8, 'in', 15, 0, 15, 'purchase', 'Stok awal', 1, '2026-04-01 08:23:00'),
(11, 8, 'out', 1, 15, 14, 'usage', 'Digunakan untuk booking JBM-2026040002', 3, '2026-04-07 15:05:00'),
(12, 9, 'in', 12, 0, 12, 'purchase', 'Stok awal', 1, '2026-04-01 08:24:00'),
(13, 9, 'out', 1, 12, 11, 'usage', 'Digunakan untuk booking JBM-2026040008', 3, '2026-04-12 15:20:00');

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `ref_id`, `is_read`, `url`, `created_at`) VALUES
(1, 4, 'Booking Berhasil Dibuat', 'Booking JBM-2026040001 untuk Ganti Oli sudah dibuat dan menunggu konfirmasi.', 'booking', 1, 1, 'my/booking/1', '2026-04-06 08:20:00'),
(2, 4, 'Booking Dikonfirmasi', 'Booking JBM-2026040001 sudah dikonfirmasi oleh admin.', 'booking', 1, 1, 'my/booking/1', '2026-04-06 09:05:00'),
(3, 4, 'Servis Selesai', 'Booking JBM-2026040001 sudah selesai dan pembayaran telah diterima.', 'service', 1, 0, 'my/booking/1', '2026-04-06 17:40:00'),
(4, 5, 'Menunggu Konfirmasi Pembayaran', 'Bukti transfer untuk booking JBM-2026040002 sudah diupload dan menunggu konfirmasi admin.', 'payment', 2, 0, 'my/booking/2/payment', '2026-04-07 15:10:00'),
(5, 6, 'Booking Sedang Diservis', 'Booking JBM-2026040003 sedang dikerjakan oleh mekanik.', 'service', 3, 0, 'my/booking/3', '2026-04-08 10:30:00'),
(6, 4, 'Tagihan Baru', 'Invoice INV-2026040003 untuk booking JBM-2026040007 sudah dibuat.', 'payment', 7, 0, 'my/booking/7/payment', '2026-04-11 13:05:00'),
(7, 5, 'Booking Selesai', 'Booking JBM-2026040008 selesai dan pembayaran sudah diterima.', 'service', 8, 1, 'my/booking/8', '2026-04-12 15:55:00'),
(8, 6, 'Booking Dibatalkan', 'Booking JBM-2026040006 dibatalkan sesuai permintaan.', 'booking', 6, 1, 'my/booking/6', '2026-04-04 09:05:00');

INSERT INTO `testimonials` (`id_testimonial`, `id_user`, `name`, `avatar`, `rating`, `content`, `is_approved`, `created_at`) VALUES
(1, 4, 'Siti Rahayu', NULL, 5, 'Pelayanan sangat memuaskan. Mekaniknya profesional dan ramah.', 1, '2026-04-01 09:00:00'),
(2, 5, 'Dedi Kurniawan', NULL, 5, 'Ganti oli di JBM cepat dan hasilnya bagus. Harga juga transparan.', 1, '2026-04-01 09:10:00'),
(3, 6, 'Rina Wulandari', NULL, 4, 'Servis AC mobil saya tuntas dan teknisi menjelaskan masalah dengan detail.', 1, '2026-04-01 09:20:00'),
(4, NULL, 'Bapak Hartono', NULL, 5, 'Sudah lama jadi pelanggan JBM dan tidak pernah mengecewakan.', 1, '2026-04-01 09:30:00'),
(5, NULL, 'Ibu Dewi', NULL, 4, 'Tempatnya bersih, ruang tunggu nyaman, dan pengerjaannya cepat.', 1, '2026-04-01 09:40:00');

INSERT INTO `gallery` (`id_gallery`, `title`, `image`, `description`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Area Bengkel Utama', 'gallery/bengkel-utama.jpg', 'Area utama pengerjaan kendaraan dengan peralatan modern', 1, 1, '2026-04-01 10:00:00'),
(2, 'Ruang Tunggu Nyaman', 'gallery/ruang-tunggu.jpg', 'Ruang tunggu ber-AC dengan WiFi gratis dan refreshment', 2, 1, '2026-04-01 10:05:00'),
(3, 'Tim Mekanik Profesional', 'gallery/tim-mekanik.jpg', 'Tim mekanik bersertifikat dan berpengalaman lebih dari 10 tahun', 3, 1, '2026-04-01 10:10:00'),
(4, 'Peralatan Diagnosa', 'gallery/peralatan.jpg', 'Dilengkapi alat diagnosa komputer terkini untuk akurasi servis', 4, 1, '2026-04-01 10:15:00'),
(5, 'Service AC Mobil', 'gallery/service-ac.jpg', 'Proses pengisian freon dan perbaikan sistem AC mobil', 5, 1, '2026-04-01 10:20:00'),
(6, 'Spooring & Balancing', 'gallery/spooring.jpg', 'Mesin spooring digital untuk hasil presisi', 6, 1, '2026-04-01 10:25:00');

INSERT INTO `settings` (`id_setting`, `key`, `value`, `description`) VALUES
(1, 'workshop_name', 'JBM Bengkel Mobil', 'Nama Bengkel'),
(2, 'workshop_tagline', 'Servis Terpercaya, Kualitas Terjamin', 'Tagline Bengkel'),
(3, 'workshop_phone', '0812-3456-7890', 'Nomor Telepon Bengkel'),
(4, 'workshop_wa', '6281234567890', 'Nomor WhatsApp (format internasional tanpa +)'),
(5, 'workshop_email', 'info@jbmbengkel.com', 'Email Bengkel'),
(6, 'workshop_address', 'Jl. Raya Serpong No. 88, Tangerang Selatan', 'Alamat Lengkap'),
(7, 'workshop_city', 'Tangerang Selatan', 'Kota'),
(8, 'workshop_lat', '-6.2946', 'Latitude Google Maps'),
(9, 'workshop_lng', '106.6674', 'Longitude Google Maps'),
(10, 'workshop_maps_embed', 'https://maps.google.com/maps?q=-6.2946,106.6674&output=embed', 'Embed Google Maps URL'),
(11, 'bank_name', 'BCA', 'Nama Bank Transfer'),
(12, 'bank_account', '1234567890', 'Nomor Rekening'),
(13, 'bank_holder', 'JBM Bengkel Mobil', 'Nama Pemilik Rekening'),
(14, 'operating_hours', 'Senin - Sabtu: 08.00 - 17.00 WIB', 'Jam Operasional'),
(15, 'max_booking_days', '14', 'Maksimal hari booking ke depan'),
(16, 'invoice_prefix', 'INV', 'Prefix nomor invoice'),
(17, 'booking_prefix', 'JBM', 'Prefix kode booking');
