<?php
$tab = $filters['status'] ?? '';
$status_map = [
    '' => ['Semua status', 'primary'],
    'waiting_confirmation' => ['Menunggu Konfirmasi', 'warning'],
    'paid' => ['Lunas', 'success'],
    'unpaid' => ['Belum Dibayar', 'secondary'],
];
$status_badges = [
    'waiting_confirmation' => ['warning', 'Menunggu Konfirmasi'],
    'paid' => ['success', 'Lunas'],
    'unpaid' => ['secondary', 'Belum Dibayar'],
];

$visible_total = count($payments);
$visible_paid = 0;
$visible_waiting = 0;
$visible_unpaid = 0;
$proof_uploaded = 0;
$visible_revenue = 0;

foreach ($payments as $payment_item) {
    $visible_revenue += (float) $payment_item->total_amount;
    if (!empty($payment_item->proof_file)) {
        $proof_uploaded++;
    }

    if ($payment_item->status === 'paid') {
        $visible_paid++;
    } elseif ($payment_item->status === 'waiting_confirmation') {
        $visible_waiting++;
    } else {
        $visible_unpaid++;
    }
}
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">Finance workspace</div>
            <h2 class="admin-page-hero__title fw-bold">Kelola pembayaran dengan alur review yang lebih cepat dan rapi.</h2>
            <p class="admin-page-hero__desc">Tampilan ini disusun ulang agar kasir dan admin lebih mudah memantau invoice, memeriksa bukti transfer, dan menyelesaikan konfirmasi tanpa kehilangan konteks booking pelanggan.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <?php foreach ($status_map as $value => $meta): ?>
                    <a href="<?= base_url('admin/payments' . ($value !== '' ? '?status=' . urlencode($value) : '')) ?>" class="btn <?= $tab === $value ? 'btn-primary' : 'btn-light-primary' ?> rounded-pill px-5">
                        <?= htmlspecialchars($meta[0]) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-file-invoice-dollar fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Invoice tampil</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $visible_total ?></div>
                        <div class="text-muted fs-7 mt-2">Sesuai filter aktif saat ini.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(245, 158, 11, 0.16); color: #d97706;"><i class="fa-solid fa-hourglass-half fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Perlu review</div>
                        <div class="fs-2hx fw-bold mt-1"><?= (int) ($pending_count ?? $visible_waiting) ?></div>
                        <div class="text-muted fs-7 mt-2">Pembayaran menunggu verifikasi admin.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-wallet fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Sudah lunas</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $visible_paid ?></div>
                        <div class="text-muted fs-7 mt-2">Transaksi selesai pada daftar terfilter.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(99, 102, 241, 0.16); color: #4f46e5;"><i class="fa-solid fa-receipt fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Nominal terpantau</div>
                        <div class="fs-3 fw-bold mt-1">Rp <?= number_format($visible_revenue, 0, ',', '.') ?></div>
                        <div class="text-muted fs-7 mt-2"><?= $proof_uploaded ?> bukti bayar telah diunggah.</div>
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
                <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Filter pembayaran</div>
                <h3 class="fw-bold mb-0">Cari invoice, rentang tanggal, dan status transaksi</h3>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="admin-chip"><i class="fa-solid fa-filter"></i><?= htmlspecialchars($status_map[$tab][0] ?? 'Semua status') ?></span>
                <span class="admin-chip"><i class="fa-solid fa-clipboard-check"></i><?= $visible_total ?> hasil</span>
                <span class="admin-chip"><i class="fa-solid fa-clock"></i><?= $visible_waiting ?> pending pada hasil filter</span>
            </div>
        </div>
        <form method="get" action="" class="row g-3 align-items-end">
            <div class="col-lg-4">
                <label class="form-label fw-semibold">Cari invoice atau customer</label>
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass text-muted position-absolute top-50 translate-middle-y ms-4"></i>
                    <input type="text" name="q" class="form-control form-control-lg ps-12" placeholder="INV, kode booking, nama customer..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" />
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select form-select-lg">
                    <?php foreach ($status_map as $value => $meta): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $tab === $value ? 'selected' : '' ?>><?= htmlspecialchars($meta[0]) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 col-lg-2">
                <label class="form-label fw-semibold">Dari tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-lg" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>" />
            </div>
            <div class="col-md-4 col-lg-2">
                <label class="form-label fw-semibold">Sampai tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-lg" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>" />
            </div>
            <div class="col-md-8 col-lg-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg flex-fill">
                        <i class="fa-solid fa-magnifying-glass me-2"></i>Terapkan
                    </button>
                    <a href="<?= base_url('admin/payments') ?>" class="btn btn-light btn-lg">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="admin-surface-card overflow-hidden">
    <div class="card-header border-0 pt-6 pb-0 px-5 px-lg-7">
        <div>
            <h3 class="fw-bold mb-1">Daftar transaksi</h3>
            <div class="admin-table-meta">Tinjau bukti pembayaran, cek kaitan booking, lalu konfirmasi atau tolak bila diperlukan.</div>
        </div>
    </div>
    <div class="card-body p-0 pt-5">
        <?php if (empty($payments)): ?>
            <div class="text-center py-15 px-5">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary mb-5" style="width: 88px; height: 88px;">
                    <i class="fa-solid fa-file-invoice-dollar fs-1"></i>
                </div>
                <h4 class="fw-bold mb-3">Belum ada transaksi pada filter ini</h4>
                <p class="text-muted fs-6 mb-0">Coba ubah status, tanggal, atau kata kunci pencarian untuk melihat invoice lain.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle gs-0 gy-0 fs-7 mb-0 admin-data-table">
                    <thead>
                        <tr>
                            <th class="ps-5">Invoice</th>
                            <th>Booking</th>
                            <th>Customer</th>
                            <th class="text-end">Nominal</th>
                            <th>Metode</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th class="text-end pe-5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <?php $badge = $status_badges[$p->status] ?? ['secondary', ucfirst($p->status)]; ?>
                            <tr>
                                <td class="ps-5">
                                    <div class="fw-bold fs-6 text-gray-900"><?= htmlspecialchars($p->invoice_number) ?></div>
                                    <div class="admin-table-meta mt-1">Dibuat <?= !empty($p->created_at) ? date('d M Y, H:i', strtotime($p->created_at)) : '-' ?></div>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/booking/' . $p->booking_id) ?>" class="fw-semibold text-primary d-block text-hover-primary">
                                        <?= htmlspecialchars($p->booking_code) ?>
                                    </a>
                                    <div class="admin-table-meta mt-1"><?= !empty($p->booking_date) ? date('d M Y', strtotime($p->booking_date)) : '-' ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="symbol symbol-42px">
                                            <div class="symbol-label bg-light-primary text-primary fw-bold">
                                                <?= strtoupper(substr($p->customer_name ?? 'C', 0, 1)) ?>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-gray-900"><?= htmlspecialchars($p->customer_name) ?></div>
                                            <div class="admin-table-meta mt-1"><?= htmlspecialchars($p->plate_number ?? '-') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold fs-6 text-gray-900">Rp <?= number_format($p->total_amount, 0, ',', '.') ?></div>
                                    <div class="admin-table-meta mt-1"><?= htmlspecialchars($p->service_name ?? '-') ?></div>
                                </td>
                                <td>
                                    <span class="badge badge-light-info text-capitalize"><?= htmlspecialchars($p->method ?: '-') ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($p->proof_file)): ?>
                                        <a href="<?= base_url('uploads/payment_proofs/' . $p->proof_file) ?>" target="_blank" class="btn btn-sm btn-light-info rounded-pill px-4">
                                            <i class="fa-solid fa-image me-2"></i>Lihat bukti
                                        </a>
                                    <?php else: ?>
                                        <span class="admin-chip"><i class="fa-regular fa-image"></i>Belum ada</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-light-<?= $badge[0] ?> px-4 py-2"><?= htmlspecialchars($badge[1]) ?></span>
                                </td>
                                <td class="text-end pe-5">
                                    <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-end">
                                        <a href="<?= base_url('admin/invoice/' . $p->id . '/print') ?>" target="_blank" class="btn btn-icon btn-sm btn-light" title="Cetak invoice">
                                            <i class="fa-solid fa-print fs-8"></i>
                                        </a>
                                        <?php if ($p->status !== 'paid'): ?>
                                            <button class="btn btn-icon btn-sm btn-light-success btn-confirm" data-id="<?= $p->id ?>" title="Konfirmasi pembayaran">
                                                <i class="fa-solid fa-check fs-8"></i>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-light-danger btn-reject" data-id="<?= $p->id ?>" title="Tolak pembayaran">
                                                <i class="fa-solid fa-times fs-8"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-confirm').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi pembayaran?',
                showCancelButton: true,
                confirmButtonText: 'Ya, konfirmasi'
            }).then(res => {
                if (!res.isConfirmed) return;
                $.ajax({
                    url: BASE_URL + 'admin/payment/' + id + '/confirm',
                    method: 'POST',
                    success: r => {
                        if (r.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Dikonfirmasi!',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1250);
                        } else Swal.fire({
                            icon: 'error',
                            text: r.message
                        });
                    }
                });
            });
        });
    });

    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                icon: 'warning',
                title: 'Tolak pembayaran',
                html: '<input id="rejectNote" class="swal2-input" placeholder="Alasan penolakan...">',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                preConfirm: () => document.getElementById('rejectNote').value
            }).then(res => {
                if (!res.isConfirmed) return;
                $.ajax({
                    url: BASE_URL + 'admin/payment/' + id + '/reject',
                    method: 'POST',
                    data: {
                        note: res.value
                    },
                    success: r => {
                        if (r.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ditolak',
                                timer: 1200,
                                showConfirmButton: false
                            });
                            setTimeout(() => location.reload(), 1250);
                        } else Swal.fire({
                            icon: 'error',
                            text: r.message
                        });
                    }
                });
            });
        });
    });
</script>