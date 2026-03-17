<?php
$status_map = [
    'unpaid'               => ['danger',  'Belum Dibayar'],
    'waiting_confirmation' => ['warning', 'Menunggu Konfirmasi'],
    'paid'                 => ['success', 'Lunas'],
    'cancelled'            => ['secondary', 'Dibatalkan'],
];
$ps = $status_map[$payment->status ?? 'unpaid'] ?? ['secondary', '-'];
?>
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Pembayaran</h1>
    <a href="<?= base_url('my/booking/' . $booking->id) ?>" class="btn btn-light btn-sm">
        <i class="fa-solid fa-arrow-left me-2"></i>Kembali ke Detail
    </a>
</div>

<div class="row g-5 g-xl-8">
    <!-- Invoice summary -->
    <div class="col-xl-7">
        <div class="card card-flush mb-5">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title fw-bold">Invoice</h3>
                <div class="card-toolbar">
                    <span class="badge badge-light-<?= $ps[0] ?> fs-6"><?= $ps[1] ?></span>
                </div>
            </div>
            <div class="card-body">
                <?php if ($payment): ?>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted">No. Invoice</span>
                        <span class="fw-bold"><?= htmlspecialchars($payment->invoice_number) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted">Tanggal Invoice</span>
                        <span class="fw-semibold"><?= date('d F Y', strtotime($payment->created_at)) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted">Booking</span>
                        <span class="fw-semibold"><?= htmlspecialchars($booking->booking_code) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-5 pb-2 border-bottom">
                        <span class="text-muted">Kendaraan</span>
                        <span class="fw-semibold"><?= htmlspecialchars($booking->plate_number) ?> — <?= htmlspecialchars($booking->brand . ' ' . $booking->vehicle_model) ?></span>
                    </div>

                    <!-- Item breakdown -->
                    <?php if (!empty($items)): ?>
                        <table class="table table-borderless fs-7 mb-5">
                            <thead>
                                <tr class="bg-light fw-bold text-muted">
                                    <th class="rounded-start ps-3">Item</th>
                                    <th>Tipe</th>
                                    <th>Qty</th>
                                    <th class="text-end rounded-end pe-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="ps-3"><?= htmlspecialchars($item->description) ?></td>
                                        <td><span class="badge badge-light-<?= $item->type === 'jasa' ? 'info' : 'warning' ?>"><?= ucfirst($item->type) ?></span></td>
                                        <td><?= $item->quantity ?></td>
                                        <td class="text-end pe-3">Rp <?= number_format($item->unit_price * $item->quantity, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <td colspan="3" class="fw-bold text-end ps-3">Total</td>
                                    <td class="fw-bolder fs-5 text-primary text-end pe-3">
                                        Rp <?= number_format($payment->total_amount, 0, ',', '.') ?>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    <?php else: ?>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Total</span>
                            <span class="fw-bolder fs-4 text-primary">Rp <?= number_format($payment->total_amount, 0, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($payment->status === 'paid'): ?>
                        <div class="alert alert-success d-flex align-items-center p-4">
                            <i class="fa-solid fa-circle-check text-success me-3 fs-3"></i>
                            <span>Pembayaran telah dikonfirmasi. Terima kasih!</span>
                        </div>
                        <a href="<?= base_url('my/invoice/' . $payment->id) ?>" class="btn btn-success w-100 mt-3" target="_blank">
                            <i class="fa-solid fa-file-invoice me-2"></i>Unduh Invoice PDF
                        </a>
                    <?php endif; ?>

                <?php elseif ($booking->status === 'waiting_payment'): ?>
                    <div class="alert alert-warning d-flex align-items-center p-4">
                        <i class="fa-solid fa-clock text-warning me-3 fs-3"></i>
                        <span>Tagihan sedang disiapkan oleh tim kami. Mohon tunggu sebentar.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payment method -->
    <?php if ($payment && in_array($payment->status, ['unpaid', 'waiting_confirmation'])): ?>
        <div class="col-xl-5">
            <div class="card card-flush">
                <div class="card-header pt-5">
                    <h3 class="card-title fw-bold">Cara Bayar</h3>
                </div>
                <div class="card-body">
                    <div class="mb-6">
                        <div class="form-check form-check-custom form-check-solid mb-4">
                            <input class="form-check-input" type="radio" name="pay_method" id="pay_cash" value="cash"
                                <?= ($payment->method === 'cash' || !$payment->method) ? 'checked' : '' ?> />
                            <label class="form-check-label" for="pay_cash">
                                <span class="fw-bold d-block">Tunai di Bengkel</span>
                                <span class="text-muted fs-7">Bayar langsung saat mengambil kendaraan.</span>
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input" type="radio" name="pay_method" id="pay_transfer" value="transfer"
                                <?= $payment->method === 'transfer' ? 'checked' : '' ?> />
                            <label class="form-check-label" for="pay_transfer">
                                <span class="fw-bold d-block">Transfer Bank</span>
                                <span class="text-muted fs-7">Transfer ke rekening bengkel lalu upload bukti.</span>
                            </label>
                        </div>
                    </div>

                    <!-- Transfer info (conditional) -->
                    <div id="transfer_info" class="<?= $payment->method !== 'transfer' ? 'd-none' : '' ?>">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-5 mb-5">
                            <div>
                                <div class="fw-bold text-info mb-2">Rekening Tujuan</div>
                                <div class="fs-7">
                                    <?= htmlspecialchars($settings['bank_name'] ?? 'BCA') ?><br />
                                    <strong class="fs-5 text-dark"><?= htmlspecialchars($settings['bank_account_number'] ?? '1234567890') ?></strong><br />
                                    a.n. <?= htmlspecialchars($settings['bank_account_name'] ?? 'JBM Bengkel') ?>
                                </div>
                            </div>
                        </div>

                        <!-- Upload proof -->
                        <?php if (!$payment->proof_file): ?>
                            <div class="mb-5">
                                <label class="form-label fw-semibold required">Upload Bukti Transfer</label>
                                <input type="file" id="proof_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" />
                                <div class="form-text text-muted">Maks. 2MB. Format: JPG, PNG, PDF.</div>
                            </div>
                            <button type="button" class="btn btn-primary w-100" onclick="uploadProof(<?= $booking->id ?>)">
                                <span class="indicator-label"><i class="fa-solid fa-upload me-2"></i>Upload Bukti Transfer</span>
                                <span class="indicator-progress d-none"><span class="spinner-border spinner-border-sm me-2"></span>Mengupload...</span>
                            </button>
                        <?php else: ?>
                            <div class="alert alert-warning d-flex align-items-center p-4">
                                <i class="fa-solid fa-hourglass-half text-warning me-3 fs-3"></i>
                                <span>Bukti transfer sedang diverifikasi oleh admin.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';

    document.querySelectorAll('[name="pay_method"]').forEach(function(r) {
        r.addEventListener('change', function() {
            document.getElementById('transfer_info').classList.toggle('d-none', this.value !== 'transfer');
        });
    });

    function uploadProof(bookingId) {
        var file = document.getElementById('proof_file').files[0];
        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih File',
                text: 'Silakan pilih file bukti transfer terlebih dahulu.'
            });
            return;
        }
        var btn = document.querySelector('[onclick="uploadProof(' + bookingId + ')"]');
        var fd = new FormData();
        fd.append('proof_file', file);
        fd.append(CSRF_TOKEN_NAME, CSRF_HASH);

        btn.disabled = true;
        btn.querySelector('.indicator-label').classList.add('d-none');
        btn.querySelector('.indicator-progress').classList.remove('d-none');

        $.ajax({
            url: BASE_URL + 'my/payment/upload/' + bookingId,
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-Token': CSRF_HASH
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2500,
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
                    btn.disabled = false;
                    btn.querySelector('.indicator-label').classList.remove('d-none');
                    btn.querySelector('.indicator-progress').classList.add('d-none');
                }
            }
        });
    }
</script>