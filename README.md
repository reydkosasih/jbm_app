# JBM Bengkel Mobil

Web application bengkel mobil berbasis CodeIgniter 3 untuk booking servis, monitoring progres, pembayaran, dan operasional admin.

## Requirements
- PHP 7.4+
- MySQL 8.0+
- Composer
- Apache or Nginx

## Installation
1. Install dependency dengan `composer install`.
2. Copy `.env.example` menjadi `.env` lalu isi kredensial yang sesuai.
3. Import `database/jbm_schema.sql` ke MySQL.
4. Pastikan folder `uploads/` dan `application/logs/` writable.
5. Atur web server menggunakan konfigurasi pada `deployment/nginx.conf` atau Apache `.htaccess`.

## Default Login
- Admin: `admin@jbm.id` / `admin123`

Ganti password default segera setelah deploy.

## Stack
- CodeIgniter 3
- Bootstrap 5
- jQuery + AJAX
- SweetAlert2
- MySQL
- dompdf
