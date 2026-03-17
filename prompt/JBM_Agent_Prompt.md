# 🤖 JBM BENGKEL MOBIL — AI AGENT EXECUTION PLAN
> **Mode**: Full-stack Web Development Agent  
> **Stack**: CodeIgniter 3 · Bootstrap 5 · MySQL · AJAX · SweetAlert2  
> **Target**: Production-Ready Web Application  
> **Execution**: Sequential Phase-by-Phase — complete each phase fully before moving to next

---

## ⚙️ AGENT DIRECTIVES

```
ROLE: You are a senior full-stack developer agent.
TASK: Build a complete, production-ready web application for "JBM Bengkel Mobil" (auto repair shop).
RULES:
  1. Execute each PHASE sequentially. Do not skip.
  2. Generate REAL, WORKING code — no placeholders, no pseudocode.
  3. Every file must be complete and immediately runnable.
  4. Use CodeIgniter 3 MVC conventions strictly.
  5. All SQL must be valid MySQL 8.0 syntax.
  6. All UI must use Bootstrap 5 and be mobile-responsive.
  7. After each phase, output a PHASE COMPLETE checklist.
  8. If a decision is ambiguous, choose the most production-safe option.
```

---

## 📁 PHASE 0 — PROJECT SCAFFOLD & DATABASE

### 0.1 — Generate Full MySQL Schema

**AGENT TASK**: Create file `database/jbm_schema.sql` with the following tables. Generate complete SQL with:
- `CREATE DATABASE IF NOT EXISTS jbm_bengkel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`
- All tables use `InnoDB`, `utf8mb4`, proper `FOREIGN KEY` constraints with `ON DELETE CASCADE` or `ON DELETE SET NULL` as appropriate
- Include `INDEX` on all foreign key columns and frequently queried columns
- Insert realistic seed data (minimum 5 rows per master table)

**TABLES TO GENERATE**:

```
TABLE: users
  - id_user         INT UNSIGNED AUTO_INCREMENT PK
  - name            VARCHAR(100) NOT NULL
  - email           VARCHAR(150) UNIQUE NOT NULL
  - phone           VARCHAR(20)
  - password        VARCHAR(255) NOT NULL  -- bcrypt hash
  - role            ENUM('admin','kasir','mekanik','customer') DEFAULT 'customer'
  - avatar          VARCHAR(255) DEFAULT NULL
  - is_active       TINYINT(1) DEFAULT 1
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  - updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

TABLE: vehicles
  - id_vehicle      INT UNSIGNED AUTO_INCREMENT PK
  - id_user         INT UNSIGNED NOT NULL FK→users
  - brand           VARCHAR(50) NOT NULL  -- Toyota, Honda, dll
  - model           VARCHAR(50) NOT NULL  -- Avanza, Brio, dll
  - year            YEAR NOT NULL
  - license_plate   VARCHAR(20) NOT NULL
  - color           VARCHAR(30)
  - notes           TEXT
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: services
  - id_service      INT UNSIGNED AUTO_INCREMENT PK
  - name            VARCHAR(100) NOT NULL  -- Ganti Oli, Tune-up, dll
  - description     TEXT
  - base_price      DECIMAL(12,2) NOT NULL
  - duration_min    INT DEFAULT 60  -- estimasi durasi dalam menit
  - is_active       TINYINT(1) DEFAULT 1

TABLE: time_slots
  - id_slot         INT UNSIGNED AUTO_INCREMENT PK
  - slot_time       TIME NOT NULL  -- 08:00, 09:00, dst
  - max_booking     INT DEFAULT 2  -- kapasitas per slot
  - is_active       TINYINT(1) DEFAULT 1

TABLE: bookings
  - id_booking      INT UNSIGNED AUTO_INCREMENT PK
  - booking_code    VARCHAR(20) UNIQUE NOT NULL  -- JBM-20250001
  - id_user         INT UNSIGNED NOT NULL FK→users
  - id_vehicle      INT UNSIGNED NOT NULL FK→vehicles
  - id_service      INT UNSIGNED NOT NULL FK→services
  - id_slot         INT UNSIGNED FK→time_slots
  - booking_date    DATE NOT NULL
  - complaint       TEXT  -- keluhan customer
  - status          ENUM('pending','confirmed','queued','in_progress','done','cancelled') DEFAULT 'pending'
  - estimated_done  DATETIME DEFAULT NULL
  - notes_admin     TEXT
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  - updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

TABLE: service_orders
  - id_order        INT UNSIGNED AUTO_INCREMENT PK
  - id_booking      INT UNSIGNED UNIQUE NOT NULL FK→bookings
  - id_mechanic     INT UNSIGNED FK→users (role=mekanik)
  - started_at      DATETIME
  - finished_at     DATETIME
  - mechanic_notes  TEXT

TABLE: booking_status_logs
  - id_log          INT UNSIGNED AUTO_INCREMENT PK
  - id_booking      INT UNSIGNED NOT NULL FK→bookings
  - old_status      VARCHAR(20)
  - new_status      VARCHAR(20) NOT NULL
  - changed_by      INT UNSIGNED FK→users
  - notes           TEXT
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: payments
  - id_payment      INT UNSIGNED AUTO_INCREMENT PK
  - id_booking      INT UNSIGNED UNIQUE NOT NULL FK→bookings
  - invoice_number  VARCHAR(30) UNIQUE NOT NULL  -- INV-20250001
  - method          ENUM('cash','transfer') NOT NULL
  - bank_name       VARCHAR(50) DEFAULT NULL
  - proof_image     VARCHAR(255) DEFAULT NULL  -- path bukti transfer
  - subtotal        DECIMAL(12,2) NOT NULL
  - discount        DECIMAL(12,2) DEFAULT 0
  - total           DECIMAL(12,2) NOT NULL
  - status          ENUM('pending','waiting_confirm','paid','cancelled') DEFAULT 'pending'
  - paid_at         DATETIME DEFAULT NULL
  - confirmed_by    INT UNSIGNED FK→users
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: payment_items
  - id_item         INT UNSIGNED AUTO_INCREMENT PK
  - id_payment      INT UNSIGNED NOT NULL FK→payments
  - item_type       ENUM('service','spare_part') NOT NULL
  - item_name       VARCHAR(150) NOT NULL
  - quantity        INT DEFAULT 1
  - unit_price      DECIMAL(12,2) NOT NULL
  - subtotal        DECIMAL(12,2) NOT NULL

TABLE: spare_parts
  - id_part         INT UNSIGNED AUTO_INCREMENT PK
  - part_code       VARCHAR(30) UNIQUE NOT NULL
  - name            VARCHAR(150) NOT NULL
  - category        VARCHAR(50)  -- Oli, Filter, Busi, dll
  - brand           VARCHAR(50)
  - unit            VARCHAR(20) DEFAULT 'pcs'
  - buy_price       DECIMAL(12,2) NOT NULL
  - sell_price      DECIMAL(12,2) NOT NULL
  - stock           INT DEFAULT 0
  - min_stock       INT DEFAULT 5  -- threshold alert
  - is_active       TINYINT(1) DEFAULT 1
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: stock_mutations
  - id_mutation     INT UNSIGNED AUTO_INCREMENT PK
  - id_part         INT UNSIGNED NOT NULL FK→spare_parts
  - id_order        INT UNSIGNED FK→service_orders (NULL jika restock)
  - type            ENUM('in','out') NOT NULL
  - quantity        INT NOT NULL
  - notes           TEXT
  - created_by      INT UNSIGNED FK→users
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: notifications
  - id_notif        INT UNSIGNED AUTO_INCREMENT PK
  - id_user         INT UNSIGNED NOT NULL FK→users
  - title           VARCHAR(150) NOT NULL
  - message         TEXT NOT NULL
  - type            ENUM('booking','payment','service','stock','system') DEFAULT 'system'
  - is_read         TINYINT(1) DEFAULT 0
  - url             VARCHAR(255) DEFAULT NULL
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: testimonials
  - id_testimonial  INT UNSIGNED AUTO_INCREMENT PK
  - id_user         INT UNSIGNED FK→users
  - customer_name   VARCHAR(100) NOT NULL
  - content         TEXT NOT NULL
  - rating          TINYINT(1) DEFAULT 5  -- 1-5
  - is_approved     TINYINT(1) DEFAULT 0
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: gallery
  - id_gallery      INT UNSIGNED AUTO_INCREMENT PK
  - title           VARCHAR(150)
  - image_path      VARCHAR(255) NOT NULL
  - sort_order      INT DEFAULT 0
  - is_active       TINYINT(1) DEFAULT 1
  - created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP

TABLE: settings
  - id_setting      INT UNSIGNED AUTO_INCREMENT PK
  - key_name        VARCHAR(100) UNIQUE NOT NULL
  - value           TEXT
  - description     VARCHAR(255)
```

