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
$allowed_transitions = [
    'pending'              => ['confirmed', 'cancelled'],
    'confirmed'            => ['in_progress', 'cancelled'],
    'in_progress'          => ['waiting_payment'],
    'waiting_payment'      => ['waiting_confirmation'],
    'waiting_confirmation' => ['completed'],
    'completed'            => [],
    'cancelled'            => [],
];
$si      = $status_map[$booking->status] ?? ['secondary', $booking->status];
$next    = $allowed_transitions[$booking->status] ?? [];
$steps = ['pending', 'confirmed', 'in_progress', 'waiting_payment', 'completed'];
$step_labels = ['Menunggu', 'Dikonfirmasi', 'Diservis', 'Tunggu Bayar', 'Selesai'];
$current_step = array_search($booking->status, $steps);
if ($current_step === false) $current_step = -1;

$order_total = 0;
foreach ($service_orders as $service_order_item) {
    $order_total += ((float) $service_order_item->quantity * (float) $service_order_item->unit_price);
}

$timeline_total = count($status_logs);
$customer_vehicle = trim(($booking->brand ?? '') . ' ' . ($booking->vehicle_model ?? ''));
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-8">
            <a href="<?= base_url('admin/bookings') ?>" class="btn btn-sm btn-light-primary rounded-pill px-4 mb-4">
                <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke daftar booking
            </a>
            <div class="admin-page-hero__eyebrow">Booking workspace</div>
            <h2 class="admin-page-hero__title fw-bold">Pantau progres booking <?= htmlspecialchars($booking->booking_code) ?> dari penerimaan hingga pembayaran.</h2>
            <p class="admin-page-hero__desc">Halaman detail ini dirapikan agar admin bisa membaca profil pelanggan, order servis, timeline status, dan tindakan pembayaran dalam satu alur yang lebih terstruktur.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-user"></i><?= htmlspecialchars($booking->customer_name) ?></span>
                <span class="admin-chip"><i class="fa-solid fa-car-side"></i><?= htmlspecialchars($booking->plate_number) ?></span>
                <span class="admin-chip"><i class="fa-solid fa-screwdriver-wrench"></i><?= htmlspecialchars($booking->service_name) ?></span>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="row g-3">
                <div class="col-sm-6 col-xl-12">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-file-invoice fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Status booking</div>
                        <div class="mt-2"><span class="badge badge-light-<?= $si[0] ?> fs-6 px-4 py-2"><?= $si[1] ?></span></div>
                        <div class="text-muted fs-7 mt-3">Booking pada tanggal <?= date('d M Y', strtotime($booking->booking_date)) ?> pukul <?= htmlspecialchars($booking->slot_label) ?>.</div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-12">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-wallet fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Nilai order</div>
                        <div class="fs-3 fw-bold mt-1">Rp <?= number_format($order_total, 0, ',', '.') ?></div>
                        <div class="text-muted fs-7 mt-2"><?= count($service_orders) ?> item servis dan sparepart, <?= $timeline_total ?> log status tercatat.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($booking->status !== 'cancelled'): ?>
    <div class="admin-surface-card mb-6">
        <div class="card-body p-5 p-lg-7">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-4 mb-5">
                <div>
                    <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Progress booking</div>
                    <h3 class="fw-bold mb-0">Tahapan pengerjaan kendaraan</h3>
                </div>
                <span class="admin-chip"><i class="fa-solid fa-list-check"></i><?= max($current_step + 1, 1) ?> dari <?= count($steps) ?> tahap</span>
            </div>
            <div class="stepper stepper-links d-flex justify-content-center">
                <?php foreach ($steps as $i => $step): ?>
                    <?php $done = $i < $current_step;
                    $active = $i === $current_step; ?>
                    <div class="stepper-item <?= $active ? 'current' : ($done ? 'completed' : '') ?> mx-4 text-center">
                        <div class="stepper-icon w-40px h-40px mb-2 mx-auto <?= $active ? 'bg-primary' : ($done ? 'bg-success' : 'bg-light') ?> rounded-circle d-flex align-items-center justify-content-center">
                            <?php if ($done): ?>
                                <i class="fa-solid fa-check fs-6 text-white"></i>
                            <?php else: ?>
                                <span class="fs-7 fw-bold <?= $active ? 'text-white' : 'text-muted' ?>"><?= $i + 1 ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="fs-8 <?= $active ? 'fw-bold text-primary' : ($done ? 'text-success' : 'text-muted') ?>"><?= $step_labels[$i] ?></span>
                    </div>
                    <?php if ($i < count($steps) - 1): ?>
                        <div class="d-flex align-items-center pb-4" style="width:40px">
                            <hr class="w-100 opacity-25">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-5">
    <div class="col-lg-8">

        <div class="card card-flush mb-5">
            <div class="card-header">
                <h3 class="card-title">Informasi Booking</h3>
            </div>
            <div class="card-body">
                <div class="row g-4 fs-7">
                    <div class="col-sm-6">
                        <span class="text-muted d-block mb-1">Kode Booking</span>
                        <span class="fw-bold"><?= htmlspecialchars($booking->booking_code) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block mb-1">Customer</span>
                        <span class="fw-bold"><?= htmlspecialchars($booking->customer_name) ?></span>
                        <span class="text-muted d-block"><?= htmlspecialchars($booking->customer_phone ?? '') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block mb-1">Kendaraan</span>
                        <span class="fw-bold"><?= htmlspecialchars($booking->plate_number) ?></span>
                        <span class="text-muted d-block"><?= htmlspecialchars($customer_vehicle !== '' ? $customer_vehicle : '-') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block mb-1">Layanan</span>
                        <span class="fw-bold"><?= htmlspecialchars($booking->service_name) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block mb-1">Tanggal</span>
                        <span class="fw-bold"><?= date('d F Y', strtotime($booking->booking_date)) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted d-block mb-1">Jam</span>
                        <span class="fw-bold"><?= htmlspecialchars($booking->slot_label) ?></span>
                    </div>
                    <?php if (!empty($booking->notes)): ?>
                        <div class="col-12">
                            <span class="text-muted d-block mb-1">Catatan Customer</span>
                            <span class="fw-semibold"><?= nl2br(htmlspecialchars($booking->notes)) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card card-flush mb-5">
            <div class="card-header">
                <h3 class="card-title">Pesanan Servis / Sparepart</h3>
                <?php if (in_array($booking->status, ['confirmed', 'in_progress'])): ?>
                    <div class="card-toolbar gap-2">
                        <button class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#modalAddOrder">
                            <i class="fa-solid fa-plus me-1"></i>Tambah Item
                        </button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <?php if (empty($service_orders)): ?>
                    <div class="text-center py-10 px-5 text-muted fs-7">Belum ada item servis atau sparepart.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-0 fs-7 mb-0 admin-data-table">
                            <thead>
                                <tr>
                                    <th class="ps-5">Deskripsi</th>
                                    <th>Tipe</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                    <?php if (in_array($booking->status, ['confirmed', 'in_progress'])): ?>
                                        <th class="text-end pe-5">Hapus</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($service_orders as $so): $subtotal = $so->quantity * $so->unit_price; ?>
                                    <tr>
                                        <td class="ps-5">
                                            <div class="fw-semibold text-gray-900"><?= htmlspecialchars($so->description) ?></div>
                                        </td>
                                        <td><span class="badge badge-light-<?= $so->type === 'jasa' ? 'info' : 'warning' ?> px-4 py-2"><?= $so->type === 'jasa' ? 'Jasa' : 'Sparepart' ?></span></td>
                                        <td class="text-end"><?= $so->quantity ?></td>
                                        <td class="text-end">Rp <?= number_format($so->unit_price, 0, ',', '.') ?></td>
                                        <td class="text-end fw-bold">Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
                                        <?php if (in_array($booking->status, ['confirmed', 'in_progress'])): ?>
                                            <td class="text-end pe-5">
                                                <button class="btn btn-icon btn-sm btn-light-danger btn-del-order" data-id="<?= $so->id ?>">
                                                    <i class="fa-solid fa-trash fs-8"></i>
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="fw-bold fs-6">
                                    <td class="ps-5" colspan="<?= in_array($booking->status, ['confirmed', 'in_progress']) ? 4 : 4 ?>">Total</td>
                                    <td class="text-end">Rp <?= number_format($order_total, 0, ',', '.') ?></td>
                                    <?php if (in_array($booking->status, ['confirmed', 'in_progress'])): ?><td></td><?php endif; ?>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <?php if ($booking->status === 'in_progress' && !empty($service_orders) && empty($payment)): ?>
                <div class="card-footer d-flex justify-content-end">
                    <button class="btn btn-success" id="btnGenerateInvoice">
                        <i class="fa-solid fa-file-invoice-dollar me-2"></i>Generate Invoice
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <?php if (in_array($booking->status, ['confirmed', 'in_progress', 'waiting_payment', 'waiting_confirmation', 'completed'])): ?>
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <h3 class="card-title">Catatan Mekanik</h3>
                </div>
                <div class="card-body">
                    <textarea id="mechanicNote" class="form-control" rows="3"><?= htmlspecialchars($booking->mechanic_note ?? '') ?></textarea>
                    <div class="text-end mt-3">
                        <button class="btn btn-sm btn-primary" id="btnSaveNote">Simpan Catatan</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card card-flush mb-5">
            <div class="card-header">
                <h3 class="card-title">Riwayat Status</h3>
            </div>
            <div class="card-body">
                <?php if (empty($status_logs)): ?>
                    <p class="text-muted fs-7">Belum ada log.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($status_logs as $log):
                            $lsi = $status_map[$log->status] ?? ['secondary', $log->status]; ?>
                            <div class="timeline-item">
                                <div class="timeline-line w-10px"></div>
                                <div class="timeline-icon symbol symbol-circle symbol-10px me-4">
                                    <div class="symbol-label bg-<?= $lsi[0] ?>"></div>
                                </div>
                                <div class="timeline-content mb-5">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-semibold fs-7"><?= $lsi[1] ?></span>
                                        <span class="text-muted fs-8"><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></span>
                                    </div>
                                    <?php if (!empty($log->note)): ?>
                                        <p class="text-muted fs-8 mb-0"><?= htmlspecialchars($log->note) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        <?php if (!empty($next)): ?>
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <h3 class="card-title">Update Status</h3>
                </div>
                <div class="card-body">
                    <form id="formStatus">
                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select class="form-select" name="status" id="selectStatus">
                                <?php foreach ($next as $ns):
                                    $nsi = $status_map[$ns] ?? ['secondary', $ns]; ?>
                                    <option value="<?= $ns ?>"><?= $nsi[1] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan (opsional)</label>
                            <textarea class="form-control" name="admin_note" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <div class="card card-flush mb-5">
            <div class="card-header">
                <h3 class="card-title">Pembayaran</h3>
            </div>
            <div class="card-body fs-7">
                <?php if (empty($payment)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-file-invoice fs-3x mb-3"></i>
                        <p class="mb-0">Belum ada invoice.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">No. Invoice</span>
                            <span class="fw-bold"><?= htmlspecialchars($payment->invoice_number) ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total</span>
                            <span class="fw-bold text-primary">Rp <?= number_format($payment->total_amount, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Metode</span>
                            <span class="fw-semibold"><?= ucfirst($payment->method ?? '-') ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Status</span>
                            <span class="badge badge-light-<?= $payment->status === 'paid' ? 'success' : 'warning' ?> px-4 py-2">
                                <?= $payment->status === 'paid' ? 'Lunas' : 'Belum Lunas' ?>
                            </span>
                        </div>
                        <?php if (!empty($payment->proof_file)): ?>
                            <div>
                                <a href="<?= base_url('uploads/payment_proofs/' . $payment->proof_file) ?>" target="_blank" class="btn btn-sm btn-light w-100">
                                    <i class="fa-solid fa-image me-1"></i>Lihat Bukti Transfer
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="separator my-4"></div>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('admin/invoice/' . $payment->id . '/print') ?>" target="_blank" class="btn btn-sm btn-light flex-fill">
                            <i class="fa-solid fa-print me-1"></i>Cetak
                        </a>
                        <a href="<?= base_url('admin/invoice/' . $payment->id . '/pdf') ?>" target="_blank" class="btn btn-sm btn-light-primary flex-fill">
                            <i class="fa-solid fa-file-pdf me-1"></i>PDF
                        </a>
                    </div>
                    <?php if ($payment->status !== 'paid'): ?>
                        <div class="d-flex gap-2 mt-2">
                            <button class="btn btn-sm btn-success flex-fill btn-confirm-pay" data-id="<?= $payment->id ?>">
                                <i class="fa-solid fa-check me-1"></i>Konfirmasi
                            </button>
                            <button class="btn btn-sm btn-danger flex-fill btn-reject-pay" data-id="<?= $payment->id ?>">
                                <i class="fa-solid fa-times me-1"></i>Tolak
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Add Service Order -->
<div class="modal fade" id="modalAddOrder" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Item Servis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label required">Tipe</label>
                    <div class="d-flex gap-4">
                        <label class="d-flex align-items-center gap-2">
                            <input type="radio" name="order_type" value="jasa" checked class="form-check-input mt-0" id="typeJasa">
                            <span>Jasa</span>
                        </label>
                        <label class="d-flex align-items-center gap-2">
                            <input type="radio" name="order_type" value="sparepart" class="form-check-input mt-0" id="typePart">
                            <span>Sparepart</span>
                        </label>
                    </div>
                </div>
                <div id="jasaFields">
                    <div class="mb-3">
                        <label class="form-label required">Deskripsi Jasa</label>
                        <input type="text" id="jasaDesc" class="form-control" placeholder="Contoh: Ganti oli mesin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Harga</label>
                        <input type="number" id="jasaPrice" class="form-control" placeholder="0">
                    </div>
                </div>
                <div id="partFields" class="d-none">
                    <div class="mb-3">
                        <label class="form-label required">Sparepart</label>
                        <select id="partSelect" class="form-select">
                            <option value="">Pilih sparepart...</option>
                            <?php foreach ($spare_parts as $sp): ?>
                                <option value="<?= $sp->id ?>" data-price="<?= $sp->selling_price ?>" data-stock="<?= $sp->stock ?>">
                                    <?= htmlspecialchars($sp->name) ?> (Stok: <?= $sp->stock ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Qty</label>
                        <input type="number" id="partQty" class="form-control" value="1" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label required">Harga per Unit</label>
                        <input type="number" id="partPrice" class="form-control" placeholder="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnSaveOrder">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    const BOOKING_ID = <?= (int)$booking->id ?>;

    // Toggle jasa/part fields
    document.querySelectorAll('input[name="order_type"]').forEach(r => {
        r.addEventListener('change', function() {
            document.getElementById('jasaFields').classList.toggle('d-none', this.value !== 'jasa');
            document.getElementById('partFields').classList.toggle('d-none', this.value !== 'sparepart');
        });
    });

    // Auto-fill price when selecting sparepart
    document.getElementById('partSelect')?.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        document.getElementById('partPrice').value = opt.dataset.price || '';
    });

    // Save service order
    document.getElementById('btnSaveOrder')?.addEventListener('click', function() {
        const type = document.querySelector('input[name="order_type"]:checked').value;
        let data = {
            type
        };

        if (type === 'jasa') {
            data.description = document.getElementById('jasaDesc').value.trim();
            data.unit_price = document.getElementById('jasaPrice').value;
            data.qty = 1;
        } else {
            data.spare_part_id = document.getElementById('partSelect').value;
            data.qty = document.getElementById('partQty').value;
            data.unit_price = document.getElementById('partPrice').value;
        }

        $.ajax({
            url: BASE_URL + 'admin/booking/' + BOOKING_ID + '/add-order',
            method: 'POST',
            data: data,
            success: function(r) {
                if (r.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalAddOrder')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Ditambahkan',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1250);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: r.message
                    });
                }
            }
        });
    });

    // Delete service order
    document.querySelectorAll('.btn-del-order').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                icon: 'warning',
                title: 'Hapus item?',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then(function(res) {
                if (!res.isConfirmed) return;
                $.ajax({
                    url: BASE_URL + 'admin/service-order/' + id + '/delete',
                    method: 'POST',
                    success: function(r) {
                        if (r.success) {
                            location.reload();
                        } else Swal.fire({
                            icon: 'error',
                            text: r.message
                        });
                    }
                });
            });
        });
    });

    // Update status form
    document.getElementById('formStatus')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const status = document.getElementById('selectStatus').value;
        const note = this.querySelector('[name="admin_note"]').value;
        $.ajax({
            url: BASE_URL + 'admin/booking/' + BOOKING_ID + '/status',
            method: 'POST',
            data: {
                status,
                admin_note: note
            },
            success: function(r) {
                if (r.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status diupdate',
                        timer: 1200,
                        showConfirmButton: false
                    });
                    setTimeout(() => location.reload(), 1250);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: r.message
                    });
                }
            }
        });
    });

    // Save mechanic note
    document.getElementById('btnSaveNote')?.addEventListener('click', function() {
        const note = document.getElementById('mechanicNote').value;
        $.ajax({
            url: BASE_URL + 'admin/booking/' + BOOKING_ID + '/mechanic-note',
            method: 'POST',
            data: {
                note
            },
            success: function(r) {
                if (r.success) Swal.fire({
                    icon: 'success',
                    title: 'Tersimpan',
                    timer: 900,
                    showConfirmButton: false
                });
                else Swal.fire({
                    icon: 'error',
                    text: r.message
                });
            }
        });
    });

    // Generate invoice
    document.getElementById('btnGenerateInvoice')?.addEventListener('click', function() {
        Swal.fire({
            icon: 'question',
            title: 'Generate Invoice?',
            text: 'Invoice akan dibuat dan status berubah ke Tunggu Bayar.',
            showCancelButton: true,
            confirmButtonText: 'Ya, buat'
        }).then(function(res) {
            if (!res.isConfirmed) return;
            $.ajax({
                url: BASE_URL + 'admin/booking/' + BOOKING_ID + '/generate-invoice',
                method: 'POST',
                success: function(r) {
                    if (r.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Invoice dibuat!',
                            timer: 1400,
                            showConfirmButton: false
                        });
                        setTimeout(() => location.reload(), 1450);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: r.message
                        });
                    }
                }
            });
        });
    });

    // Confirm payment
    document.querySelector('.btn-confirm-pay')?.addEventListener('click', function() {
        const pid = this.dataset.id;
        Swal.fire({
            icon: 'question',
            title: 'Konfirmasi pembayaran?',
            showCancelButton: true,
            confirmButtonText: 'Ya'
        }).then(res => {
            if (!res.isConfirmed) return;
            $.ajax({
                url: BASE_URL + 'admin/payment/' + pid + '/confirm',
                method: 'POST',
                success: r => {
                    if (r.success) location.reload();
                    else Swal.fire({
                        icon: 'error',
                        text: r.message
                    });
                }
            });
        });
    });

    // Reject payment
    document.querySelector('.btn-reject-pay')?.addEventListener('click', function() {
        const pid = this.dataset.id;
        Swal.fire({
            icon: 'warning',
            title: 'Tolak pembayaran',
            input: 'text',
            inputLabel: 'Alasan penolakan',
            showCancelButton: true,
            confirmButtonText: 'Tolak'
        }).then(res => {
            if (!res.isConfirmed) return;
            $.ajax({
                url: BASE_URL + 'admin/payment/' + pid + '/reject',
                method: 'POST',
                data: {
                    note: res.value
                },
                success: r => {
                    if (r.success) location.reload();
                    else Swal.fire({
                        icon: 'error',
                        text: r.message
                    });
                }
            });
        });
    });
</script>