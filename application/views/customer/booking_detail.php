<?php
// Status helpers
$status_map = [
    'pending'              => ['warning',  'Menunggu Konfirmasi', 'fa-clock'],
    'confirmed'            => ['primary',  'Dikonfirmasi',         'fa-calendar-check'],
    'in_progress'          => ['info',     'Sedang Diservis',      'fa-wrench'],
    'waiting_payment'      => ['warning',  'Menunggu Pembayaran',  'fa-money-bill'],
    'waiting_confirmation' => ['warning',  'Konfirmasi Bayar',     'fa-hourglass-half'],
    'completed'            => ['success',  'Selesai',              'fa-circle-check'],
    'cancelled'            => ['danger',   'Dibatalkan',           'fa-ban'],
];
$s_info = $status_map[$booking->status] ?? ['secondary', ucfirst($booking->status), 'fa-question'];

// Progress steps
$progress_steps = ['pending', 'confirmed', 'in_progress', 'waiting_payment', 'completed'];
$current_step   = array_search($booking->status, $progress_steps);
if ($current_step === false) $current_step = -1;
?>

<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Detail Booking</h1>
    <a href="<?= base_url('my/history') ?>" class="btn btn-light btn-sm">
        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row g-5 g-xl-8">
    <!-- Main column -->
    <div class="col-xl-8">

        <!-- Booking code & status card -->
        <div class="card card-flush mb-5">
            <div class="card-body py-8">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-4">
                    <div>
                        <div class="text-muted fs-7 mb-1">Kode Booking</div>
                        <h2 class="fw-bolder text-dark mb-0"><?= htmlspecialchars($booking->booking_code) ?></h2>
                        <span class="text-muted fs-7">Dibuat <?= date('d F Y H:i', strtotime($booking->created_at)) ?></span>
                    </div>
                    <span class="badge badge-light-<?= $s_info[0] ?> fs-5 px-4 py-3">
                        <i class="fa-solid <?= $s_info[2] ?> me-2"></i><?= $s_info[1] ?>
                    </span>
                </div>

                <!-- Progress bar -->
                <?php if ($booking->status !== 'cancelled'): ?>
                    <div class="mt-8">
                        <div class="d-flex justify-content-between mb-2">
                            <?php foreach (['Booking', 'Konfirmasi', 'Servis', 'Pembayaran', 'Selesai'] as $i => $label): ?>
                                <div class="d-flex flex-column align-items-center" style="width:20%;">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle mb-2
                                <?= $i <= $current_step ? 'bg-' . ($i === $current_step ? $s_info[0] : 'success') . ' text-white' : 'bg-light text-muted' ?>"
                                        style="width:36px;height:36px;border:2px solid <?= $i <= $current_step ? 'transparent' : '#e4e6ea' ?>;">
                                        <?php if ($i < $current_step): ?>
                                            <i class="fa-solid fa-check fs-7"></i>
                                        <?php else: ?>
                                            <span class="fs-8 fw-bold"><?= $i + 1 ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="fs-9 text-center text-<?= $i <= $current_step ? 'gray-800 fw-semibold' : 'muted' ?>"><?= $label ?></span>
                                </div>
                                <?php if ($i < 4): ?>
                                    <div class="flex-grow-1 mt-4" style="height:2px;background:<?= $i < $current_step ? '#50cd89' : '#e4e6ea' ?>;margin-top:17px;"></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Service & schedule info -->
        <div class="card card-flush mb-5">
            <div class="card-header">
                <h3 class="card-title fw-bold">Detail Servis</h3>
            </div>
            <div class="card-body">
                <div class="row g-5">
                    <div class="col-md-6">
                        <div class="d-flex gap-4">
                            <div class="d-flex align-items-center justify-content-center bg-light-primary rounded" style="width:50px;height:50px;flex-shrink:0;">
                                <i class="fa-solid fa-wrench text-primary fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-8 mb-1">Layanan</div>
                                <div class="fw-bold fs-6"><?= htmlspecialchars($booking->service_name) ?></div>
                                <div class="text-muted fs-8">Estimasi <?= $booking->service_duration ?> menit</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-4">
                            <div class="d-flex align-items-center justify-content-center bg-light-info rounded" style="width:50px;height:50px;flex-shrink:0;">
                                <i class="fa-solid fa-calendar text-info fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-8 mb-1">Jadwal</div>
                                <div class="fw-bold fs-6"><?= date('d F Y', strtotime($booking->booking_date)) ?></div>
                                <div class="text-muted fs-8"><?= htmlspecialchars($booking->slot_label) ?> (<?= substr($booking->start_time, 0, 5) ?> – <?= substr($booking->end_time, 0, 5) ?>)</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-4">
                            <div class="d-flex align-items-center justify-content-center bg-light-warning rounded" style="width:50px;height:50px;flex-shrink:0;">
                                <i class="fa-solid fa-car text-warning fs-4"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-8 mb-1">Kendaraan</div>
                                <div class="fw-bold fs-6"><?= htmlspecialchars($booking->brand . ' ' . $booking->vehicle_model) ?> <?= $booking->year ?></div>
                                <div class="text-muted fs-8"><?= htmlspecialchars($booking->plate_number) ?> · <?= htmlspecialchars($booking->color) ?></div>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($booking->mechanic_name)): ?>
                        <div class="col-md-6">
                            <div class="d-flex gap-4">
                                <div class="d-flex align-items-center justify-content-center bg-light-success rounded" style="width:50px;height:50px;flex-shrink:0;">
                                    <i class="fa-solid fa-user-tie text-success fs-4"></i>
                                </div>
                                <div>
                                    <div class="text-muted fs-8 mb-1">Mekanik</div>
                                    <div class="fw-bold fs-6"><?= htmlspecialchars($booking->mechanic_name) ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($booking->notes)): ?>
                        <div class="col-12">
                            <div class="text-muted fs-8 mb-1">Catatan Anda</div>
                            <div class="text-gray-700 fs-6"><?= htmlspecialchars($booking->notes) ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($booking->mechanic_notes)): ?>
                        <div class="col-12">
                            <div class="text-muted fs-8 mb-1">Catatan Mekanik</div>
                            <div class="text-gray-700 fs-6"><?= htmlspecialchars($booking->mechanic_notes) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Service orders -->
        <?php if (!empty($service_orders)): ?>
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <h3 class="card-title fw-bold">Detail Pekerjaan &amp; Suku Cadang</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 rounded-start">Item</th>
                                    <th>Tipe</th>
                                    <th>Qty</th>
                                    <th>Harga Satuan</th>
                                    <th class="rounded-end pe-4 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($service_orders as $so): ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?= htmlspecialchars($so->part_name ?? $so->description) ?></td>
                                        <td><span class="badge badge-light-<?= $so->type === 'jasa' ? 'info' : 'warning' ?>"><?= ucfirst($so->type) ?></span></td>
                                        <td><?= $so->quantity ?> <?= htmlspecialchars($so->unit ?? '') ?></td>
                                        <td>Rp <?= number_format($so->unit_price, 0, ',', '.') ?></td>
                                        <td class="text-end pe-4 fw-bold">Rp <?= number_format($so->quantity * $so->unit_price, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Status timeline -->
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title fw-bold">Riwayat Status</h3>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach (array_reverse($logs) as $log): ?>
                        <?php $li = $status_map[$log->status] ?? ['secondary', ucfirst($log->status), 'fa-circle']; ?>
                        <div class="timeline-item">
                            <div class="timeline-line w-40px"></div>
                            <div class="timeline-icon symbol symbol-circle symbol-40px">
                                <div class="symbol-label bg-light-<?= $li[0] ?>">
                                    <i class="fa-solid <?= $li[2] ?> fs-6 text-<?= $li[0] ?>"></i>
                                </div>
                            </div>
                            <div class="timeline-content mb-10 mt-n2">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-gray-800"><?= $li[1] ?></span>
                                    <span class="text-muted fs-8"><?= date('d/m/Y H:i', strtotime($log->created_at)) ?></span>
                                </div>
                                <?php if (!empty($log->note)): ?>
                                    <div class="text-muted fs-7"><?= htmlspecialchars($log->note) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($log->actor_name)): ?>
                                    <div class="text-muted fs-8 mt-1">oleh <?= htmlspecialchars($log->actor_name) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-xl-4">
        <!-- Payment card -->
        <?php if ($payment): ?>
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <h3 class="card-title fw-bold">Pembayaran</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">No. Invoice</span>
                        <span class="fw-bold"><?= htmlspecialchars($payment->invoice_number) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Total</span>
                        <span class="fw-bold fs-5 text-primary">Rp <?= number_format($payment->total_amount, 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted">Status</span>
                        <?php $pm = $status_map[$payment->status] ?? ['secondary', ucfirst($payment->status)]; ?>
                        <span class="badge badge-light-<?= $pm[0] ?>"><?= $pm[1] ?></span>
                    </div>
                    <?php if (in_array($payment->status, ['unpaid', 'waiting_confirmation'])): ?>
                        <a href="<?= base_url('my/payment/' . $booking->id) ?>" class="btn btn-primary w-100">
                            <i class="fa-solid fa-money-bill me-2"></i>Bayar Sekarang
                        </a>
                    <?php elseif ($payment->status === 'paid'): ?>
                        <a href="<?= base_url('my/invoice/' . $payment->id) ?>" class="btn btn-light-success w-100" target="_blank">
                            <i class="fa-solid fa-file-invoice me-2"></i>Unduh Invoice
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cancel booking -->
        <?php if (in_array($booking->status, ['pending', 'confirmed'])): ?>
            <div class="card border border-danger border-dashed">
                <div class="card-body p-5">
                    <h5 class="fw-bold text-danger mb-2">Batalkan Booking</h5>
                    <p class="text-muted fs-7 mb-4">
                        Pembatalan gratis jika dilakukan sebelum H-1 jadwal servis.
                    </p>
                    <button type="button" class="btn btn-sm btn-danger w-100" onclick="cancelBooking(<?= $booking->id ?>)">
                        <i class="fa-solid fa-ban me-2"></i>Batalkan Booking
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';

    function cancelBooking(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Batalkan Booking?',
            text: 'Tindakan ini tidak dapat dibatalkan.',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#f1416c',
        }).then(function(result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: BASE_URL + 'my/booking/cancel/' + id,
                method: 'POST',
                data: {
                    [CSRF_TOKEN_NAME]: CSRF_HASH
                },
                headers: {
                    'X-CSRF-Token': CSRF_HASH
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                                icon: 'success',
                                title: 'Dibatalkan',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            })
                            .then(function() {
                                window.location.reload();
                            });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });
                    }
                }
            });
        });
    }
</script>