**SEED DATA REQUIRED**:
- 1 admin user (password: `admin123` bcrypt), 2 mekanik, 3 customer
- 6 jenis services (Ganti Oli, Tune-up, Service AC, Spooring, Balancing, Ganti Ban)
- Time slots 08:00–16:00 (interval 1 jam)
- 10 spare parts (oli Castrol, filter oli Shell, busi NGK, dll.)
- 5 testimonials (approved)
- Settings: workshop_name, workshop_phone, workshop_address, workshop_lat, workshop_lng, bank_name, bank_account, bank_holder

---

### 0.2 — Generate CI3 Base Configuration

**AGENT TASK**: Generate these config files completely:

**File**: `application/config/config.php` (patch only relevant keys)
```
base_url        = auto-detect via $_SERVER
encryption_key  = random 32-char string
sess_driver     = database
sess_cookie_name = jbm_session
sess_expiration = 7200
csrf_protection = TRUE
csrf_token_name = jbm_csrf_token
```

**File**: `application/config/database.php`
```
hostname = localhost
username = root (configurable via .env pattern using getenv())
password = (via getenv())
database = jbm_bengkel
dbdriver = mysqli
db_debug = (getenv('CI_ENV') != 'production')
char_set = utf8mb4
dbcollat  = utf8mb4_unicode_ci
```

