<?php
$header_title = $page_title ?? $title ?? 'Dashboard';
$site_title = $settings['workshop_name'] ?? 'JBM Bengkel Mobil';
$current_uri = uri_string();

$nav_sections = [
    [
        'label' => 'Operasional',
        'items' => [
            ['label' => 'Dashboard', 'href' => base_url('admin'), 'icon' => 'fa-gauge-high', 'match' => ['admin']],
            ['label' => 'Antrean Hari Ini', 'href' => base_url('admin/queue'), 'icon' => 'fa-list-check', 'match' => ['admin/queue']],
            ['label' => 'Booking', 'href' => base_url('admin/bookings'), 'icon' => 'fa-calendar-check', 'match' => ['admin/bookings', 'admin/booking']],
            ['label' => 'Pembayaran', 'href' => base_url('admin/payments'), 'icon' => 'fa-credit-card', 'match' => ['admin/payments', 'admin/payment', 'admin/invoice']],
            ['label' => 'Laporan', 'href' => base_url('admin/reports'), 'icon' => 'fa-chart-simple', 'match' => ['admin/reports']],
        ],
    ],
    [
        'label' => 'Katalog',
        'items' => [
            ['label' => 'Layanan', 'href' => base_url('admin/services'), 'icon' => 'fa-screwdriver-wrench', 'match' => ['admin/services']],
            ['label' => 'Sparepart', 'href' => base_url('admin/spare-parts'), 'icon' => 'fa-boxes-stacked', 'match' => ['admin/spare-parts', 'admin/stock']],
            ['label' => 'Galeri', 'href' => base_url('admin/gallery'), 'icon' => 'fa-images', 'match' => ['admin/gallery']],
        ],
    ],
    [
        'label' => 'Sistem',
        'items' => [
            ['label' => 'Pengguna', 'href' => base_url('admin/users'), 'icon' => 'fa-users', 'match' => ['admin/users']],
            ['label' => 'Pengaturan', 'href' => base_url('admin/settings'), 'icon' => 'fa-gears', 'match' => ['admin/settings']],
        ],
    ],
];

$low_count = 0;
$low_parts = [];
try {
    $low_count = (int) $this->db->where('stock <=', $this->db->protect_identifiers('min_stock'))->where('is_active', 1)->count_all_results('spare_parts');
    $low_parts = $this->db->select('name, stock, min_stock')->where('stock <=', $this->db->protect_identifiers('min_stock'))->where('is_active', 1)->limit(3)->get('spare_parts')->result_array();
} catch (Exception $e) {
    $low_count = 0;
    $low_parts = [];
}

