<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk — <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></title>
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
            <!-- Left panel -->
            <div class="d-flex flex-column flex-lg-row-fluid w-lg-50 p-10 order-2 order-lg-1">
                <div class="d-flex flex-center flex-column flex-lg-row-fluid">
                    <div class="w-lg-500px p-10">
                        <!-- Back to home -->
                        <div class="mb-8">
                            <a href="<?= base_url() ?>" class="text-muted fs-7 text-decoration-none">
                                <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Beranda
                            </a>
                        </div>

                        <form id="login_form" novalidate>
                            <div class="text-center mb-11">
                                <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle" style="width:52px;height:52px;">
                                        <i class="fa-solid fa-wrench text-white fs-3"></i>
                                    </div>
                                </div>
                                <h1 class="text-gray-900 fw-bolder mb-3">Masuk ke Akun Anda</h1>
                                <div class="text-gray-500 fw-semibold fs-6">
                                    <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?>
                                </div>
                            </div>

                            <div class="fv-row mb-8">
                                <label class="form-label fw-semibold text-gray-900 fs-6 mb-2">Email</label>
                                <input type="email" name="email" id="email" class="form-control bg-transparent"
                                    placeholder="email@contoh.com" required autocomplete="email" />
                            </div>

                            <div class="fv-row mb-3">
                                <label class="form-label fw-semibold text-gray-900 fs-6 mb-2">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control bg-transparent"
                                        placeholder="Min. 8 karakter" required />
                                    <button type="button" class="btn btn-outline-secondary" id="toggle_password">
                                        <i class="fa-solid fa-eye fs-6" id="eye_icon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
                                <div></div>
                                <a href="<?= base_url('forgot-password') ?>" class="link-primary">Lupa Password?</a>
                            </div>

                            <div class="d-grid mb-10">
                                <button type="submit" id="login_btn" class="btn btn-primary">
                                    <span class="indicator-label">Masuk</span>
                                    <span class="indicator-progress d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Memproses...
                                    </span>
                                </button>
                            </div>

                            <div class="text-gray-500 text-center fw-semibold fs-6">
                                Belum punya akun?
                                <a href="<?= base_url('register') ?>" class="link-primary">Daftar Sekarang</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right panel -->
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
                <div class="d-flex flex-column flex-center py-15 px-5 px-md-15 w-100">
                    <div class="text-center">
                        <div class="mb-8">
                            <i class="fa-solid fa-car-side text-white" style="font-size:100px;opacity:0.2;"></i>
                        </div>
                        <h2 class="text-white fw-bolder fs-2hx mb-5">Selamat Datang Kembali!</h2>
                        <p class="text-white opacity-75 fs-5 mb-8">Kelola booking servis Anda dengan mudah dan pantau status kendaraan secara real-time.</p>
                        <div class="d-flex justify-content-center gap-6 flex-wrap">
                            <div class="text-center">
                                <div class="text-white fw-bold fs-2">Cepat</div>
                                <div class="text-white opacity-50 fs-8">Booking</div>
                            </div>
                            <div class="text-white opacity-25 d-flex align-items-center">|</div>
                            <div class="text-center">
                                <div class="text-white fw-bold fs-2">Real-time</div>
                                <div class="text-white opacity-50 fs-8">Status</div>
                            </div>
                            <div class="text-white opacity-25 d-flex align-items-center">|</div>
                            <div class="text-center">
                                <div class="text-white fw-bold fs-2">Aman</div>
                                <div class="text-white opacity-50 fs-8">Transaksi</div>
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

        // Toggle password visibility
        document.getElementById('toggle_password').addEventListener('click', function() {
            var pw = document.getElementById('password');
            var icon = document.getElementById('eye_icon');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.className = 'fa-solid fa-eye-slash fs-6';
            } else {
                pw.type = 'password';
                icon.className = 'fa-solid fa-eye fs-6';
            }
        });

        // Login form submit
        document.getElementById('login_form').addEventListener('submit', function(e) {
            e.preventDefault();
            var btn = document.getElementById('login_btn');
            var label = btn.querySelector('.indicator-label');
            var prog = btn.querySelector('.indicator-progress');

            // Disable btn & show spinner
            btn.disabled = true;
            label.classList.add('d-none');
            prog.classList.remove('d-none');

            $.ajax({
                url: BASE_URL + 'login',
                method: 'POST',
                data: {
                    email: $('#email').val().trim(),
                    password: $('#password').val(),
                    [CSRF_TOKEN_NAME]: CSRF_HASH
                },
                headers: {
                    'X-CSRF-Token': CSRF_HASH
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil masuk!',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = res.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Masuk',
                            text: res.message
                        });
                        btn.disabled = false;
                        label.classList.remove('d-none');
                        prog.classList.add('d-none');
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan. Coba lagi.';
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