**File**: `application/config/routes.php`
```php
// Public routes
$route['default_controller'] = 'Landing';
$route['about']              = 'Landing/about';
$route['services']           = 'Landing/services';

// Auth routes
$route['login']              = 'Auth/login';
$route['register']           = 'Auth/register';
$route['logout']             = 'Auth/logout';
$route['forgot-password']    = 'Auth/forgot_password';
$route['reset-password/(:any)'] = 'Auth/reset_password/$1';

// Customer routes (prefix: my/)
$route['my/dashboard']       = 'Customer/dashboard';
$route['my/booking']         = 'Customer/booking';
$route['my/booking/store']   = 'Customer/store_booking';
$route['my/booking/(:num)']  = 'Customer/booking_detail/$1';
$route['my/status']          = 'Customer/service_status';
$route['my/payment/(:num)']  = 'Customer/payment/$1';
$route['my/payment/upload/(:num)'] = 'Customer/upload_proof/$1';
$route['my/history']         = 'Customer/history';
$route['my/profile']         = 'Customer/profile';
$route['my/vehicles']        = 'Customer/vehicles';

// Admin routes (prefix: admin/)
$route['admin']                     = 'Admin/dashboard';
$route['admin/queue']               = 'Admin/queue';
$route['admin/queue/update-status'] = 'Admin/update_status';
$route['admin/bookings']            = 'Admin/bookings';
$route['admin/bookings/(:num)']     = 'Admin/booking_detail/$1';
$route['admin/payments']            = 'Admin/payments';
$route['admin/payments/confirm/(:num)'] = 'Admin/confirm_payment/$1';
$route['admin/stock']               = 'Admin/stock';
$route['admin/stock/(:num)']        = 'Admin/stock_detail/$1';
$route['admin/reports']             = 'Admin/reports';
$route['admin/reports/daily']       = 'Admin/report_daily';
$route['admin/reports/monthly']     = 'Admin/report_monthly';
$route['admin/users']               = 'Admin/users';
$route['admin/settings']            = 'Admin/settings';

// AJAX / API routes
$route['api/notifications']         = 'Api/notifications';
$route['api/notifications/read/(:num)'] = 'Api/read_notification/$1';
$route['api/booking/check-slots']   = 'Api/check_slots';
$route['api/booking/status/(:any)'] = 'Api/booking_status/$1';
$route['api/stock/low']             = 'Api/low_stock';
```

**File**: `application/config/autoload.php`
```php
$autoload['libraries'] = ['database', 'session', 'form_validation', 'upload', 'email'];
$autoload['helpers']   = ['url', 'form', 'html', 'file', 'security', 'date'];
$autoload['model']     = [];
```

---

### 0.3 — Generate Base Controller (MY_Controller)

**AGENT TASK**: Create `application/core/MY_Controller.php` with:
- `class MY_Controller extends CI_Controller`
- Method `__construct()`: load common data (site settings, auth check helper)
- Method `is_logged_in()`: return bool from session
- Method `get_user()`: return current user array from session
- Method `require_login()`: redirect to /login if not logged in
- Method `require_role($role)`: check role, show 403 if unauthorized — role can be string or array
- Method `set_notification($type, $message)`: wrapper untuk flashdata SweetAlert
- Method `json_response($data, $status = 200)`: output JSON with proper headers
- Child class `Customer_Controller extends MY_Controller`: auto-calls `require_role('customer')`
- Child class `Admin_Controller extends MY_Controller`: auto-calls `require_role(['admin','kasir'])`

---

### 0.4 — Generate Master Layout Views

**AGENT TASK**: Create these layout files with **complete, valid HTML5**. Use Bootstrap 5.3 CDN. Include SweetAlert2 CDN. All layouts must support:
- Flash notification display via SweetAlert2 (read from CI flashdata)
- CSRF meta tag in `<head>` for AJAX
- Active nav-link detection based on current URL

**File**: `application/views/layouts/public.php`
- Header: navbar responsive (logo JBM + nav: Beranda, Layanan, Galeri, Testimoni, Lokasi + button Login & Daftar)
- Footer: info bengkel, social media links, copyright
- `<?= $content ?>` as main yield area

**File**: `application/views/layouts/customer.php`
- Sidebar (mobile: bottom nav, desktop: left sidebar)
- Sidebar items: Dashboard, Booking Baru, Status Servis, Riwayat, Profil
- Top navbar: notifikasi bell icon (badge count), nama user, logout
- Real-time notification badge via AJAX polling setiap 30 detik

**File**: `application/views/layouts/admin.php`
- Top navbar: logo, hamburger, notif bell, user dropdown
- Sidebar: Dashboard, Antrean Hari Ini, Manajemen Booking, Pembayaran, Stok, Laporan, User, Pengaturan
- Collapsible sidebar for mobile
- Stok alert banner jika ada item di bawah min_stock

---

**PHASE 0 COMPLETE CHECKLIST**:
```
[ ] jbm_schema.sql generated with all 15 tables + seed data
[ ] config.php configured
[ ] database.php configured with getenv() pattern
[ ] routes.php with all defined routes
[ ] autoload.php configured
[ ] MY_Controller.php with all methods
[ ] Customer_Controller & Admin_Controller created
[ ] Layout: public.php complete
[ ] Layout: customer.php with AJAX polling
[ ] Layout: admin.php with stock alert
```

---

## 🌐 PHASE 1 — LANDING PAGE

**AGENT TASK**: Create `application/controllers/Landing.php` and all associated views.

### Controller: `Landing.php`
```
Methods:
  index()         → load landing page (all sections in one page)
  get_settings()  → private, return settings array from DB
```

### View Sections (all in `application/views/landing/`):

**`index.php`** — Single-page layout menggunakan layout `public.php`, berisi include semua section:

**Section: Hero** (`_hero.php`)
- Full-width background gradient (biru gelap ke hitam) atau gambar bengkel
- Headline: "Servis Mobil Terpercaya di [Kota]"
- Sub-headline dengan tagline JBM
- 2 CTA buttons: "Booking Sekarang" (→/my/booking) dan "Lihat Layanan" (scroll anchor)
- Animasi CSS sederhana pada teks

