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
?>
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Riwayat Servis</h1>
</div>

<!-- Filter tabs -->
<div class="d-flex overflow-auto mb-5">
    <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold flex-nowrap">
        <li class="nav-item">
            <a class="nav-link text-active-primary py-2 <?= empty($status) ? 'active' : '' ?>"
                href="<?= base_url('my/history') ?>">Semua</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-active-primary py-2 <?= $status === 'pending' ? 'active' : '' ?>"
                href="<?= base_url('my/history?status=pending') ?>">Menunggu</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-active-primary py-2 <?= $status === 'in_progress' ? 'active' : '' ?>"
                href="<?= base_url('my/history?status=in_progress') ?>">Diservis</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-active-primary py-2 <?= $status === 'completed' ? 'active' : '' ?>"
                href="<?= base_url('my/history?status=completed') ?>">Selesai</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-active-primary py-2 <?= $status === 'cancelled' ? 'active' : '' ?>"
                href="<?= base_url('my/history?status=cancelled') ?>">Dibatalkan</a>
        </li>
    </ul>
</div>

<div class="card card-flush">
    <div class="card-body p-0">
        <?php if (empty($bookings)): ?>
            <div class="text-center py-15">
                <i class="fa-regular fa-calendar-xmark fs-3x text-muted mb-5"></i>
                <p class="text-muted fs-6">Tidak ada data riwayat servis.</p>
                <a href="<?= base_url('my/booking') ?>" class="btn btn-primary btn-sm">Booking Sekarang</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-150px rounded-start">Kode Booking</th>
                            <th class="min-w-130px">Layanan</th>
                            <th class="min-w-100px">Kendaraan</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-80px text-end rounded-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <?php $si = $status_map[$b->status] ?? ['secondary', ucfirst($b->status)]; ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-dark fw-bold text-hover-primary d-block fs-7"><?= htmlspecialchars($b->booking_code) ?></span>
                                    <span class="text-muted fs-8"><?= date('H:i', strtotime($b->created_at)) ?></span>
                                </td>
                                <td class="text-gray-700 fs-7"><?= htmlspecialchars($b->service_name) ?></td>
                                <td>
                                    <span class="badge badge-light-secondary"><?= htmlspecialchars($b->plate_number) ?></span>
                                </td>
                                <td class="text-gray-600 fs-7"><?= date('d/m/Y', strtotime($b->booking_date)) ?></td>
                                <td><span class="badge badge-light-<?= $si[0] ?>"><?= $si[1] ?></span></td>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url('my/booking/' . $b->id) ?>" class="btn btn-sm btn-light-primary">
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