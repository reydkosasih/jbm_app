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
    <h1 class="fs-2hx fw-bold">Semua Booking</h1>
</div>

<!-- Filters -->
<div class="card card-flush mb-5">
    <div class="card-body py-4">
        <form method="get" action="" class="d-flex flex-wrap gap-3 align-items-end">
            <div>
                <label class="form-label fs-7 mb-1">Status</label>
                <select name="status" class="form-select form-select-sm w-160px">
                    <option value="">Semua Status</option>
                    <?php foreach ($status_map as $key => $val): ?>
                        <option value="<?= $key ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>><?= $val[1] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="form-label fs-7 mb-1">Tanggal</label>
                <input type="date" name="date" class="form-control form-control-sm" value="<?= htmlspecialchars($filters['date'] ?? '') ?>" />
            </div>
            <div>
                <label class="form-label fs-7 mb-1">Cari</label>
                <input type="text" name="q" class="form-control form-control-sm w-200px" placeholder="Kode, nama, plat..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />
            </div>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-magnifying-glass me-1"></i>Filter
            </button>
            <a href="<?= base_url('admin/bookings') ?>" class="btn btn-sm btn-light">Reset</a>
        </form>
    </div>
</div>

<div class="card card-flush">
    <div class="card-body p-0">
        <?php if (empty($bookings)): ?>
            <div class="text-center py-15">
                <i class="fa-regular fa-calendar-xmark fs-3x text-muted mb-5"></i>
                <p class="text-muted fs-6">Tidak ada data booking.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-130px rounded-start">Kode Booking</th>
                            <th class="min-w-150px">Customer</th>
                            <th class="min-w-100px">Kendaraan</th>
                            <th class="min-w-120px">Layanan</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="min-w-80px">Jam</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-80px text-end rounded-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                            <?php $si = $status_map[$b->status] ?? ['secondary', $b->status]; ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-dark fw-bold text-hover-primary fs-7"><?= htmlspecialchars($b->booking_code) ?></span>
                                </td>
                                <td>
                                    <span class="fw-semibold fs-7 d-block"><?= htmlspecialchars($b->customer_name) ?></span>
                                    <span class="text-muted fs-8"><?= htmlspecialchars($b->customer_phone ?? '') ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-light-secondary fs-8"><?= htmlspecialchars($b->plate_number) ?></span>
                                </td>
                                <td class="text-gray-700 fs-7"><?= htmlspecialchars($b->service_name) ?></td>
                                <td class="text-gray-600 fs-7"><?= date('d/m/Y', strtotime($b->booking_date)) ?></td>
                                <td class="text-gray-600 fs-8"><?= htmlspecialchars($b->slot_label) ?></td>
                                <td><span class="badge badge-light-<?= $si[0] ?>"><?= $si[1] ?></span></td>
                                <td class="text-end pe-4">
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