**Section: Stats** (`_stats.php`)
- 4 counter cards: Total Pelanggan, Tahun Pengalaman, Mekanik Bersertifikat, Rating Bintang
- Animasi counter JavaScript saat scroll ke section ini

**Section: Services** (`_services.php`)
- Grid card Bootstrap 3 kolom (responsive 1 col mobile)
- Data dari DB via `services` table (is_active=1)
- Setiap card: ikon FontAwesome, nama servis, deskripsi singkat, harga mulai dari, tombol "Booking"

**Section: Why JBM** (`_why.php`)
- 4 keunggulan dengan ikon dan teks
- Layout 2 kolom: teks kiri, ilustrasi kanan

**Section: Gallery** (`_gallery.php`)
- Masonry/grid layout foto dari tabel `gallery`
- Lightbox sederhana (Bootstrap modal) saat klik foto

**Section: Testimonials** (`_testimonials.php`)
- Carousel Bootstrap 5
- Data dari tabel `testimonials` (is_approved=1)
- Setiap card: foto avatar (placeholder initial jika null), nama, rating bintang, isi testimonial

**Section: Location** (`_location.php`)
- Embed Google Maps iframe menggunakan `workshop_lat` & `workshop_lng` dari settings
- Di sebelahnya: alamat lengkap, jam operasional (Senin–Sabtu 08.00–17.00), nomor HP/WA (klik to call)
- Tombol "Petunjuk Arah" → buka Google Maps di tab baru

**PHASE 1 COMPLETE CHECKLIST**:
```
[ ] Landing controller created
[ ] All 7 section partials created with real Bootstrap 5 HTML
[ ] Services loaded dynamically from DB
[ ] Gallery loaded dynamically from DB
[ ] Testimonials loaded dynamically from DB
[ ] Google Maps embed uses settings from DB
[ ] Counter animation JS implemented
[ ] Page is fully responsive (test 3 breakpoints: 375px, 768px, 1280px)
```

---

## 👤 PHASE 2 — AUTHENTICATION

**AGENT TASK**: Create `application/controllers/Auth.php` and all auth views.

### Model: `application/models/User_model.php`
```
Methods:
  get_by_email($email)           → return user row or false
  get_by_id($id)                 → return user row
  create($data)                  → insert + return id
  update($id, $data)             → update user
  set_reset_token($email, $token, $expires) → update reset fields
  get_by_reset_token($token)     → return user or false
  clear_reset_token($id)         → nullify token fields
```

### Controller Methods:

**`login()`**
- GET: show form
- POST (AJAX): validate email+password, bcrypt verify, set session:
  ```php
  $this->session->set_userdata([
    'user_id'   => $user['id_user'],
    'user_name' => $user['name'],
    'user_role' => $user['role'],
    'logged_in' => true
  ]);
  ```
- Return JSON: `{success: true, redirect: '/my/dashboard'}` or `{success: false, message: '...'}`
- Rate limit: simpan failed attempts di session, block setelah 5x selama 10 menit

**`register()`**
- GET: show form
- POST (AJAX): CI3 form_validation rules (required, valid_email, min_length[8], is_unique[users.email])
- Hash password dengan `password_hash($pass, PASSWORD_BCRYPT, ['cost'=>12])`
- Return JSON sukses/error

**`logout()`**
- `$this->session->sess_destroy()` → redirect ke /login

**`forgot_password()`**
- POST: generate token `bin2hex(random_bytes(32))`, simpan ke DB dengan expiry 1 jam
- Kirim email via PHPMailer (atau CI3 email library) dengan link reset
- Return JSON

**`reset_password($token)`**
- GET: validasi token, tampilkan form baru
- POST: update password, hapus token

### Views (`application/views/auth/`):
- `login.php` — kartu login centered, form email+password, link lupa password, link daftar
- `register.php` — form registrasi: nama, email, HP, password, konfirmasi password
- `forgot.php` — form input email
- `reset.php` — form input password baru + konfirmasi

**Semua form menggunakan**:
- Bootstrap 5 validation classes
- SweetAlert2 untuk feedback sukses/error
- AJAX submit (jQuery `$.ajax`)
- CSRF token di header: `'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')`

**PHASE 2 COMPLETE CHECKLIST**:
```
[ ] User_model with all methods
[ ] login() with rate limiting + AJAX response
[ ] register() with CI3 form_validation
[ ] logout() clears session
[ ] forgot_password() sends email
[ ] reset_password() validates token expiry
[ ] All 4 auth views complete
[ ] CSRF protection on all POST forms
[ ] Password hashed with bcrypt cost=12
[ ] Session data set correctly after login
```

---

## 📅 PHASE 3 — BOOKING SYSTEM

**AGENT TASK**: Build complete booking flow.

### Models Required:
- `Booking_model.php`
- `Vehicle_model.php`
- `Service_model.php`
- `Slot_model.php`

### `Booking_model.php` Methods:
```
create($data)                          → INSERT + return booking_code
get_by_user($id_user, $limit=10)       → list booking customer
get_by_id($id)                         → detail + joins
get_active_by_date($date)              → untuk cek kapasitas
count_slot_booking($date, $id_slot)    → jumlah booking per slot
update_status($id, $status, $by)       → update + insert ke booking_status_logs
generate_booking_code()                → JBM-YYYYXXXX (auto-increment per tahun)
cancel($id_booking, $reason)           → set status cancelled
get_all_with_filter($filters)          → untuk admin, support filter date/status
```

