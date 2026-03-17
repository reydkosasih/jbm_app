<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lupa Password — <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></title>
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
                        <!-- Logo / icon -->
                        <div class="mb-7">
                            <div class="mx-auto d-flex align-items-center justify-content-center bg-light-warning rounded-circle" style="width:80px;height:80px;">
                                <i class="fa-solid fa-key text-warning" style="font-size:32px;"></i>
                            </div>
                        </div>

                        <h2 class="fw-bolder text-gray-900 mb-3">Lupa Password?</h2>
                        <div class="text-muted fw-semibold fs-6 mb-8">
                            Masukkan email Anda dan kami akan mengirimkan<br />link untuk mereset password.
                        </div>

                        <form id="forgot_form" novalidate>
                            <div class="fv-row mb-8">
                                <input type="email" name="email" id="forgot_email"
                                    class="form-control bg-transparent text-center"
                                    placeholder="email@contoh.com" required />
                            </div>

                            <div class="d-grid mb-6">
                                <button type="submit" id="forgot_btn" class="btn btn-warning">
                                    <span class="indicator-label">Kirim Link Reset</span>
                                    <span class="indicator-progress d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>Mengirim...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <!-- Success state (hidden) -->
                        <div id="success_state" class="d-none">
                            <div class="mb-6">
                                <div class="mx-auto d-flex align-items-center justify-content-center bg-light-success rounded-circle" style="width:80px;height:80px;">
                                    <i class="fa-solid fa-envelope-circle-check text-success" style="font-size:32px;"></i>
                                </div>
                            </div>
                            <h3 class="fw-bolder text-gray-900 mb-3">Email Terkirim!</h3>
                            <p class="text-muted fs-6 mb-6" id="success_msg">
                                Link reset password telah dikirim ke email Anda. Silakan periksa inbox (dan folder spam).
                            </p>
                            <p class="fs-7 text-muted">Link berlaku selama <strong>1 jam</strong>.</p>
                        </div>

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

        document.getElementById('forgot_form').addEventListener('submit', function(e) {
            e.preventDefault();

            var email = document.getElementById('forgot_email').value.trim();
            if (!email) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Email diperlukan',
                    text: 'Masukkan alamat email Anda.'
                });
                return;
            }

            var btn = document.getElementById('forgot_btn');
            var label = btn.querySelector('.indicator-label');
            var prog = btn.querySelector('.indicator-progress');
            btn.disabled = true;
            label.classList.add('d-none');
            prog.classList.remove('d-none');

            $.ajax({
                url: BASE_URL + 'forgot-password',
                method: 'POST',
                data: {
                    email: email,
                    [CSRF_TOKEN_NAME]: CSRF_HASH
                },
                headers: {
                    'X-CSRF-Token': CSRF_HASH
                },
                success: function(res) {
                    if (res.success) {
                        document.getElementById('forgot_form').classList.add('d-none');
                        document.getElementById('success_state').classList.remove('d-none');
                        if (res.message) document.getElementById('success_msg').textContent = res.message;
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
    </script>
</body>

</html>