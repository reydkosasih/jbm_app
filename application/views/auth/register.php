<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Daftar Akun — <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></title>
    <meta name="csrf-token" content="<?= $this->security->get_csrf_hash() ?>" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="<?= base_url('theme/assets/plugins/global/plugins.bundle.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('theme/assets/css/style.bundle.css') ?>" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
</head>

<body id="kt_body" class="app-blank">
    <script>
        var t = localStorage.getItem("data-bs-theme") || "light";
        document.documentElement.setAttribute("data-bs-theme", t);
    </script>
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <!-- Left: Form -->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-lg-500px p-10">
                        <div class="mb-8">
                            <a href="<?= base_url() ?>" class="text-muted fs-7 text-decoration-none">
                                <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Beranda
                            </a>
                        </div>

                        <form id="register_form" novalidate>
                            <div class="text-center mb-11">
                                <h1 class="text-gray-900 fw-bolder mb-3">Buat Akun Baru</h1>
                                <div class="text-gray-500 fw-semibold fs-6">Bergabung dengan <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel') ?></div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="form-label fw-semibold text-gray-900 fs-6 mb-2 required">Nama Lengkap</label>
                                <input type="text" name="name" id="reg_name" class="form-control bg-transparent"
                                    placeholder="Nama Anda" required minlength="3" maxlength="100" />
                            </div>

                            <div class="fv-row mb-7">
                                <label class="form-label fw-semibold text-gray-900 fs-6 mb-2 required">Email</label>
                                <input type="email" name="email" id="reg_email" class="form-control bg-transparent"
                                    placeholder="email@contoh.com" required />
                            </div>

                            <div class="fv-row mb-7">
                                <label class="form-label fw-semibold text-gray-900 fs-6 mb-2">No. HP / WhatsApp</label>
                                <input type="tel" name="phone" id="reg_phone" class="form-control bg-transparent"
                                    placeholder="08xx xxxx xxxx" maxlength="20" />
                            </div>

                            <div class="fv-row mb-7">
                                <label class="form-label fw-semibold text-gray-900 fs-6 mb-2 required">Password</label>
                                <input type="password" name="password" id="reg_password" class="form-control bg-transparent"
                                    placeholder="Min. 8 karakter" required minlength="8" />
                            </div>

                            <!-- Password strength indicator -->
                            <div class="mb-7">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-8 text-muted">Kekuatan Password</span>
                                    <span class="fs-8" id="password_strength_label">-</span>
                                </div>
                                <div class="progress" style="height:4px;">
                                    <div class="progress-bar" id="password_strength_bar" style="width:0%;transition:width 0.3s;"></div>
                                </div>
                            </div>

                            <div class="fv-row mb-7">
                                <label class="form-label fw-semibold text-gray-900 fs-6 mb-2 required">Konfirmasi Password</label>
                                <input type="password" name="password_confirm" id="reg_confirm" class="form-control bg-transparent"
                                    placeholder="Ulangi password" required />
                            </div>

                            <div class="d-grid mb-8">
                                <button type="submit" id="register_btn" class="btn btn-primary">
                                    <span class="indicator-label">Daftar Sekarang</span>
                                    <span class="indicator-progress d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Memproses...
                                    </span>
                                </button>
                            </div>

                            <div class="text-gray-500 text-center fw-semibold fs-6">
                                Sudah punya akun?
                                <a href="<?= base_url('login') ?>" class="link-primary">Masuk</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Visual -->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                style="background: linear-gradient(135deg, #0f3460 0%, #16213e 50%, #1a1a2e 100%);">
                <div class="d-flex flex-column flex-center py-15 px-5 px-md-15 w-100">
                    <div class="text-center">
                        <i class="fa-solid fa-wrench text-white mb-8" style="font-size:80px;opacity:0.2;"></i>
                        <h2 class="text-white fw-bolder fs-2hx mb-5">Servis Mudah, Cepat &amp; Terpercaya</h2>
                        <p class="text-white opacity-75 fs-5 mb-8">Daftarkan akun dan nikmati kemudahan booking servis kendaraan Anda secara online 24/7.</p>
                        <div class="d-flex flex-column gap-4 text-start mx-auto" style="max-width:300px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-15 rounded-circle" style="width:36px;height:36px;flex-shrink:0;">
                                    <i class="fa-solid fa-calendar-check text-white fs-6"></i>
                                </div>
                                <span class="text-white opacity-75 fs-6">Booking online kapan saja</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-15 rounded-circle" style="width:36px;height:36px;flex-shrink:0;">
                                    <i class="fa-solid fa-bell text-white fs-6"></i>
                                </div>
                                <span class="text-white opacity-75 fs-6">Notifikasi real-time status servis</span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-15 rounded-circle" style="width:36px;height:36px;flex-shrink:0;">
                                    <i class="fa-solid fa-file-invoice text-white fs-6"></i>
                                </div>
                                <span class="text-white opacity-75 fs-6">Invoice &amp; riwayat servis lengkap</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('theme/assets/plugins/global/plugins.bundle.js') ?>"></script>
    <script src="<?= base_url('theme/assets/js/scripts.bundle.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <script>
        var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
        var BASE_URL = '<?= base_url() ?>';

        // Password strength checker
        document.getElementById('reg_password').addEventListener('input', function() {
            var val = this.value;
            var score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            var bar = document.getElementById('password_strength_bar');
            var label = document.getElementById('password_strength_label');
            var classes = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
            var labels = ['Lemah', 'Cukup', 'Baik', 'Kuat'];
            var widths = ['25%', '50%', '75%', '100%'];

            bar.className = 'progress-bar ' + (classes[score - 1] || 'bg-danger');
            bar.style.width = score > 0 ? widths[score - 1] : '0%';
            label.textContent = score > 0 ? labels[score - 1] : '-';
            label.className = 'fs-8 ' + (score >= 3 ? 'text-success' : score >= 2 ? 'text-warning' : 'text-danger');
        });

        // Register form
        document.getElementById('register_form').addEventListener('submit', function(e) {
            e.preventDefault();

            var password = document.getElementById('reg_password').value;
            var confirm = document.getElementById('reg_confirm').value;

            if (password !== confirm) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Password tidak cocok',
                    text: 'Pastikan password dan konfirmasi password sama.'
                });
                return;
            }

            var btn = document.getElementById('register_btn');
            var label = btn.querySelector('.indicator-label');
            var prog = btn.querySelector('.indicator-progress');
            btn.disabled = true;
            label.classList.add('d-none');
            prog.classList.remove('d-none');

            $.ajax({
                url: BASE_URL + 'register',
                method: 'POST',
                data: {
                    name: $('#reg_name').val().trim(),
                    email: $('#reg_email').val().trim(),
                    phone: $('#reg_phone').val().trim(),
                    password: password,
                    password_confirm: confirm,
                    [CSRF_TOKEN_NAME]: CSRF_HASH
                },
                headers: {
                    'X-CSRF-Token': CSRF_HASH
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Akun Berhasil Dibuat!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = res.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Pendaftaran Gagal',
                            text: res.message
                        });
                        btn.disabled = false;
                        label.classList.remove('d-none');
                        prog.classList.add('d-none');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                    btn.disabled = false;
                    label.classList.remove('d-none');
                    prog.classList.add('d-none');
                }
            });
        });
    </script>
</body>

</html>