### Controller: `Customer.php` — method `booking()`

**GET** — Tampilkan form booking:
- Load `services` (active)
- Load `vehicles` milik customer dari DB
- Load `time_slots` (active)
- Pass ke view

**POST via AJAX** — Validasi & simpan:
```
Validation rules:
  id_service   → required, integer, exists in services
  id_vehicle   → required, integer, belongs to current user
  booking_date → required, valid date, not in the past, not Sunday
  id_slot      → required, integer, slot capacity not exceeded
  complaint    → optional, max_length[500]
  
On success:
  1. INSERT booking (status=pending)
  2. Generate booking_code
  3. INSERT notification untuk admin
  4. Send confirmation email ke customer (async jika memungkinkan)
  5. Return JSON: {success: true, booking_code: 'JBM-...', redirect: '/my/status'}
```

### AJAX Endpoint: `Api.php` — method `check_slots()`
```
GET params: date, id_service
Response: JSON array of slots with available count
  [{id_slot, slot_time, available: 2, is_full: false}, ...]
```

### View: `application/views/customer/booking.php`
- **Step 1**: Pilih Kendaraan (card grid dari kendaraan milik user, atau tombol "Tambah Kendaraan Baru")
- **Step 2**: Pilih Layanan (card grid dari services dengan harga)
- **Step 3**: Pilih Tanggal & Waktu (date picker, setelah pilih tanggal → AJAX load slot availability)
- **Step 4**: Review & Konfirmasi (SweetAlert2 confirm dengan ringkasan)
- Progress stepper di atas form
- Disable slot yang sudah penuh (visual: badge "Penuh" + disabled)

### View: `application/views/customer/booking_detail.php`
- Detail booking: kode, tanggal, kendaraan, layanan, status badge
- Timeline status history dari `booking_status_logs`
- Tombol Cancel (jika status masih pending/confirmed)

**PHASE 3 COMPLETE CHECKLIST**:
```
[ ] Booking_model with all methods including generate_booking_code()
[ ] Vehicle_model CRUD
[ ] check_slots AJAX endpoint working
[ ] Booking form with 4-step wizard UI
[ ] Slot capacity validation server-side
[ ] Booking date validation (no past, no Sunday)
[ ] Notification created for admin on new booking
[ ] Confirmation email sent
[ ] booking_detail view with status timeline
[ ] Cancel booking functionality
```

---

## 📡 PHASE 4 — REAL-TIME SERVICE STATUS

**AGENT TASK**: Implement real-time status tracking system.

### AJAX Polling Implementation (Primary Method):

**File**: `assets/js/realtime.js`
```javascript
// Poll every 30 seconds for booking status updates
// Poll every 30 seconds for unread notification count
// On status change: show SweetAlert2 toast notification
// On new notification: update bell badge count
// Store last known status in localStorage to detect changes
```

### AJAX Endpoint: `Api.php` — method `booking_status($booking_code)`
```
Response:
{
  booking_code: "JBM-20250001",
  status: "in_progress",
  status_label: "Sedang Dikerjakan",
  status_color: "warning",
  mechanic_name: "Budi Santoso",
  estimated_done: "14:30",
  last_updated: "2025-01-01 13:00:00",
  timeline: [
    {status: "pending", label: "Booking Diterima", time: "08:00"},
    {status: "confirmed", label: "Dikonfirmasi Admin", time: "08:15"},
    ...
  ]
}
```

### View: `application/views/customer/status.php`
- List semua booking aktif (status != done/cancelled)
- Setiap booking: progress bar visual (5 langkah), status badge berwarna
- Countdown timer jika `estimated_done` diset
- Timeline vertikal riwayat status
- Auto-refresh via `realtime.js`

### AJAX Endpoint: `Api.php` — method `notifications()`
```
Response:
{
  unread_count: 3,
  notifications: [
    {id, title, message, type, is_read, url, created_at, time_ago},
    ...
  ]
}
```

**PHASE 4 COMPLETE CHECKLIST**:
```
[ ] realtime.js implemented with 30s polling
[ ] booking_status API endpoint returns full timeline
[ ] notifications API endpoint working
[ ] Status page with visual progress bar
[ ] SweetAlert2 toast on status change
[ ] Notification bell badge auto-updates
[ ] Mark notification as read (click → AJAX)
[ ] Estimated done countdown timer
```

---

## 💳 PHASE 5 — PAYMENT SYSTEM

**AGENT TASK**: Build complete payment flow for cash and transfer.

### `Payment_model.php` Methods:
```
create($data)                  → INSERT payment + items, return id
get_by_booking($id_booking)    → payment + items joined
get_by_id($id)                 → full detail
update_status($id, $status, $confirmed_by=null)
generate_invoice_number()      → INV-YYYYXXXX
get_pending_transfer()         → untuk admin: list yg perlu dikonfirmasi
get_report_daily($date)        → sum per hari
get_report_monthly($year)      → sum per bulan, grouped by month
```

### Admin Flow — Create Invoice:
**Trigger**: Admin update status booking ke `done`
**Action**: Auto-create payment record dengan status `pending`

**Controller**: `Admin.php` — method `update_status()`
```
POST AJAX:
  1. Update booking status
  2. Insert ke booking_status_logs
  3. Jika new_status == 'done': 
     a. create payment record (ambil base_price dari service)
     b. admin input spare parts yang dipakai → create payment_items
     c. kurangi stok spare_parts
     d. insert stock_mutations
  4. Kirim notifikasi ke customer
  5. Return JSON
```

