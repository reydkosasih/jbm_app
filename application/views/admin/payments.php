<?php
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$tab = $filters['status'] ?? '';
?>
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Manajemen Pembayaran</h1>
</div>

<!-- Tab Filters -->
<ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
    <li class="nav-item">
        <a class="nav-link <?= $tab === '' ? 'active' : '' ?>" href="<?= base_url('admin/payments') ?>">Semua</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'unpaid' ? 'active' : '' ?>" href="<?= base_url('admin/payments?status=unpaid') ?>">Menunggu Konfirmasi</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $tab === 'paid' ? 'active' : '' ?>" href="<?= base_url('admin/payments?status=paid') ?>">Lunas</a>
    </li>
</ul>

<div class="card card-flush">
    <div class="card-body p-0">
        <?php if (empty($payments)): ?>
            <div class="text-center py-15 text-muted">
                <i class="fa-solid fa-file-invoice-dollar fs-3x mb-4"></i>
                <p>Tidak ada data pembayaran.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 fs-7">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 rounded-start">No. Invoice</th>
                            <th>Kode Booking</th>
                            <th>Customer</th>
                            <th class="text-end">Total</th>
                            <th>Metode</th>
                            <th>Bukti</th>
                            <th>Status</th>
                            <th class="text-end rounded-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p): ?>
                            <tr>
                                <td class="ps-4 fw-bold"><?= htmlspecialchars($p->invoice_number) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/booking/' . $p->booking_id) ?>" class="text-primary fw-semibold">
                                        <?= htmlspecialchars($p->booking_code) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($p->customer_name) ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($p->total_amount, 0, ',', '.') ?></td>
                                <td><?= ucfirst($p->method ?? '-') ?></td>
                                <td>
                                    <?php if (!empty($p->proof_file)): ?>
                                        <a href="<?= base_url('uploads/payment_proofs/' . $p->proof_file) ?>" target="_blank" class="btn btn-xs btn-light-info">
                                            <i class="fa-solid fa-image me-1"></i>Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-light-<?= $p->status === 'paid' ? 'success' : 'warning' ?>">
                                        <?= $p->status === 'paid' ? 'Lunas' : 'Belum Lunas' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="<?= base_url('admin/invoice/' . $p->id . '/print') ?>" target="_blank" class="btn btn-icon btn-sm btn-light" title="Cetak">
                                            <i class="fa-solid fa-print fs-8"></i>
                                        </a>
                                        <?php if ($p->status !== 'paid'): ?>
                                            <button class="btn btn-icon btn-sm btn-light-success btn-confirm" data-id="<?= $p->id ?>" title="Konfirmasi">
                                                <i class="fa-solid fa-check fs-8"></i>
                                            </button>
                                            <button class="btn btn-icon btn-sm btn-light-danger btn-reject" data-id="<?= $p->id ?>" title="Tolak">
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
    const CSRF_NAME = '<?= $csrf_name ?>';
    let CSRF_HASH = '<?= $csrf_hash ?>';

    function csrfData(extra) {
        const d = {};
        d[CSRF_NAME] = CSRF_HASH;
        return Object.assign(d, extra || {});
    }

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
                    headers: {
                        'X-CSRF-Token': CSRF_HASH
                    },
                    data: csrfData(),
                    success: r => {
                        CSRF_HASH = r.csrf_hash || CSRF_HASH;
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
                    headers: {
                        'X-CSRF-Token': CSRF_HASH
                    },
                    data: csrfData({
                        note: res.value
                    }),
                    success: r => {
                        CSRF_HASH = r.csrf_hash || CSRF_HASH;
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