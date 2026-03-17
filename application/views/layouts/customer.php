<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' : '' ?><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></title>

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
        /* Customer Sidebar - Bottom nav mobile, Left sidebar desktop */
        .customer-sidebar {
            width: 260px;
            min-height: 100vh;
            background: #fff;
            border-right: 1px solid #f1f1f4;
        }

        .sidebar-nav-item {
            border-radius: 8px;
            margin: 2px 0;
            transition: background 0.2s;
        }

        .sidebar-nav-item:hover,
        .sidebar-nav-item.active {
            background: #f1f9ff;
        }

        .sidebar-nav-item.active a {
            color: #009ef7 !important;
            font-weight: 600;
        }

        .sidebar-nav-item a {
            color: #5e6278;
            text-decoration: none;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 8px;
        }

        /* Mobile bottom nav */
        @media (max-width: 991.98px) {
            .customer-sidebar {
                display: none !important;
            }

            .mobile-bottom-nav {
                display: flex !important;
            }

            .main-content-area {
                padding-bottom: 80px;
            }
        }

        @media (min-width: 992px) {
            .mobile-bottom-nav {
                display: none !important;
            }
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: #fff;
            border-top: 1px solid #f1f1f4;
            padding: 8px 0 12px;
        }

        .mobile-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            padding: 4px;
            text-decoration: none;
            color: #a1a5b7;
            font-size: 11px;
            transition: color 0.2s;
        }

        .mobile-nav-item.active,
        .mobile-nav-item:hover {
            color: #009ef7;
        }

        .mobile-nav-item i {
            font-size: 18px;
        }
    </style>
</head>

