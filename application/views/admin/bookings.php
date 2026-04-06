<?php
$status_map = [
    'pending'              => ['warning',  'Menunggu'],
    'confirmed'            => ['primary',  'Dikonfirmasi'],
    'in_progress'          => ['info',     'Diservis'],
    'waiting_payment'      => ['warning',  'Tunggu Bayar'],
    'waiting_confirmation' => ['warning',  'Konfirmasi Bayar'],
    'completed'            => ['success',  'Selesai'],
    'cancelled'            => ['danger',   'Dibatalkan'],
];

$visible_total = count($bookings);
$waiting_total = 0;
$active_total = 0;
$completed_total = 0;
$today_total = 0;
$today_date = date('Y-m-d');

foreach ($bookings as $booking_item) {
    if (in_array($booking_item->status, ['pending', 'waiting_payment', 'waiting_confirmation'], true)) {
        $waiting_total++;
    }

    if (in_array($booking_item->status, ['confirmed', 'in_progress'], true)) {
        $active_total++;
    }

    if ($booking_item->status === 'completed') {
        $completed_total++;
    }

    if (!empty($booking_item->booking_date) && $booking_item->booking_date === $today_date) {
        $today_total++;
    }
}
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">Operational workspace</div>
            <h2 class="admin-page-hero__title fw-bold">Pantau booking harian, status servis, dan antrean pelanggan tanpa kehilangan detail penting.</h2>
            <p class="admin-page-hero__desc">Halaman booking dirapikan agar admin lebih cepat menyaring status, mengecek jadwal servis, dan masuk ke detail booking dari perangkat desktop maupun mobile.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-list-check"></i><?= $visible_total ?> booking tampil</span>
                <span class="admin-chip"><i class="fa-solid fa-clock"></i><?= $waiting_total ?> butuh tindak lanjut</span>
                <span class="admin-chip"><i class="fa-solid fa-calendar-day"></i><?= $today_total ?> jadwal hari ini</span>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-calendar-check fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Booking tampil</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $visible_total ?></div>
                        <div class="text-muted fs-7 mt-2">Menyesuaikan filter yang aktif.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(245, 158, 11, 0.16); color: #d97706;"><i class="fa-solid fa-hourglass-half fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Perlu respon</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $waiting_total ?></div>
                        <div class="text-muted fs-7 mt-2">Pending, tunggu bayar, atau konfirmasi.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(14, 165, 233, 0.16); color: #0284c7;"><i class="fa-solid fa-screwdriver-wrench fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Servis aktif</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $active_total ?></div>
                        <div class="text-muted fs-7 mt-2">Booking yang sedang berjalan hari ini.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-circle-check fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Selesai</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $completed_total ?></div>
                        <div class="text-muted fs-7 mt-2">Booking yang sudah tuntas pada hasil filter.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="admin-surface-card mb-6">
    <div class="card-body p-5 p-lg-7">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-4 mb-4">
            <div>
                <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Filter booking</div>
                <h3 class="fw-bold mb-0">Saring booking berdasarkan status, tanggal, dan kata kunci</h3>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="admin-chip"><i class="fa-solid fa-filter"></i><?= !empty($filters['status']) ? htmlspecialchars($status_map[$filters['status']][1] ?? $filters['status']) : 'Semua status' ?></span>
                <span class="admin-chip"><i class="fa-solid fa-magnifying-glass"></i><?= !empty($filters['search']) ? htmlspecialchars($filters['search']) : 'Tanpa keyword' ?></span>
            </div>
        </div>
        <form method="get" action="" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label fw-semibold">Cari booking</label>
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass text-muted position-absolute top-50 translate-middle-y ms-4"></i>
                    <input type="text" name="q" class="form-control form-control-lg ps-12" placeholder="Kode booking, nama customer, plat nomor..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select form-select-lg">
                    <option value="">Semua Status</option>
                    <?php foreach ($status_map as $key => $val): ?>
                        <option value="<?= $key ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>><?= $val[1] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 col-lg-3">
                <label class="form-label fw-semibold">Tanggal booking</label>
                <input type="date" name="date" class="form-control form-control-lg" value="<?= htmlspecialchars($filters['date'] ?? '') ?>" />
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg flex-fill">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Filter
                    </button>
                    <a href="<?= base_url('admin/bookings') ?>" class="btn btn-light btn-lg">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-surface-card overflow-hidden">
    <div class="card-header border-0 pt-6 pb-0 px-5 px-lg-7">
        <div>
            <h3 class="fw-bold mb-1">Daftar booking</h3>
            <div class="admin-table-meta">Pantau jadwal servis, status pengerjaan, dan buka detail booking dengan alur yang lebih rapi.</div>
        </div>
    </div>
    <div class="card-body p-0 pt-5">
        <?php if (empty($bookings)): ?>
            <div class="text-center py-15 px-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary mb-5" style="width: 88px; height: 88px;">
                    <i class="fa-regular fa-calendar-xmark fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Tidak ada booking pada filter ini</h4>
                <p class="text-muted fs-6 mb-0">Coba ubah status, tanggal, atau kata kunci pencarian untuk menampilkan data booking lain.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-0 fs-7 mb-0 admin-data-table">
                    <thead>
                        <tr>
                            <th class="ps-5">Kode booking</th>
                            <th>Customer</th>
                            <th>Kendaraan</th>
                            <th>Layanan</th>
                            <th>Jadwal</th>
                            <th>Status</th>
                            <th class="text-end pe-5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <?php $si = $status_map[$b->status] ?? ['secondary', $b->status]; ?>
                            <tr>
                                <td class="ps-5">
                                    <div class="fw-bold fs-6 text-gray-900"><?= htmlspecialchars($b->booking_code) ?></div>
                                    <div class="admin-table-meta mt-1">Dibuat <?= !empty($b->created_at) ? date('d M Y, H:i', strtotime($b->created_at)) : '-' ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="symbol symbol-42px">
                                            <div class="symbol-label bg-light-primary text-primary fw-bold">
                                                <?= strtoupper(substr($b->customer_name ?? 'C', 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-gray-900"><?= htmlspecialchars($b->customer_name) ?></div>
                                            <div class="admin-table-meta mt-1"><?= htmlspecialchars($b->customer_phone ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-secondary fs-8 px-4 py-2"><?= htmlspecialchars($b->plate_number) ?></span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-gray-900"><?= htmlspecialchars($b->service_name) ?></div>
                                    <div class="admin-table-meta mt-1">Antrean servis aktif</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-gray-900"><?= !empty($b->booking_date) ? date('d M Y', strtotime($b->booking_date)) : '-' ?></div>
                                    <div class="admin-table-meta mt-1"><?= htmlspecialchars($b->slot_label) ?></div>
                                </td>
                                <td><span class="badge badge-light-<?= $si[0] ?> px-4 py-2"><?= $si[1] ?></span></td>
                                <td class="text-end pe-5">
                                    <a href="<?= base_url('admin/booking/' . $b->id) ?>" class="btn btn-icon btn-sm btn-light-primary">
                                        <i class="fa-solid fa-eye fs-7"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>