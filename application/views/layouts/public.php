<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' : '' ?><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></title>
    <meta name="description" content="<?= htmlspecialchars($settings['workshop_tagline'] ?? 'Servis Terpercaya, Kualitas Terjamin') ?>" />

    <!-- CSRF meta tag for AJAX -->
    <meta name="csrf-token" content="<?= $this->security->get_csrf_hash() ?>" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Metronic Theme -->
    <link href="<?= base_url('theme/assets/plugins/global/plugins.bundle.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('theme/assets/css/style.bundle.css') ?>" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

    <style>
        .landing-header {
            transition: background 0.3s ease;
        }

        .landing-hero {
            min-height: 600px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }

        .service-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .counter-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #009ef7;
        }

        .testimonial-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .gallery-item {
            overflow: hidden;
            border-radius: 12px;
            cursor: pointer;
        }

        .gallery-item img {
            transition: transform 0.4s ease;
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        footer a {
            transition: color 0.2s;
        }

        footer a:hover {
            color: #009ef7 !important;
        }
    </style>
</head>

<body id="kt_body" data-bs-spy="scroll" data-bs-target="#kt_landing_menu" class="bg-body app-blank">
    <!-- Theme mode setup -->
    <script>
        var defaultThemeMode = "light";
        var themeMode;
        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                themeMode = localStorage.getItem("data-bs-theme") !== null ? localStorage.getItem("data-bs-theme") : defaultThemeMode;
            }
            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }
            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <!-- NAVBAR -->
        <div class="landing-header" data-kt-sticky="true" data-kt-sticky-name="landing-header" data-kt-sticky-offset="{default: '200px', lg: '300px'}">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between py-3">
                    <!-- Logo -->
                    <div class="d-flex align-items-center flex-equal">
                        <button class="btn btn-icon btn-active-color-primary me-3 d-flex d-lg-none" id="kt_landing_menu_toggle">
                            <i class="ki-outline ki-abstract-14 fs-2hx"></i>
                        </button>
                        <a href="<?= base_url() ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                            <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle" style="width:40px;height:40px;">
                                <i class="fa-solid fa-wrench text-white fs-4"></i>
                            </div>
                            <div>
                                <span class="fw-bold fs-4 text-gray-900 d-block lh-1"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel') ?></span>
                                <span class="text-muted fs-8">Auto Repair</span>
                            </div>
                        </a>
                    </div>

                    <!-- Nav menu -->
                    <div class="d-lg-block" id="kt_header_nav_wrapper">
                        <div class="d-lg-block p-5 p-lg-0"
                            data-kt-drawer="true" data-kt-drawer-name="landing-menu"
                            data-kt-drawer-activate="{default: true, lg: false}"
                            data-kt-drawer-overlay="true" data-kt-drawer-width="200px"
                            data-kt-drawer-direction="start"
                            data-kt-drawer-toggle="#kt_landing_menu_toggle"
                            data-kt-swapper="true" data-kt-swapper-mode="prepend"
                            data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav_wrapper'}">
                            <div class="menu menu-column flex-nowrap menu-rounded menu-lg-row menu-title-gray-600 menu-state-title-primary nav nav-flush fs-5 fw-semibold" id="kt_landing_menu">
                                <div class="menu-item">
                                    <a class="menu-link nav-link py-3 px-4 px-xxl-6 <?= (uri_string() === '') ? 'active' : '' ?>" href="<?= base_url() ?>" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Beranda</a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#services" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Layanan</a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#gallery" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Galeri</a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#testimonials" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Testimoni</a>
                                </div>
                                <div class="menu-item">
                                    <a class="menu-link nav-link py-3 px-4 px-xxl-6" href="#location" data-kt-scroll-toggle="true" data-kt-drawer-dismiss="true">Lokasi</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auth buttons -->
                    <div class="flex-equal text-end ms-1 d-flex gap-2 justify-content-end">
                        <?php if ($this->session->userdata('user_id')): ?>
                            <?php $role = $this->session->userdata('user_role'); ?>
                            <a href="<?= base_url($role === 'customer' ? 'my/dashboard' : 'admin') ?>" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-gauge me-1"></i> Dashboard
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('login') ?>" class="btn btn-light btn-active-primary btn-sm">Masuk</a>
                            <a href="<?= base_url('register') ?>" class="btn btn-primary btn-sm">Daftar</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- END NAVBAR -->

        <!-- MAIN CONTENT -->
        <div id="kt_app_content">
            <?= $content ?>
        </div>
        <!-- END MAIN CONTENT -->

        <!-- FOOTER -->
        <footer class="bg-dark text-white pt-15 pb-5 mt-0">
            <div class="container">
                <div class="row gy-8">
                    <div class="col-lg-4">
                        <div class="d-flex align-items-center gap-3 mb-5">
                            <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle" style="width:50px;height:50px;">
                                <i class="fa-solid fa-wrench text-white fs-3"></i>
                            </div>
                            <div>
                                <div class="fw-bold fs-3 text-white"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></div>
                                <div class="text-muted fs-7"><?= htmlspecialchars($settings['workshop_tagline'] ?? '') ?></div>
                            </div>
                        </div>
                        <p class="text-gray-400 fs-6 mb-6"><?= htmlspecialchars($settings['workshop_address'] ?? '') ?></p>
                        <div class="d-flex gap-3">
                            <a href="https://wa.me/<?= htmlspecialchars($settings['workshop_wa'] ?? '') ?>" class="btn btn-sm btn-success" target="_blank">
                                <i class="fa-brands fa-whatsapp me-1"></i> WhatsApp
                            </a>
                            <a href="tel:<?= htmlspecialchars($settings['workshop_phone'] ?? '') ?>" class="btn btn-sm btn-outline btn-outline-light text-white">
                                <i class="fa-solid fa-phone me-1"></i> Telepon
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-6">
                        <h5 class="fw-bold text-white mb-5">Menu</h5>
                        <ul class="list-unstyled text-gray-400 fs-6">
                            <li class="mb-3"><a href="<?= base_url() ?>" class="text-gray-400 text-decoration-none">Beranda</a></li>
                            <li class="mb-3"><a href="#services" class="text-gray-400 text-decoration-none">Layanan</a></li>
                            <li class="mb-3"><a href="#gallery" class="text-gray-400 text-decoration-none">Galeri</a></li>
                            <li class="mb-3"><a href="#testimonials" class="text-gray-400 text-decoration-none">Testimoni</a></li>
                            <li class="mb-3"><a href="#location" class="text-gray-400 text-decoration-none">Lokasi</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-6">
                        <h5 class="fw-bold text-white mb-5">Layanan</h5>
                        <ul class="list-unstyled text-gray-400 fs-6">
                            <li class="mb-3">Ganti Oli</li>
                            <li class="mb-3">Tune-Up Mesin</li>
                            <li class="mb-3">Service AC</li>
                            <li class="mb-3">Spooring &amp; Balancing</li>
                            <li class="mb-3">Ganti Ban</li>
                        </ul>
                    </div>

                    <div class="col-lg-3">
                        <h5 class="fw-bold text-white mb-5">Jam Operasional</h5>
                        <p class="text-gray-400 fs-6"><?= htmlspecialchars($settings['operating_hours'] ?? 'Senin - Sabtu: 08.00 - 17.00 WIB') ?></p>
                        <h5 class="fw-bold text-white mb-5 mt-5">Kontak</h5>
                        <ul class="list-unstyled text-gray-400 fs-6">
                            <li class="mb-2"><i class="fa-solid fa-phone me-2 text-primary"></i><?= htmlspecialchars($settings['workshop_phone'] ?? '') ?></li>
                            <li class="mb-2"><i class="fa-solid fa-envelope me-2 text-primary"></i><?= htmlspecialchars($settings['workshop_email'] ?? '') ?></li>
                        </ul>
                    </div>
                </div>

                <div class="separator separator-dashed border-secondary my-8"></div>
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <span class="text-gray-400 fs-7">&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?>. All rights reserved.</span>
                    <span class="text-gray-500 fs-8">Powered by CodeIgniter 3 &amp; Metronic</span>
                </div>
            </div>
        </footer>
        <!-- END FOOTER -->
    </div>

    <!-- JavaScript -->
    <script src="<?= base_url('theme/assets/plugins/global/plugins.bundle.js') ?>"></script>
    <script src="<?= base_url('theme/assets/js/scripts.bundle.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <!-- Flash notifications -->
    <?php
    $swal_type    = $this->session->flashdata('swal_type');
    $swal_message = $this->session->flashdata('swal_message');
    if ($swal_type && $swal_message):
    ?>
        <script>
            Swal.fire({
                icon: '<?= addslashes($swal_type) ?>',
                title: '<?= addslashes($swal_message) ?>',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true
            });
        </script>
    <?php endif; ?>

    <!-- CSRF token for AJAX -->
    <script>
        var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
        var BASE_URL = '<?= base_url() ?>';
        // Axios / jQuery global CSRF setup
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type)) {
                    xhr.setRequestHeader('X-CSRF-Token', CSRF_HASH);
                }
            }
        });
    </script>

    <?php if (isset($extra_js)): ?>
        <?= $extra_js ?>
    <?php endif; ?>
</body>

</html>