$is_active = static function ($uri, array $matches): bool {
    foreach ($matches as $match) {
        if ($uri === $match || strpos($uri, $match . '/') === 0) {
            return true;
        }
    }

    return false;
};
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($header_title) ?> - Admin <?= htmlspecialchars($site_title) ?></title>
    <meta name="csrf-token" content="<?= $this->security->get_csrf_hash() ?>" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <link href="<?= base_url('theme/assets/plugins/global/plugins.bundle.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('theme/assets/plugins/custom/datatables/datatables.bundle.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('theme/assets/css/style.bundle.css') ?>" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />

    <script>
        (function() {
            var defaultThemeMode = 'light';
            var themeMode = localStorage.getItem('data-bs-theme') || defaultThemeMode;
            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            document.documentElement.setAttribute('data-bs-theme', themeMode);
        })();
    </script>

    <style>
        :root {
            --admin-sidebar-width: 308px;
            --admin-sidebar-collapsed-width: 96px;
            --admin-surface: rgba(255, 255, 255, 0.88);
            --admin-surface-strong: rgba(255, 255, 255, 0.96);
            --admin-border: rgba(15, 23, 42, 0.09);
            --admin-shadow: 0 30px 80px rgba(15, 23, 42, 0.10);
            --admin-shadow-soft: 0 16px 50px rgba(15, 23, 42, 0.08);
            --admin-text-muted: #64748b;
            --admin-sidebar-bg: linear-gradient(180deg, #0f172a 0%, #111c35 38%, #14213d 100%);
            --admin-sidebar-border: rgba(148, 163, 184, 0.16);
            --admin-sidebar-text: rgba(226, 232, 240, 0.78);
            --admin-sidebar-text-strong: #f8fafc;
            --admin-sidebar-active: linear-gradient(135deg, rgba(14, 165, 233, 0.28), rgba(59, 130, 246, 0.34));
            --admin-sidebar-hover: rgba(148, 163, 184, 0.10);
            --admin-shell-bg: radial-gradient(circle at top left, rgba(14, 165, 233, 0.18), transparent 28%), radial-gradient(circle at top right, rgba(16, 185, 129, 0.12), transparent 22%), linear-gradient(180deg, #eef4ff 0%, #f8fbff 40%, #f4f6fb 100%);
        }

        [data-bs-theme="dark"] {
            --admin-surface: rgba(15, 23, 42, 0.84);
            --admin-surface-strong: rgba(15, 23, 42, 0.92);
            --admin-border: rgba(148, 163, 184, 0.12);
            --admin-shadow: 0 30px 80px rgba(2, 6, 23, 0.48);
            --admin-shadow-soft: 0 18px 50px rgba(2, 6, 23, 0.38);
            --admin-text-muted: #94a3b8;
            --admin-sidebar-bg: linear-gradient(180deg, #020617 0%, #071126 42%, #0f172a 100%);
            --admin-sidebar-border: rgba(148, 163, 184, 0.12);
            --admin-sidebar-text: rgba(226, 232, 240, 0.72);
            --admin-sidebar-text-strong: #ffffff;
            --admin-sidebar-active: linear-gradient(135deg, rgba(59, 130, 246, 0.32), rgba(56, 189, 248, 0.20));
            --admin-sidebar-hover: rgba(148, 163, 184, 0.08);
            --admin-shell-bg: radial-gradient(circle at top left, rgba(37, 99, 235, 0.22), transparent 28%), radial-gradient(circle at top right, rgba(16, 185, 129, 0.10), transparent 24%), linear-gradient(180deg, #020617 0%, #07111f 34%, #0b1324 100%);
        }

        html,
        body {
            min-height: 100%;
        }

        body.admin-app-body {
            background: var(--admin-shell-bg);
            color: var(--bs-gray-900);
            overflow-x: hidden;
        }

        .admin-shell {
            min-height: 100vh;
            position: relative;
            display: block;
            padding-left: var(--admin-sidebar-width);
            transition: padding-left 0.28s ease;
        }

        .admin-shell::before,
        .admin-shell::after {
            content: "";
            position: fixed;
            border-radius: 999px;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }

        .admin-shell::before {
            width: 340px;
            height: 340px;
            top: -110px;
            right: 12vw;
            background: rgba(14, 165, 233, 0.16);
        }

        .admin-shell::after {
            width: 260px;
            height: 260px;
            bottom: 10vh;
            left: 10vw;
            background: rgba(16, 185, 129, 0.14);
        }

        .admin-sidebar {
            width: var(--admin-sidebar-width);
            height: 100vh;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1020;
            padding: 24px 20px;
            background: var(--admin-sidebar-bg);
            border-right: 1px solid var(--admin-sidebar-border);
            box-shadow: 24px 0 70px rgba(2, 6, 23, 0.22);
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: width 0.28s ease, transform 0.28s ease;
        }

        .admin-sidebar__brand,
        .admin-sidebar__meta,
        .admin-sidebar__footer {
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(14px);
            border-radius: 24px;
        }

        .admin-sidebar__brand {
            padding: 18px;
        }

        .admin-sidebar__brand-badge {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0ea5e9, #2563eb);
            color: #fff;
            box-shadow: 0 14px 30px rgba(37, 99, 235, 0.35);
        }

        .admin-sidebar__meta,
        .admin-sidebar__footer {
            padding: 16px 18px;
        }

        .admin-sidebar__hint {
            color: var(--admin-sidebar-text);
            font-size: 12px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .admin-nav-section {
            margin-top: 10px;
        }

        .admin-nav-section__label {
            color: rgba(226, 232, 240, 0.40);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.14em;
            margin: 0 0 10px 14px;
        }

        .admin-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 14px;
            border-radius: 18px;
            text-decoration: none;
            color: var(--admin-sidebar-text);
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .admin-nav-link:hover {
            color: var(--admin-sidebar-text-strong);
            background: var(--admin-sidebar-hover);
            transform: translateX(2px);
        }

        .admin-nav-link.is-active {
            color: var(--admin-sidebar-text-strong);
            background: var(--admin-sidebar-active);
            box-shadow: inset 0 0 0 1px rgba(125, 211, 252, 0.14);
        }

        .admin-nav-link__icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.06);
            flex-shrink: 0;
        }

        .admin-nav-link.is-active .admin-nav-link__icon {
            background: rgba(255, 255, 255, 0.12);
        }

        .admin-main {
            min-width: 0;
            position: relative;
            z-index: 1;
            padding: 20px;
        }

        .admin-main__inner {
            min-height: calc(100vh - 40px);
            display: flex;
            flex-direction: column;
            border: 1px solid var(--admin-border);
            border-radius: 32px;
            background: linear-gradient(180deg, var(--admin-surface-strong) 0%, var(--admin-surface) 100%);
            backdrop-filter: blur(24px);
            box-shadow: var(--admin-shadow);
            overflow: visible;
        }

        .admin-topbar {
            position: sticky;
            top: 0;
            z-index: 15;
            padding: 18px 24px;
            border-bottom: 1px solid var(--admin-border);
            background: rgba(255, 255, 255, 0.36);
            backdrop-filter: blur(18px);
        }

        [data-bs-theme="dark"] .admin-topbar {
            background: rgba(15, 23, 42, 0.44);
        }

        .admin-topbar__crumb {
            color: var(--admin-text-muted);
            font-size: 12px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .admin-topbar__title {
            font-size: clamp(1.25rem, 1.1rem + 0.8vw, 1.9rem);
            font-weight: 700;
            margin: 4px 0 0;
        }

        .admin-surface-card {
            border: 1px solid var(--admin-border);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(255, 255, 255, 0.72));
            box-shadow: var(--admin-shadow-soft);
        }

        [data-bs-theme="dark"] .admin-surface-card {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.80), rgba(15, 23, 42, 0.66));
        }

        .admin-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--admin-border);
            background: rgba(255, 255, 255, 0.72);
            color: var(--bs-gray-700);
            font-size: 12px;
            font-weight: 600;
        }

        [data-bs-theme="dark"] .admin-chip {
            background: rgba(15, 23, 42, 0.78);
            color: #cbd5e1;
        }

        .admin-page-wrap {
            flex: 1;
            padding: 24px;
            overflow: visible;
        }

        .admin-page-wrap .card.card-flush {
            border: 1px solid var(--admin-border);
            border-radius: 28px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.84), rgba(255, 255, 255, 0.7));
            box-shadow: var(--admin-shadow-soft);
            overflow: hidden;
        }

        [data-bs-theme="dark"] .admin-page-wrap .card.card-flush {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.82), rgba(15, 23, 42, 0.66));
        }

        .admin-page-wrap .card.card-flush .card-header {
            min-height: auto;
            padding: 22px 24px 16px;
            border-bottom: 1px solid var(--admin-border);
            background: transparent;
        }

        .admin-page-wrap .card.card-flush .card-body {
            padding: 24px;
        }

        .admin-page-wrap .card.card-flush .card-body.p-0,
        .admin-page-wrap .card.card-flush .card-body.pt-0,
        .admin-page-wrap .card.card-flush .card-body.py-0 {
            padding: 0 !important;
        }

        .admin-page-wrap .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .admin-page-wrap .nav-line-tabs {
            gap: 8px;
            border-bottom: 0;
        }

        .admin-page-wrap .nav-line-tabs .nav-link {
            margin: 0;
            border: 1px solid transparent;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 600;
            color: var(--admin-text-muted);
            background: rgba(148, 163, 184, 0.1);
        }

        .admin-page-wrap .nav-line-tabs .nav-link.active,
        .admin-page-wrap .nav-line-tabs .show>.nav-link {
            color: var(--bs-primary);
            border-color: rgba(59, 130, 246, 0.14);
            background: rgba(59, 130, 246, 0.12);
        }

        .admin-page-hero {
            border: 1px solid var(--admin-border);
            border-radius: 32px;
            padding: 26px;
            margin-bottom: 24px;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(37, 99, 235, 0.08) 42%, rgba(16, 185, 129, 0.10) 100%), var(--admin-surface);
            box-shadow: var(--admin-shadow-soft);
        }

        .admin-page-hero__eyebrow {
            color: var(--admin-text-muted);
            font-size: 12px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .admin-page-hero__title {
            font-size: clamp(1.5rem, 1.2rem + 1vw, 2.4rem);
            line-height: 1.08;
            margin-bottom: 12px;
        }

        .admin-page-hero__desc {
            max-width: 760px;
            color: var(--admin-text-muted);
            font-size: 14px;
            margin: 0;
        }

        .admin-kpi-card {
            height: 100%;
            padding: 20px;
            border-radius: 24px;
            border: 1px solid var(--admin-border);
            background: rgba(255, 255, 255, 0.72);
        }

        [data-bs-theme="dark"] .admin-kpi-card {
            background: rgba(15, 23, 42, 0.60);
        }

        .admin-kpi-card__icon {
            width: 52px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: rgba(14, 165, 233, 0.14);
            color: var(--bs-primary);
        }

        .admin-data-table thead th {
            background: rgba(148, 163, 184, 0.10);
            color: var(--admin-text-muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border-bottom-width: 0;
            padding-top: 16px;
            padding-bottom: 16px;
        }

        .admin-data-table tbody td {
            padding-top: 18px;
            padding-bottom: 18px;
            vertical-align: middle;
        }

        .admin-table-meta {
            color: var(--admin-text-muted);
            font-size: 12px;
        }

        .theme-mode-option.active {
            background: rgba(var(--bs-primary-rgb), 0.08);
            color: var(--bs-primary);
        }

        .admin-notif-dropdown {
            min-width: 360px;
            max-height: 420px;
            overflow-y: auto;
            border-radius: 24px;
            border-color: var(--admin-border);
            box-shadow: var(--admin-shadow-soft);
            background: var(--admin-surface-strong);
            backdrop-filter: blur(16px);
        }

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(2, 6, 23, 0.58);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            z-index: 1010;
        }

        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        body.sidebar-mobile-open {
            overflow: hidden;
        }

        body.sidebar-collapsed .admin-shell {
            padding-left: var(--admin-sidebar-collapsed-width);
        }

        body.sidebar-collapsed .admin-sidebar {
            width: var(--admin-sidebar-collapsed-width);
            padding-left: 14px;
            padding-right: 14px;
        }

        body.sidebar-collapsed .admin-sidebar .sidebar-label,
        body.sidebar-collapsed .admin-sidebar .admin-nav-section__label,
        body.sidebar-collapsed .admin-sidebar .admin-sidebar__footer,
        body.sidebar-collapsed .admin-sidebar .admin-sidebar__meta,
        body.sidebar-collapsed .admin-sidebar .admin-nav-link__badge,
        body.sidebar-collapsed .admin-sidebar .admin-sidebar__brand-copy {
            display: none !important;
        }

        body.sidebar-collapsed .admin-sidebar__brand {
            padding: 14px;
            display: flex;
            justify-content: center;
        }

        body.sidebar-collapsed .admin-nav-link {
            justify-content: center;
            padding-left: 10px;
            padding-right: 10px;
        }

        body.sidebar-collapsed .admin-nav-link__icon {
            margin-right: 0;
        }

        @media (max-width: 991.98px) {
            .admin-shell {
                padding-left: 0;
            }

            .admin-sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: min(88vw, 320px);
                transform: translateX(-100%);
            }

            .admin-sidebar.sidebar-open {
                transform: translateX(0);
            }

            .admin-main {
                padding: 12px;
            }

            .admin-main__inner {
                min-height: calc(100vh - 24px);
                border-radius: 26px;
            }

            .admin-topbar {
                top: 0;
            }

            .admin-topbar,
            .admin-page-wrap {
                padding-left: 18px;
                padding-right: 18px;
            }

            .admin-page-hero {
                padding: 22px;
            }
        }

        @media (max-width: 767.98px) {
            .admin-topbar {
                padding-top: 16px;
                padding-bottom: 16px;
            }

            .admin-page-wrap {
                padding: 16px;
            }

            .admin-page-wrap .card.card-flush .card-header {
                padding: 18px 18px 14px;
            }

            .admin-page-wrap .card.card-flush .card-body {
                padding: 18px;
            }

            .admin-page-hero {
                border-radius: 24px;
                padding: 18px;
            }

            .admin-notif-dropdown {
                min-width: min(92vw, 360px);
            }
        }
    </style>
