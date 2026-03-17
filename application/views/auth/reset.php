<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Reset Password — <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></title>
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
        <div class="d-flex flex-column flex-center flex-column-fluid">
            <div class="d-flex flex-column flex-center text-center p-10">
                <div class="card card-flush w-md-550px py-5">
                    <div class="card-body py-15 py-lg-20">
                        <div class="mb-7">
                            <div class="mx-auto d-flex align-items-center justify-content-center bg-light-primary rounded-circle" style="width:80px;height:80px;">
                                <i class="fa-solid fa-lock text-primary" style="font-size:32px;"></i>
                            </div>
                        </div>

                        <h2 class="fw-bolder text-gray-900 mb-3">Reset Password</h2>
                        <div class="text-muted fw-semibold fs-6 mb-8">Buat password baru yang kuat untuk akun Anda.</div>

                        <?php if (isset($token_invalid) && $token_invalid): ?>
                            <!-- Invalid / expired token -->
                            <div class="alert alert-danger d-flex align-items-center p-5 mb-6">
                                <i class="fa-solid fa-circle-xmark fs-2 text-danger me-4"></i>
                                <div class="text-gray-700">
                                    Link reset password tidak valid atau sudah kadaluarsa. Silakan minta link baru.
                                </div>
                            </div>
                            <a href="<?= base_url('forgot-password') ?>" class="btn btn-primary">Minta Link Baru</a>

                        <?php else: ?>
                            <form id="reset_form" novalidate>
                                <input type="hidden" name="token" id="reset_token" value="<?= htmlspecialchars($token ?? '') ?>" />

                                <div class="fv-row mb-7">
                                    <label class="form-label fw-semibold text-gray-900 fs-6 mb-2 required">Password Baru</label>
                                    <input type="password" name="password" id="new_password"
                                        class="form-control bg-transparent"
                                        placeholder="Min. 8 karakter" required minlength="8" />
                                </div>

                                <!-- Strength indicator -->
                                <div class="mb-7">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fs-8 text-muted">Kekuatan Password</span>
                                        <span class="fs-8" id="strength_label">-</span>
                                    </div>
                                    <div class="progress" style="height:4px;">
                                        <div class="progress-bar" id="strength_bar" style="width:0%;transition:width 0.3s;"></div>
                                    </div>
                                </div>

                                <div class="fv-row mb-8">
                                    <label class="form-label fw-semibold text-gray-900 fs-6 mb-2 required">Konfirmasi Password Baru</label>
                                    <input type="password" name="password_confirm" id="confirm_password"
                                        class="form-control bg-transparent"
                                        placeholder="Ulangi password baru" required />
                                </div>

                                <div class="d-grid mb-6">
                                    <button type="submit" id="reset_btn" class="btn btn-primary">
                                        <span class="indicator-label">Simpan Password Baru</span>
                                        <span class="indicator-progress d-none">
                                            <span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...
                                        </span>
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('login') ?>" class="link-primary fw-semibold fs-6">
                                <i class="fa-solid fa-arrow-left me-2 fs-7"></i>Kembali ke Login
                            </a>
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

        document.getElementById('new_password') && document.getElementById('new_password').addEventListener('input', function() {
            var val = this.value;
            var score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            var bar = document.getElementById('strength_bar');
            var label = document.getElementById('strength_label');
            var classes = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
            var labels = ['Lemah', 'Cukup', 'Baik', 'Kuat'];
            var widths = ['25%', '50%', '75%', '100%'];

            bar.className = 'progress-bar ' + (classes[score - 1] || 'bg-danger');
            bar.style.width = score > 0 ? widths[score - 1] : '0%';
            label.textContent = score > 0 ? labels[score - 1] : '-';
            label.className = 'fs-8 ' + (score >= 3 ? 'text-success' : score >= 2 ? 'text-warning' : 'text-danger');
        });

        var resetForm = document.getElementById('reset_form');
        if (resetForm) {
            resetForm.addEventListener('submit', function(e) {
                e.preventDefault();

                var password = document.getElementById('new_password').value;
                var confirm = document.getElementById('confirm_password').value;

                if (password !== confirm) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Password tidak cocok',
                        text: 'Pastikan kedua password sama.'
                    });
                    return;
                }

                var token = document.getElementById('reset_token').value;
                var btn = document.getElementById('reset_btn');
                var label = btn.querySelector('.indicator-label');
                var prog = btn.querySelector('.indicator-progress');
                btn.disabled = true;
                label.classList.add('d-none');
                prog.classList.remove('d-none');

                $.ajax({
                    url: BASE_URL + 'reset-password/' + token,
                    method: 'POST',
                    data: {
                        password: password,
                        password_confirm: confirm,
                        token: token,
                        [CSRF_TOKEN_NAME]: CSRF_HASH
                    },
                    headers: {
                        'X-CSRF-Token': CSRF_HASH
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Password Berhasil Diubah!',
                                text: 'Silakan login dengan password baru Anda.',
                                timer: 2500,
                                showConfirmButton: false
                            }).then(function() {
                                window.location.href = BASE_URL + 'login';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
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
        }
    </script>
</body>

</html>