<body id="kt_app_body" data-kt-app-sidebar-enabled="true" class="app-default">
    <!-- Theme mode setup -->
    <script>
        var defaultThemeMode = "light";
        var themeMode = localStorage.getItem("data-bs-theme") || defaultThemeMode;
        if (themeMode === "system") themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    </script>

    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">

            <!-- TOP NAVBAR -->
            <div id="kt_app_header" class="app-header d-flex flex-stack px-6 py-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url('my/dashboard') ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                        <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle" style="width:36px;height:36px;">
                            <i class="fa-solid fa-wrench text-white fs-5"></i>
                        </div>
                        <span class="fw-bold fs-5 text-gray-900 d-none d-lg-block"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel') ?></span>
                    </a>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Notification Bell -->
                    <div class="position-relative">
                        <button class="btn btn-icon btn-light btn-sm position-relative" id="notif_bell_btn" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-9 d-none" id="notif_badge">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:340px;max-height:400px;overflow-y:auto;" id="notif_dropdown">
                            <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                                <span class="fw-bold fs-6">Notifikasi</span>
                                <a href="#" class="text-muted fs-8" id="mark_all_read">Tandai semua dibaca</a>
                            </div>
                            <div id="notif_list">
                                <div class="text-center py-8 text-muted fs-7">
                                    <i class="fa-solid fa-bell-slash fs-2 d-block mb-3"></i>
                                    Tidak ada notifikasi baru
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="symbol symbol-30px symbol-circle">
                                <?php $avatar = $current_user['avatar'] ?? null; ?>
                                <?php if ($avatar): ?>
                                    <img src="<?= base_url('uploads/avatars/' . $avatar) ?>" alt="Avatar" class="rounded-circle" style="width:30px;height:30px;object-fit:cover;" />
                                <?php else: ?>
                                    <div class="symbol-label bg-primary text-white fs-8 fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width:30px;height:30px;">
                                        <?= strtoupper(substr($current_user['name'] ?? 'U', 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <span class="fw-semibold text-gray-800 d-none d-md-block"><?= htmlspecialchars($current_user['name'] ?? '') ?></span>
                            <i class="fa-solid fa-chevron-down fs-9 text-muted"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= base_url('my/profile') ?>"><i class="fa-solid fa-user me-2 text-primary"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="<?= base_url('my/booking') ?>"><i class="fa-solid fa-calendar-plus me-2 text-success"></i>Booking Baru</a></li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- END TOP NAVBAR -->

            <div class="app-wrapper d-flex" id="kt_app_wrapper">
                <!-- LEFT SIDEBAR (desktop only) -->
                <div class="customer-sidebar d-none d-lg-flex flex-column p-4">
                    <nav class="flex-grow-1">
                        <ul class="list-unstyled mb-0">
                            <li class="sidebar-nav-item <?= (uri_string() === 'my/dashboard') ? 'active' : '' ?>">
                                <a href="<?= base_url('my/dashboard') ?>">
                                    <i class="fa-solid fa-gauge-high fs-5 text-primary-emphasis"></i>
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li class="sidebar-nav-item <?= (strpos(uri_string(), 'my/booking') !== false) ? 'active' : '' ?>">
                                <a href="<?= base_url('my/booking') ?>">
                                    <i class="fa-solid fa-calendar-plus fs-5 text-success-emphasis"></i>
                                    <span>Booking Baru</span>
                                </a>
                            </li>
                            <li class="sidebar-nav-item <?= (uri_string() === 'my/status') ? 'active' : '' ?>">
                                <a href="<?= base_url('my/status') ?>">
                                    <i class="fa-solid fa-hourglass-half fs-5 text-warning-emphasis"></i>
                                    <span>Status Servis</span>
                                </a>
                            </li>
                            <li class="sidebar-nav-item <?= (uri_string() === 'my/history') ? 'active' : '' ?>">
                                <a href="<?= base_url('my/history') ?>">
                                    <i class="fa-solid fa-clock-rotate-left fs-5 text-info-emphasis"></i>
                                    <span>Riwayat</span>
                                </a>
                            </li>
                            <li class="sidebar-nav-item <?= (uri_string() === 'my/vehicles') ? 'active' : '' ?>">
                                <a href="<?= base_url('my/vehicles') ?>">
                                    <i class="fa-solid fa-car fs-5 text-secondary-emphasis"></i>
                                    <span>Kendaraan Saya</span>
                                </a>
                            </li>
                            <li class="sidebar-nav-item <?= (uri_string() === 'my/profile') ? 'active' : '' ?>">
                                <a href="<?= base_url('my/profile') ?>">
                                    <i class="fa-solid fa-user-gear fs-5 text-danger-emphasis"></i>
                                    <span>Profil</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <div class="border-top pt-4 mt-4">
                        <a href="<?= base_url('logout') ?>" class="d-flex align-items-center gap-2 text-danger text-decoration-none fs-7 fw-semibold">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Keluar
                        </a>
                    </div>
                </div>
                <!-- END LEFT SIDEBAR -->

                <!-- MAIN CONTENT -->
                <div class="app-main flex-column flex-row-fluid main-content-area" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid p-6 p-lg-8">
                        <?= $content ?>
                    </div>
                </div>
                <!-- END MAIN CONTENT -->
            </div>

        </div>
    </div>

    <!-- MOBILE BOTTOM NAV -->
    <nav class="mobile-bottom-nav shadow-sm">
        <a href="<?= base_url('my/dashboard') ?>" class="mobile-nav-item <?= (uri_string() === 'my/dashboard') ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
        </a>
        <a href="<?= base_url('my/booking') ?>" class="mobile-nav-item <?= (strpos(uri_string(), 'my/booking') !== false) ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-plus"></i><span>Booking</span>
        </a>
        <a href="<?= base_url('my/status') ?>" class="mobile-nav-item <?= (uri_string() === 'my/status') ? 'active' : '' ?>">
            <i class="fa-solid fa-hourglass-half"></i><span>Status</span>
        </a>
        <a href="<?= base_url('my/history') ?>" class="mobile-nav-item <?= (uri_string() === 'my/history') ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left"></i><span>Riwayat</span>
        </a>
        <a href="<?= base_url('my/profile') ?>" class="mobile-nav-item <?= (uri_string() === 'my/profile') ? 'active' : '' ?>">
            <i class="fa-solid fa-user-gear"></i><span>Profil</span>
        </a>
    </nav>

    <!-- JavaScript -->
    <script src="<?= base_url('theme/assets/plugins/global/plugins.bundle.js') ?>"></script>
    <script src="<?= base_url('theme/assets/js/scripts.bundle.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/realtime.js') ?>"></script>

    <!-- Flash notifications via SweetAlert2 -->
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

    <!-- CSRF + Global JS vars -->
    <script>
        var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
        var BASE_URL = '<?= base_url() ?>';
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type)) {
                    xhr.setRequestHeader('X-CSRF-Token', CSRF_HASH);
                    if (settings.data && typeof settings.data === 'string') {
                        if (settings.data.indexOf(CSRF_TOKEN_NAME) === -1) {
                            settings.data += '&' + encodeURIComponent(CSRF_TOKEN_NAME) + '=' + encodeURIComponent(CSRF_HASH);
                        }
                    }
                }
            }
        });
        // Auto-sync CSRF_HASH from every JSON response
        $(document).ajaxSuccess(function(event, xhr) {
            try {
                var r = JSON.parse(xhr.responseText);
                if (r && r.csrf_hash) {
                    CSRF_HASH = r.csrf_hash;
                }
            } catch (e) {}
        });
    </script>

    <!-- AJAX Notification Polling (every 30s) -->
    <script>
        (function() {
            var lastUnread = 0;

            function loadNotifications() {
                $.getJSON(BASE_URL + 'api/notifications', function(res) {
                    if (!res || res.error) return;

                    var count = res.unread_count || 0;
                    var badge = $('#notif_badge');

                    // Update badge
                    if (count > 0) {
                        badge.text(count).removeClass('d-none');
                    } else {
                        badge.addClass('d-none');
                    }

                    // Show toast if new notification arrived
                    if (count > lastUnread && lastUnread !== -1) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Notifikasi baru',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                    lastUnread = count;

                    // Build notification list
                    var html = '';
                    if (res.notifications && res.notifications.length > 0) {
                        res.notifications.forEach(function(n) {
                            html += '<a href="' + (n.url || '#') + '" class="d-flex align-items-start gap-3 p-4 border-bottom text-decoration-none hover-bg-light ' + (n.is_read ? 'opacity-75' : '') + '">' +
                                '<div class="flex-shrink-0 mt-1"><i class="fa-solid fa-circle fs-9 text-' + (n.is_read ? 'secondary' : 'primary') + '"></i></div>' +
                                '<div>' +
                                '<div class="fw-semibold text-gray-800 fs-7">' + n.title + '</div>' +
                                '<div class="text-muted fs-8">' + n.message + '</div>' +
                                '<div class="text-muted fs-9 mt-1">' + n.created_at + '</div>' +
                                '</div>' +
                                '</a>';
                        });
                    } else {
                        html = '<div class="text-center py-8 text-muted fs-7"><i class="fa-solid fa-bell-slash fs-2 d-block mb-3"></i>Tidak ada notifikasi</div>';
                    }
                    $('#notif_list').html(html);
                });
            }

            // Initial load + polling
            loadNotifications();
            setInterval(loadNotifications, 30000);

            // Mark all read
            $('#mark_all_read').on('click', function(e) {
                e.preventDefault();
                $.post(BASE_URL + 'api/notifications/read-all', {
                    [CSRF_TOKEN_NAME]: CSRF_HASH
                }, function() {
                    loadNotifications();
                });
            });
        })();
    </script>

    <?php if (isset($extra_js)): ?>
        <?= $extra_js ?>
    <?php endif; ?>
</body>

</html>