### Customer Flow — Bayar:

**View**: `application/views/customer/payment.php`
- Tampilkan invoice: rincian jasa + spare parts, subtotal, total
- Pilihan metode: 2 card besar (Tunai / Transfer Bank)
- Jika Transfer: tampilkan info rekening (dari settings), nominal, batas konfirmasi 24 jam
- Form upload bukti transfer (drag & drop + preview)
- Status badge pembayaran

**Controller**: `Customer.php` — method `upload_proof($id_payment)`
```
POST:
  - Validasi: file type (jpg/png/pdf), max 2MB
  - Rename: proof_[timestamp]_[random].[ext]
  - Simpan ke assets/uploads/payments/
  - Update payment: proof_image = filename, status = 'waiting_confirm'
  - Notifikasi ke admin: ada bukti transfer baru
  - Return JSON
```

### Admin — Konfirmasi Pembayaran:

**Controller**: `Admin.php` — method `confirm_payment($id)`
```
POST AJAX:
  - Update payment status ke 'paid', set paid_at, confirmed_by
  - Notifikasi ke customer: pembayaran dikonfirmasi
  - Return JSON
```

**View**: `application/views/admin/payments.php`
- Tab: Menunggu Konfirmasi | Semua Pembayaran
- Tabel dengan: invoice no, customer, total, metode, proof (preview), tombol Konfirmasi/Tolak
- Modal preview bukti transfer

### PDF Invoice Generation:
**File**: `application/libraries/Pdf_lib.php`
- Gunakan **dompdf** (via Composer)
- Method: `generate_invoice($payment_id)` → return PDF binary
- Layout invoice: logo JBM, info bengkel, data customer, tabel items, total, tanda tangan digital

**PHASE 5 COMPLETE CHECKLIST**:
```
[ ] Payment_model with all methods
[ ] Auto-create payment when booking status → done
[ ] Payment view for customer with method selection
[ ] File upload for transfer proof (validation + rename)
[ ] Admin payment list with tab filter
[ ] Confirm/reject payment AJAX
[ ] Notifications sent on each payment event
[ ] PDF invoice generation with dompdf
[ ] Customer can download invoice PDF
[ ] Stock deducted when payment items created
```

---

## 📊 PHASE 6 — CUSTOMER HISTORY & DASHBOARD

### Customer Dashboard (`/my/dashboard`):
```
View: application/views/customer/dashboard.php
Content:
  - Greeting card: nama user + kendaraan
  - Stats: Total Booking, Booking Aktif, Total Pengeluaran
  - Last booking status card (quick view)
  - Upcoming booking reminder
  - Last 3 riwayat servis
```

### Transaction History (`/my/history`):
```
View: application/views/customer/history.php
Features:
  - Tabel semua transaksi (lunas) dengan kolom:
    Tanggal | Kendaraan | Layanan | Spare Parts | Total | Invoice
  - Filter: bulan, tahun, jenis layanan
  - Total pengeluaran period yang dipilih
  - Tombol download PDF per invoice
  - Tombol export CSV semua riwayat
```

### Service History (`/my/history` — tab Riwayat Servis):
```
Features:
  - Timeline servis per kendaraan
  - Setiap entry: tanggal, layanan, km saat itu (jika ada), catatan mekanik
  - Highlight jika sudah > 3 bulan dari servis terakhir (reminder)
  - Card "Servis Berikutnya Direkomendasikan" berdasarkan interval waktu
```

**PHASE 6 COMPLETE CHECKLIST**:
```
[ ] Customer dashboard with stats
[ ] Transaction history with filter
[ ] CSV export functionality
[ ] Service history per vehicle (timeline)
[ ] Reminder logic for overdue service
[ ] All download links working
```

---

## 🛠️ PHASE 7 — ADMIN DASHBOARD & BACK-OFFICE

### 7.1 — Admin Dashboard (`/admin`)

**View**: `application/views/admin/dashboard.php`
```
Top KPI Cards (data via AJAX):
  - Pendapatan Hari Ini
  - Total Booking Bulan Ini
  - Antrean Aktif Sekarang
  - Stok Item Kritis (< min_stock)

Charts (Chart.js):
  - Line chart: pendapatan 7 hari terakhir
  - Doughnut chart: distribusi jenis layanan bulan ini

Tables:
  - 5 booking terbaru (dengan status badge)
  - 5 pembayaran pending konfirmasi
```

### 7.2 — Queue Management (`/admin/queue`)

**View**: `application/views/admin/queue.php`
```
Layout: Kanban board 4 kolom
  Kolom 1: Menunggu Konfirmasi (status=pending)
  Kolom 2: Antrean (status=confirmed/queued)
  Kolom 3: Sedang Dikerjakan (status=in_progress)
  Kolom 4: Selesai Hari Ini (status=done, hari ini)

Setiap booking card menampilkan:
  - Nama customer + nomor HP
  - Jenis layanan + kendaraan
  - Jam booking + estimasi selesai
  - Tombol ubah status (dropdown → AJAX update)
  - Assign mekanik (select dropdown)
  - Input estimasi selesai (timepicker)

Auto-refresh queue setiap 60 detik via AJAX
```

### 7.3 — Stock Management (`/admin/stock`)

