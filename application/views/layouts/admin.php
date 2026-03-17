<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — Admin ' : 'Admin — ' ?><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></title>

    <!-- CSRF meta tag for AJAX -->
    <meta name="csrf-token" content="<?= $this->security->get_csrf_hash() ?>" />

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Metronic Theme -->
    <link href="<?= base_url('theme/assets/plugins/global/plugins.bundle.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('theme/assets/plugins/custom/datatables/datatables.bundle.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('theme/assets/css/style.bundle.css') ?>" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

    <style>
        .admin-sidebar {
            width: 260px;
            min-height: 100vh;
            background: #1e1e2d;
            transition: width 0.3s ease;
            flex-shrink: 0;
        }

        .admin-sidebar .sidebar-logo {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .admin-nav-item {
            border-radius: 8px;
            margin: 2px 6px;
        }

        .admin-nav-item a {
            color: #a1a5b7;
            text-decoration: none;
            padding: 10px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .admin-nav-item:hover a {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .admin-nav-item.active a {
            background: #009ef7;
            color: #fff !important;
        }

        .admin-nav-section {
            color: #565674;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 12px 20px 6px;
            margin-top: 8px;
        }

        .sidebar-collapsed .admin-sidebar {
            width: 70px;
        }

        .sidebar-collapsed .sidebar-label,
        .sidebar-collapsed .admin-nav-section {
            display: none;
        }

        .sidebar-collapsed .admin-sidebar .sidebar-logo span {
            display: none;
        }

        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                left: -260px;
                top: 0;
                height: 100%;
                z-index: 1050;
                transition: left 0.3s ease;
            }

            .admin-sidebar.sidebar-open {
                left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body id="kt_app_body" class="app-default">
    <!-- Theme mode setup -->
    <script>
        var defaultThemeMode = "light";
        var themeMode = localStorage.getItem("data-bs-theme") || defaultThemeMode;
        if (themeMode === "system") themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    </script>

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebar_overlay"></div>

    <div class="d-flex min-vh-100" id="kt_app_root">

        <!-- ADMIN SIDEBAR -->
        <div class="admin-sidebar" id="kt_admin_sidebar">
            <div class="sidebar-logo d-flex align-items-center gap-3 p-5">
                <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle flex-shrink-0" style="width:36px;height:36px;">
                    <i class="fa-solid fa-wrench text-white fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold fs-6 text-white sidebar-label"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel') ?></div>
                    <div class="text-muted fs-9 sidebar-label">Admin Panel</div>
                </div>
            </div>

            <nav class="p-2">
                <div class="admin-nav-section">Menu Utama</div>

                <ul class="list-unstyled mb-0">
                    <li class="admin-nav-item <?= (uri_string() === 'admin') ? 'active' : '' ?>">
                        <a href="<?= base_url('admin') ?>">
                            <i class="fa-solid fa-gauge-high fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Dashboard</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= (uri_string() === 'admin/queue') ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/queue') ?>">
                            <i class="fa-solid fa-list-check fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Antrean Hari Ini</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= (strpos(uri_string(), 'admin/bookings') !== false) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/bookings') ?>">
                            <i class="fa-solid fa-calendar-check fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Manajemen Booking</span>
                        </a>
                    </li>

                    <div class="admin-nav-section">Keuangan</div>

                    <li class="admin-nav-item <?= (strpos(uri_string(), 'admin/payments') !== false) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/payments') ?>">
                            <i class="fa-solid fa-credit-card fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Pembayaran</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= (strpos(uri_string(), 'admin/reports') !== false) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/reports') ?>">
                            <i class="fa-solid fa-chart-bar fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Laporan</span>
                        </a>
                    </li>

                    <div class="admin-nav-section">Inventaris</div>

                    <li class="admin-nav-item <?= (strpos(uri_string(), 'admin/stock') !== false) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/stock') ?>">
                            <i class="fa-solid fa-boxes-stacked fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Stok Sparepart</span>
                            <?php
                            // Show low stock badge count
                            try {
                                $low_count = $this->db->where('stock <=', $this->db->protect_identifiers('min_stock'))->where('is_active', 1)->count_all_results('spare_parts');
                                if ($low_count > 0):
                            ?>
                                    <span class="badge badge-sm badge-danger ms-auto sidebar-label"><?= $low_count ?></span>
                            <?php endif;
                            } catch (Exception $e) {
                            } ?>
                        </a>
                    </li>

                    <div class="admin-nav-section">Pengaturan</div>

                    <li class="admin-nav-item <?= (strpos(uri_string(), 'admin/users') !== false) ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/users') ?>">
                            <i class="fa-solid fa-users fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Pengguna</span>
                        </a>
                    </li>
                    <li class="admin-nav-item <?= (uri_string() === 'admin/settings') ? 'active' : '' ?>">
                        <a href="<?= base_url('admin/settings') ?>">
                            <i class="fa-solid fa-gears fs-5 flex-shrink-0"></i>
                            <span class="sidebar-label">Pengaturan</span>
                        </a>
                    </li>
                </ul>

                <div class="border-top border-secondary mt-4 pt-4">
                    <a href="<?= base_url('logout') ?>" class="d-flex align-items-center gap-3 text-danger text-decoration-none p-2 sidebar-nav-item rounded-2">
                        <i class="fa-solid fa-arrow-right-from-bracket fs-5 flex-shrink-0"></i>
                        <span class="sidebar-label fs-7">Keluar</span>
                    </a>
                </div>
            </nav>
        </div>
        <!-- END ADMIN SIDEBAR -->

        <!-- MAIN AREA -->
        <div class="flex-grow-1 d-flex flex-column overflow-hidden">

            <!-- TOP HEADER -->
            <div class="d-flex align-items-center justify-content-between px-6 py-3 border-bottom bg-body" id="kt_app_header">
                <div class="d-flex align-items-center gap-3">
                    <!-- Hamburger -->
                    <button class="btn btn-icon btn-sm btn-light" id="sidebar_toggle">
                        <i class="fa-solid fa-bars fs-5"></i>
                    </button>
                    <!-- Breadcrumb / Page title -->
                    <div>
                        <h4 class="fw-bold text-gray-800 mb-0 fs-5"><?= isset($page_title) ? htmlspecialchars($page_title) : 'Dashboard' ?></h4>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <!-- Low Stock Alert -->
                    <?php
                    try {
                        $low_parts = $this->db->select('name,stock,min_stock')->where('stock <=', $this->db->protect_identifiers('min_stock'))->where('is_active', 1)->limit(3)->get('spare_parts')->result_array();
                        if (!empty($low_parts)):
                    ?>
                            <div class="d-none d-md-block">
                                <span class="badge badge-danger cursor-pointer" data-bs-toggle="tooltip" title="<?= count($low_parts) ?> item stok rendah — klik untuk lihat">
                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>Stok Rendah: <?= count($low_parts) ?>
                                </span>
                            </div>
                    <?php endif;
                    } catch (Exception $e) {
                    } ?>

                    <!-- Notifications -->
                    <div class="position-relative">
                        <button class="btn btn-icon btn-light btn-sm position-relative" id="admin_notif_btn" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-bell fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-9 d-none" id="admin_notif_badge">0</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-0" style="min-width:340px;max-height:400px;overflow-y:auto;" id="admin_notif_dropdown">
                            <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                                <span class="fw-bold fs-6">Notifikasi</span>
                                <a href="#" class="text-muted fs-8" id="admin_mark_all_read">Tandai semua dibaca</a>
                            </div>
                            <div id="admin_notif_list">
                                <div class="text-center py-8 text-muted fs-7"><i class="fa-solid fa-bell-slash fs-2 d-block mb-3"></i>Tidak ada notifikasi</div>
                            </div>
                        </div>
                    </div>

                    <!-- User dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle text-white fs-8 fw-bold flex-shrink-0" style="width:30px;height:30px;">
                                <?= strtoupper(substr($current_user['name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <span class="fw-semibold text-gray-800 d-none d-md-block"><?= htmlspecialchars($current_user['name'] ?? '') ?></span>
                            <span class="badge badge-light-primary fs-9 d-none d-md-inline"><?= ucfirst($current_user['role'] ?? '') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li class="px-4 py-2 border-bottom">
                                <div class="fw-bold text-gray-800 fs-6"><?= htmlspecialchars($current_user['name'] ?? '') ?></div>
                                <div class="text-muted fs-8"><?= htmlspecialchars($current_user['email'] ?? '') ?></div>
                            </li>
                            <li><a class="dropdown-item" href="<?= base_url('admin/settings') ?>"><i class="fa-solid fa-gears me-2 text-primary"></i>Pengaturan</a></li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- END TOP HEADER -->

            <!-- Low Stock Alert Banner -->
            <?php
            try {
                $low_count_banner = $this->db->where('stock <=', $this->db->protect_identifiers('min_stock'))->where('is_active', 1)->count_all_results('spare_parts');
                if ($low_count_banner > 0):
            ?>
                    <div class="alert alert-warning alert-dismissible d-flex align-items-center gap-3 rounded-0 border-0 border-bottom mb-0 py-3 px-6" role="alert">
                        <i class="fa-solid fa-triangle-exclamation fs-4 text-warning"></i>
                        <div class="flex-grow-1">
                            <strong><?= $low_count_banner ?> item sparepart</strong> berada di bawah stok minimum.
                            <a href="<?= base_url('admin/stock') ?>" class="alert-link">Lihat detail stok &rarr;</a>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
            <?php endif;
            } catch (Exception $e) {
            } ?>

            <!-- PAGE CONTENT -->
            <main class="flex-grow-1 p-6 p-lg-8 overflow-auto">
                <?= $content ?>
            </main>
            <!-- END PAGE CONTENT -->

            <!-- FOOTER -->
            <div class="px-6 py-3 border-top text-muted fs-8 d-flex justify-content-between">
                <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></span>
                <span>Admin Panel v1.0</span>
            </div>
        </div>
        <!-- END MAIN AREA -->

    </div>

    <!-- JavaScript -->
    <script src="<?= base_url('theme/assets/plugins/global/plugins.bundle.js') ?>"></script>
    <script src="<?= base_url('theme/assets/plugins/custom/datatables/datatables.bundle.js') ?>"></script>
    <script src="<?= base_url('theme/assets/js/scripts.bundle.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/realtime.js') ?>"></script>

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

    <!-- Global JS vars -->
    <script>
        var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
        var BASE_URL = '<?= base_url() ?>';
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type)) {
                    xhr.setRequestHeader('X-CSRF-Token', CSRF_HASH);
                    // Also inject into POST data when sent as string
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

        // Sidebar toggle
        var sidebar = document.getElementById('kt_admin_sidebar');
        var overlay = document.getElementById('sidebar_overlay');

        document.getElementById('sidebar_toggle').addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('sidebar-open');
                overlay.classList.toggle('show');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
            }
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('show');
        });

        // Admin notifications polling
        (function() {
            var lastUnread = 0;

            function loadAdminNotifications() {
                $.getJSON(BASE_URL + 'api/notifications', function(res) {
                    if (!res || res.error) return;
                    var count = res.unread_count || 0;
                    var badge = $('#admin_notif_badge');
                    if (count > 0) {
                        badge.text(count).removeClass('d-none');
                    } else {
                        badge.addClass('d-none');
                    }
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
                    var html = '';
                    if (res.notifications && res.notifications.length > 0) {
                        res.notifications.forEach(function(n) {
                            html += '<a href="' + (n.url || '#') + '" class="d-flex align-items-start gap-3 p-4 border-bottom text-decoration-none ' + (n.is_read ? 'opacity-75' : '') + '">' +
                                '<div class="flex-shrink-0 mt-1"><i class="fa-solid fa-circle fs-9 text-' + (n.is_read ? 'secondary' : 'primary') + '"></i></div>' +
                                '<div><div class="fw-semibold text-gray-800 fs-7">' + n.title + '</div><div class="text-muted fs-8">' + n.message + '</div><div class="text-muted fs-9 mt-1">' + n.created_at + '</div></div></a>';
                        });
                    } else {
                        html = '<div class="text-center py-8 text-muted fs-7"><i class="fa-solid fa-bell-slash fs-2 d-block mb-3"></i>Tidak ada notifikasi</div>';
                    }
                    $('#admin_notif_list').html(html);
                });
            }

            loadAdminNotifications();
            setInterval(loadAdminNotifications, 30000);

            $('#admin_mark_all_read').on('click', function(e) {
                e.preventDefault();
                $.post(BASE_URL + 'api/notifications/read-all', {
                    [CSRF_TOKEN_NAME]: CSRF_HASH
                }, function() {
                    loadAdminNotifications();
                });
            });
        })();
    </script>

    <?php if (isset($extra_js)): ?>
        <?= $extra_js ?>
    <?php endif; ?>
</body>

</html>