</head>

<body id="kt_app_body" class="admin-app-body app-default">
    <div class="sidebar-overlay" id="sidebar_overlay"></div>

    <div class="admin-shell" id="kt_app_root">
        <aside class="admin-sidebar" id="kt_admin_sidebar">
            <div class="admin-sidebar__brand">
                <a href="<?= base_url('admin') ?>" class="d-flex align-items-center gap-3 text-decoration-none">
                    <span class="admin-sidebar__brand-badge">
                        <i class="fa-solid fa-wrench fs-3"></i>
                    </span>
                    <span class="admin-sidebar__brand-copy min-w-0">
                        <span class="d-block fw-bold fs-5 text-white text-truncate sidebar-label"><?= htmlspecialchars($site_title) ?></span>
                        <span class="d-block text-white opacity-75 fs-8 sidebar-label">Workshop control center</span>
                    </span>
                </a>
            </div>

            <div class="admin-sidebar__meta d-flex align-items-start justify-content-between gap-3">
                <div class="sidebar-label">
                    <div class="admin-sidebar__hint">Mode Operasional</div>
                    <div class="text-white fw-semibold fs-7 mt-2">Admin & Kasir</div>
                    <div class="text-white opacity-75 fs-8 mt-1">Pantau booking, pembayaran, dan stok dalam satu panel.</div>
                </div>
                <span class="badge badge-light-info admin-nav-link__badge"><?= date('d M') ?></span>
            </div>

            <div class="flex-grow-1 overflow-auto pe-1">
                <?php foreach ($nav_sections as $section): ?>
                    <div class="admin-nav-section">
                        <div class="admin-nav-section__label sidebar-label"><?= htmlspecialchars($section['label']) ?></div>
                        <ul class="admin-nav-list">
                            <?php foreach ($section['items'] as $item): ?>
                                <?php $active = $is_active($current_uri, $item['match']); ?>
                                <li>
                                    <a href="<?= $item['href'] ?>" class="admin-nav-link <?= $active ? 'is-active' : '' ?>">
                                        <span class="admin-nav-link__icon"><i class="fa-solid <?= htmlspecialchars($item['icon']) ?> fs-5"></i></span>
                                        <span class="flex-grow-1 min-w-0 sidebar-label">
                                            <span class="d-block fw-semibold text-truncate"><?= htmlspecialchars($item['label']) ?></span>
                                        </span>
                                        <?php if ($item['label'] === 'Sparepart' && $low_count > 0): ?>
                                            <span class="badge badge-danger badge-sm admin-nav-link__badge"><?= $low_count ?></span>
                                        <?php endif; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="admin-sidebar__footer sidebar-label">
                <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <div class="admin-sidebar__hint">Sedang login</div>
                        <div class="text-white fw-semibold fs-7 mt-1"><?= htmlspecialchars($current_user['name'] ?? 'Administrator') ?></div>
                    </div>
                    <span class="badge badge-light-primary text-uppercase"><?= htmlspecialchars($current_user['role'] ?? 'admin') ?></span>
                </div>
                <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-light-danger w-100">
                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar
                </a>
            </div>
        </aside>

        <div class="admin-main">
            <div class="admin-main__inner">
                <header class="admin-topbar">
                    <div class="d-flex flex-wrap align-items-start justify-content-between gap-4">
                        <div class="d-flex align-items-start gap-3">
                            <button class="btn btn-icon btn-light-primary btn-sm mt-1" id="sidebar_toggle" aria-label="Toggle sidebar">
                                <i class="fa-solid fa-bars fs-5"></i>
                            </button>
                            <div>
                                <div class="admin-topbar__crumb">Admin panel / <?= htmlspecialchars($current_user['role'] ?? 'admin') ?></div>
                                <h1 class="admin-topbar__title"><?= htmlspecialchars($header_title) ?></h1>
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                                    <span class="admin-chip"><i class="fa-regular fa-clock"></i><?= date('l, d F Y') ?></span>
                                    <?php if ($low_count > 0): ?>
                                        <a href="<?= base_url('admin/spare-parts') ?>" class="admin-chip text-decoration-none">
                                            <i class="fa-solid fa-triangle-exclamation text-warning"></i><?= $low_count ?> item stok rendah
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                            <div class="dropdown">
                                <button class="btn btn-light d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width: 34px; height: 34px;">
                                        <i class="fa-solid fa-circle-half-stroke fs-6" id="themeModeIcon"></i>
                                    </span>
                                    <span class="fw-semibold d-none d-sm-inline" id="themeModeLabel">Tema</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-2 admin-surface-card border-0 shadow-sm" style="min-width: 220px;">
                                    <button type="button" class="dropdown-item d-flex align-items-center justify-content-between rounded-3 px-3 py-2 theme-mode-option" data-theme-value="light">
                                        <span><i class="fa-regular fa-sun me-2"></i>Light</span>
                                        <i class="fa-solid fa-check text-primary d-none"></i>
                                    </button>
                                    <button type="button" class="dropdown-item d-flex align-items-center justify-content-between rounded-3 px-3 py-2 theme-mode-option" data-theme-value="dark">
                                        <span><i class="fa-regular fa-moon me-2"></i>Dark</span>
                                        <i class="fa-solid fa-check text-primary d-none"></i>
                                    </button>
                                    <button type="button" class="dropdown-item d-flex align-items-center justify-content-between rounded-3 px-3 py-2 theme-mode-option" data-theme-value="system">
                                        <span><i class="fa-solid fa-display me-2"></i>System</span>
                                        <i class="fa-solid fa-check text-primary d-none"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="position-relative dropdown">
                                <button class="btn btn-light btn-icon position-relative" id="admin_notif_btn" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-bell fs-5"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-9 d-none" id="admin_notif_badge">0</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-0 admin-notif-dropdown" id="admin_notif_dropdown">
                                    <div class="d-flex justify-content-between align-items-center p-4 border-bottom border-gray-200">
                                        <div>
                                            <div class="fw-bold fs-6">Notifikasi</div>
                                            <div class="text-muted fs-8">Update aktivitas booking dan pembayaran terbaru.</div>
                                        </div>
                                        <a href="#" class="text-primary fs-8 fw-semibold" id="admin_mark_all_read">Tandai dibaca</a>
                                    </div>
                                    <div id="admin_notif_list">
                                        <div class="text-center py-8 text-muted fs-7"><i class="fa-solid fa-bell-slash fs-2 d-block mb-3"></i>Tidak ada notifikasi</div>
                                    </div>
                                </div>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-light d-flex align-items-center gap-3" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="d-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width: 40px; height: 40px; background: linear-gradient(135deg, #0ea5e9, #2563eb);">
                                        <?= strtoupper(substr($current_user['name'] ?? 'A', 0, 1)) ?>
                                    </span>
                                    <span class="text-start d-none d-md-inline">
                                        <span class="d-block fw-semibold text-gray-900 lh-sm"><?= htmlspecialchars($current_user['name'] ?? '') ?></span>
                                        <span class="d-block text-muted fs-8 lh-sm"><?= htmlspecialchars($current_user['email'] ?? '') ?></span>
                                    </span>
                                    <i class="fa-solid fa-chevron-down fs-8 text-muted d-none d-md-inline"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-2 admin-surface-card border-0 shadow-sm" style="min-width: 250px;">
                                    <div class="px-3 py-2 border-bottom border-gray-200 mb-2">
                                        <div class="fw-bold text-gray-900"><?= htmlspecialchars($current_user['name'] ?? '') ?></div>
                                        <div class="text-muted fs-8"><?= htmlspecialchars($current_user['role'] ?? '') ?></div>
                                    </div>
                                    <a class="dropdown-item rounded-3 px-3 py-2" href="<?= base_url('admin/settings') ?>"><i class="fa-solid fa-gears me-2 text-primary"></i>Pengaturan</a>
                                    <a class="dropdown-item rounded-3 px-3 py-2 text-danger" href="<?= base_url('logout') ?>"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($low_parts)): ?>
                        <div class="mt-4 admin-surface-card p-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold text-gray-900">Perhatian stok minimum</div>
                                    <div class="text-muted fs-7">Beberapa sparepart perlu segera di-restock agar operasional tetap lancar.</div>
                                </div>
                                <a href="<?= base_url('admin/spare-parts') ?>" class="btn btn-sm btn-primary">Kelola stok</a>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <?php foreach ($low_parts as $part): ?>
                                    <span class="admin-chip"><i class="fa-solid fa-box-open text-warning"></i><?= htmlspecialchars($part['name']) ?>: <?= (int) $part['stock'] ?>/<?= (int) $part['min_stock'] ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </header>

                <main class="admin-page-wrap">
                    <?= $content ?>
                </main>

                <footer class="px-6 py-4 border-top text-muted fs-8 d-flex flex-wrap justify-content-between gap-2">
                    <span>&copy; <?= date('Y') ?> <?= htmlspecialchars($site_title) ?></span>
                    <span>Admin workspace dengan mode light dan dark</span>
                </footer>
            </div>
        </div>
    </div>

    <script src="<?= base_url('theme/assets/plugins/global/plugins.bundle.js') ?>"></script>
    <script src="<?= base_url('theme/assets/plugins/custom/datatables/datatables.bundle.js') ?>"></script>
    <script src="<?= base_url('theme/assets/js/scripts.bundle.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/realtime.js') ?>"></script>

    <?php
    $swal_type = $this->session->flashdata('swal_type');
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

    <script>
        var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
        var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';
        var BASE_URL = '<?= base_url() ?>';

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type)) {
                    xhr.setRequestHeader('X-CSRF-Token', CSRF_HASH);
                    if (settings.data && typeof settings.data === 'string' && settings.data.indexOf(CSRF_TOKEN_NAME) === -1) {
                        settings.data += '&' + encodeURIComponent(CSRF_TOKEN_NAME) + '=' + encodeURIComponent(CSRF_HASH);
                    }
                }
            }
        });

        $(document).ajaxSuccess(function(event, xhr) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response && response.csrf_hash) {
                    CSRF_HASH = response.csrf_hash;
                }
            } catch (e) {}
        });

        (function() {
            var sidebar = document.getElementById('kt_admin_sidebar');
            var overlay = document.getElementById('sidebar_overlay');
            var toggleButton = document.getElementById('sidebar_toggle');
            var navLinks = document.querySelectorAll('.admin-nav-link');
            var isDesktop = function() {
                return window.innerWidth >= 992;
            };

            var setMobileSidebar = function(open) {
                sidebar.classList.toggle('sidebar-open', open);
                overlay.classList.toggle('show', open);
                document.body.classList.toggle('sidebar-mobile-open', open);
            };

            var syncSidebarPreference = function() {
                if (!isDesktop()) {
                    document.body.classList.remove('sidebar-collapsed');
                    setMobileSidebar(false);
                    return;
                }
                setMobileSidebar(false);
                if (localStorage.getItem('admin-sidebar-state') === 'collapsed') {
                    document.body.classList.add('sidebar-collapsed');
                } else {
                    document.body.classList.remove('sidebar-collapsed');
                }
            };

            toggleButton.addEventListener('click', function() {
                if (isDesktop()) {
                    document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('admin-sidebar-state', document.body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
                    return;
                }
                setMobileSidebar(!sidebar.classList.contains('sidebar-open'));
            });

            overlay.addEventListener('click', function() {
                setMobileSidebar(false);
            });

            navLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    if (!isDesktop()) {
                        setMobileSidebar(false);
                    }
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    setMobileSidebar(false);
                }
            });

            window.addEventListener('resize', syncSidebarPreference);
            syncSidebarPreference();
        })();

        (function() {
            var themeOptions = document.querySelectorAll('.theme-mode-option');
            var themeLabel = document.getElementById('themeModeLabel');
            var themeIcon = document.getElementById('themeModeIcon');
            var systemQuery = window.matchMedia('(prefers-color-scheme: dark)');

            var getStoredTheme = function() {
                return localStorage.getItem('data-bs-theme') || 'light';
            };

            var getAppliedTheme = function(mode) {
                if (mode === 'system') {
                    return systemQuery.matches ? 'dark' : 'light';
                }
                return mode;
            };

            var updateThemeUi = function(mode) {
                var applied = getAppliedTheme(mode);
                var labels = {
                    light: 'Light mode',
                    dark: 'Dark mode',
                    system: 'Ikuti sistem'
                };
                var icons = {
                    light: 'fa-sun',
                    dark: 'fa-moon',
                    system: 'fa-display'
                };

                document.documentElement.setAttribute('data-bs-theme', applied);
                themeLabel.textContent = labels[mode] || 'Tema';
                themeIcon.className = 'fa-solid ' + (icons[mode] || 'fa-circle-half-stroke') + ' fs-6';

                themeOptions.forEach(function(option) {
                    var active = option.getAttribute('data-theme-value') === mode;
                    option.classList.toggle('active', active);
                    var check = option.querySelector('.fa-check');
                    if (check) {
                        check.classList.toggle('d-none', !active);
                    }
                });
            };

            themeOptions.forEach(function(option) {
                option.addEventListener('click', function() {
                    var nextTheme = option.getAttribute('data-theme-value');
                    localStorage.setItem('data-bs-theme', nextTheme);
                    updateThemeUi(nextTheme);
                });
            });

            if (typeof systemQuery.addEventListener === 'function') {
                systemQuery.addEventListener('change', function() {
                    if (getStoredTheme() === 'system') {
                        updateThemeUi('system');
                    }
                });
            }

            updateThemeUi(getStoredTheme());
        })();

        (function() {
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(element) {
                    new bootstrap.Tooltip(element);
                });
            }
        })();

        (function() {
            var lastUnread = 0;

            function loadAdminNotifications() {
                $.getJSON(BASE_URL + 'api/notifications', function(res) {
                    if (!res || res.error) {
                        return;
                    }

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
                        res.notifications.forEach(function(notification) {
                            html += '<a href="' + (notification.url || '#') + '" class="d-flex align-items-start gap-3 p-4 border-bottom border-gray-200 text-decoration-none ' + (notification.is_read ? 'opacity-75' : '') + '">' +
                                '<div class="flex-shrink-0 mt-1"><span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width:34px;height:34px;"><i class="fa-solid fa-bell fs-8"></i></span></div>' +
                                '<div class="min-w-0"><div class="fw-semibold text-gray-900 fs-7 text-truncate">' + notification.title + '</div><div class="text-muted fs-8">' + notification.message + '</div><div class="text-muted fs-9 mt-1">' + notification.created_at + '</div></div></a>';
                        });
                    } else {
                        html = '<div class="text-center py-8 text-muted fs-7"><i class="fa-solid fa-bell-slash fs-2 d-block mb-3"></i>Tidak ada notifikasi</div>';
                    }

                    $('#admin_notif_list').html(html);
                });
            }

            loadAdminNotifications();
            setInterval(loadAdminNotifications, 30000);

            $('#admin_mark_all_read').on('click', function(event) {
                event.preventDefault();
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