**View**: `application/views/admin/stock.php`
```
Features:
  - Tabel semua spare parts dengan kolom:
    Kode | Nama | Kategori | Stok | Min Stok | Harga Beli | Harga Jual | Status
  - Row merah jika stok < min_stock
  - Modal tambah/edit spare part (AJAX submit)
  - Tombol Restock: input jumlah masuk → update stok + insert stock_mutation
  - Filter: kategori, status stok
  - Search realtime (jQuery filter)
  - Export ke CSV
```

### 7.4 — Financial Reports (`/admin/reports`)

**View**: `application/views/admin/reports.php`
```
Tabs:
  Tab 1 — Laporan Harian:
    - Date picker pilih tanggal
    - Tabel transaksi hari itu: invoice, customer, layanan, metode, total
    - Summary: total cash, total transfer, grand total
    - Export PDF tombol

  Tab 2 — Laporan Bulanan:
    - Pilih bulan & tahun
    - Chart.js bar chart: pendapatan per minggu dalam bulan
    - Tabel rekap per hari
    - Perbandingan dengan bulan sebelumnya (% naik/turun)
    - Export Excel (CSV)

  Tab 3 — Laporan Tahunan:
    - Chart.js line chart: pendapatan per bulan
    - Tabel rekap per bulan
```

### 7.5 — User Management (`/admin/users`)

**View**: `application/views/admin/users.php`
```
Tabs: Customer | Mekanik | Admin/Kasir

Customer tab:
  - Tabel: nama, email, HP, total kendaraan, total booking, status aktif
  - Klik → modal detail: riwayat booking, kendaraan, total pengeluaran
  - Toggle aktif/nonaktif (AJAX)

Mekanik tab:
  - Tabel: nama, email, total servis diselesaikan, status
  - Tambah mekanik baru (modal form)
  - Edit data mekanik

Admin/Kasir tab:
  - Hanya visible untuk role=admin
  - Tambah akun operator dengan role pilihan
```

**PHASE 7 COMPLETE CHECKLIST**:
```
[ ] Admin dashboard with live KPI via AJAX
[ ] Chart.js line & doughnut charts
[ ] Kanban queue board with AJAX status update
[ ] Auto-refresh queue every 60 seconds
[ ] Mechanic assignment working
[ ] Stock table with low-stock highlighting
[ ] Add/edit/restock spare part via modal AJAX
[ ] Daily report with export PDF
[ ] Monthly report with Chart.js
[ ] Yearly report chart
[ ] User management all 3 tabs
[ ] Toggle user active/inactive
```

---

## 🔔 PHASE 8 — NOTIFICATIONS & GLOBAL FEATURES

### Notification Library (`application/libraries/Notification_lib.php`):
```php
class Notification_lib {
  // Send notification to user (insert to DB)
  public function send($id_user, $title, $message, $type='system', $url=null)
  
  // Send to all admin users
  public function send_to_admins($title, $message, $type='system', $url=null)
  
  // Send email notification (wrapper CI3 email)
  public function send_email($to, $subject, $body_html)
  
  // Trigger WhatsApp message via WA Gateway API (optional, configurable)
  public function send_whatsapp($phone, $message)
}
```

### SweetAlert2 Global Integration:
**File**: `assets/js/main.js`
```javascript
// Read CI flashdata from meta tags and show SweetAlert
// Global AJAX error handler (show SweetAlert on 500/403/401)
// CSRF token auto-inject on all $.ajax calls
// Loading overlay show/hide functions
// Confirm dialog wrapper: JBM.confirm(title, text, callback)
// Toast wrapper: JBM.toast(icon, title, message)
```

### Settings Page (`/admin/settings`):
```
Fields: 
  workshop_name, workshop_tagline, workshop_phone, workshop_whatsapp
  workshop_address, workshop_city, workshop_lat, workshop_lng
  workshop_email, workshop_hours_weekday, workshop_hours_saturday
  bank_name, bank_account, bank_holder
  max_booking_per_day, reminder_days_before_service
  smtp_host, smtp_user, smtp_pass, smtp_port (password field)
  
Save via AJAX, SweetAlert confirm before save
```

**PHASE 8 COMPLETE CHECKLIST**:
```
[ ] Notification_lib complete with all methods
[ ] Email sending working (test with real SMTP)
[ ] SweetAlert2 fully integrated globally
[ ] CSRF auto-inject on all AJAX
[ ] Global AJAX error handling
[ ] Settings page with all fields
[ ] Settings saved to DB correctly
```

---

## 🧪 PHASE 9 — SECURITY HARDENING & TESTING

**AGENT TASK**: Apply all security measures and generate test scenarios.

### Security Implementation Checklist:
```
[ ] All POST forms have CSRF token (CI3 auto + meta tag for AJAX)
[ ] All DB queries use CI3 Active Record (no raw string interpolation)
[ ] All user output uses htmlspecialchars() or CI3 xss_clean()
[ ] File uploads: whitelist ext check + MIME type check + rename + store outside webroot
[ ] Login rate limiting implemented (session-based counter)
[ ] Role check on EVERY admin and customer controller method
[ ] Error reporting OFF in production (check CI_ENV)
[ ] SQL errors never exposed to user
[ ] .htaccess denies direct access to application/ folder
[ ] Upload folder .htaccess denies PHP execution
[ ] Passwords always bcrypt, never logged or exposed
```

### Generate `.htaccess` files:
- Root `.htaccess`: remove index.php from URL, force HTTPS
- `assets/uploads/.htaccess`: deny PHP execution
- `application/.htaccess`: deny all direct access

### Generate Test Scenarios (`tests/test_scenarios.md`):
```
SCENARIO 1: Customer full journey
  1. Register new account
  2. Add vehicle
  3. Book service (fill all form steps)
  4. Admin confirms booking
  5. Admin updates status: queued → in_progress → done
  6. Customer sees real-time status updates
  7. Admin creates invoice with spare parts
  8. Customer chooses transfer, uploads proof
  9. Admin confirms payment
  10. Customer downloads PDF invoice
  
SCENARIO 2: Overbooking prevention
  - Fill all slots for a given date/time
  - Try to book same slot → should fail with "Slot penuh" error
  
SCENARIO 3: Stock depletion
  - Set spare part stock to 1
  - Use it in a service order
  - Check stock is now 0
  - Verify admin receives low-stock notification
  
SCENARIO 4: Access control
  - Try accessing /admin without login → redirect to /login
  - Login as customer, try accessing /admin → 403
  - Login as mekanik, try accessing /admin/reports → 403
  
SCENARIO 5: CSRF protection
  - Submit form without CSRF token → rejected with 403
```

---

## 🚀 PHASE 10 — DEPLOYMENT CONFIGURATION

**AGENT TASK**: Generate all production deployment files.

### File: `.env.example`
```
CI_ENV=production
DB_HOST=localhost
DB_USER=jbm_user
DB_PASS=strong_password_here
DB_NAME=jbm_bengkel
SMTP_HOST=smtp.gmail.com
SMTP_USER=email@gmail.com
SMTP_PASS=app_password_here
SMTP_PORT=587
APP_KEY=random_32_char_string_here
```

### File: `deployment/nginx.conf`
```nginx
# Complete Nginx config for JBM app
# - PHP-FPM socket
# - Gzip compression
# - Static asset caching (30 days)
# - PHP execution in uploads/ blocked
# - HTTPS redirect
# - Security headers: X-Frame-Options, X-Content-Type-Options, CSP
```

### File: `deployment/setup.sh`
```bash
#!/bin/bash
# Automated setup script:
# 1. Install PHP 7.4, Nginx, MySQL, Composer
# 2. Clone repo
# 3. Run composer install
# 4. Copy .env.example to .env
# 5. Import database schema
# 6. Set folder permissions (uploads: 755, logs: 755)
# 7. Setup cron jobs
```

### File: `deployment/crontab.txt`
```
# Daily DB backup at 02:00
0 2 * * * mysqldump -u$DB_USER -p$DB_PASS jbm_bengkel > /backups/jbm_$(date +\%Y\%m\%d).sql

# Delete backups older than 30 days
0 3 * * * find /backups -name "jbm_*.sql" -mtime +30 -delete

# Send service reminders to customers (weekly)
0 9 * * 1 php /var/www/jbm/index.php cli/send_reminders
```

### File: `README.md`
```markdown
# JBM Bengkel Mobil — Web Application

## Requirements
- PHP 7.4+
- MySQL 8.0+
- Composer
- Nginx/Apache

## Installation
1. Clone repo
2. `composer install`
3. Copy `.env.example` to `.env` and fill values
4. Import `database/jbm_schema.sql`
5. Set write permissions: `chmod -R 755 assets/uploads application/logs`
6. Configure web server (see deployment/nginx.conf)

## Default Login
- Admin: admin@jbm.id / admin123 (CHANGE IMMEDIATELY)

## Tech Stack
CodeIgniter 3 · Bootstrap 5 · MySQL · AJAX · SweetAlert2
```

**PHASE 10 COMPLETE CHECKLIST**:
```
[ ] .env.example with all required variables
[ ] Nginx config complete with security headers
[ ] setup.sh automated installer
[ ] Crontab configured
[ ] README.md complete
[ ] All folder permissions documented
[ ] Default credentials documented with change warning
```

---

## ✅ FINAL DELIVERY CHECKLIST

```
PHASE 0  [ ] Schema + Config + Base Controller + Layouts
PHASE 1  [ ] Landing Page (7 sections, responsive)
PHASE 2  [ ] Auth (login/register/forgot/reset + rate limit)
PHASE 3  [ ] Booking System (wizard form + slot validation)
PHASE 4  [ ] Real-time Status (AJAX polling + notifications)
PHASE 5  [ ] Payment (cash/transfer + upload + PDF invoice)
PHASE 6  [ ] Customer History & Dashboard
PHASE 7  [ ] Admin Dashboard + Queue + Stock + Reports + Users
PHASE 8  [ ] Notification Library + SweetAlert2 + Settings
PHASE 9  [ ] Security Hardening + Test Scenarios
PHASE 10 [ ] Deployment Config (Nginx + scripts + README)

QUALITY GATES:
  [ ] Zero raw SQL string concatenation anywhere
  [ ] All controllers have role check
  [ ] All AJAX endpoints return consistent JSON format
  [ ] All file uploads validated server-side
  [ ] Mobile responsive confirmed at 375px, 768px, 1280px
  [ ] SweetAlert2 used for all user-facing confirmations/alerts
  [ ] PDF invoice downloadable and readable
  [ ] Real-time status polling working end-to-end
```

---

> **AGENT NOTE**: Begin execution with PHASE 0. Generate each file completely before moving to the next. 
> If dependencies between phases exist, note them explicitly and resolve before proceeding.
> Target: a developer should be able to clone the output repo, run `setup.sh`, and have a working production app.
