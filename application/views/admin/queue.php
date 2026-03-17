<?php
$status_map = [
    'pending'    => ['warning', 'Menunggu'],
    'confirmed'  => ['primary', 'Dikonfirmasi'],
    'in_progress' => ['info',    'Diservis'],
];
$csrf_name  = $this->security->get_csrf_token_name();
$csrf_hash  = $this->security->get_csrf_hash();
?>
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Antrian Hari Ini</h1>
    <button class="btn btn-sm btn-light" onclick="location.reload()">
        <i class="fa-solid fa-rotate-right me-1"></i>Refresh
    </button>
</div>

<?php if (empty($queue)): ?>
    <div class="card card-flush">
        <div class="card-body text-center py-15">
            <i class="fa-regular fa-calendar-check fs-3x text-muted mb-4"></i>
            <p class="text-muted fs-6">Tidak ada antrian hari ini.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4" id="queueCards">
        <?php foreach ($queue as $q):
            $si = $status_map[$q->status] ?? ['secondary', $q->status];
        ?>
            <div class="col-xl-4 col-md-6" id="card-<?= $q->id ?>">
                <div class="card card-flush border-<?= $si[0] ?> border-start border-3">
                    <div class="card-header pt-4 pb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-50px">
                                <div class="symbol-label bg-light-<?= $si[0] ?>">
                                    <i class="fa-solid fa-wrench fs-4 text-<?= $si[0] ?>"></i>
                                </div>
                            </div>
                            <div>
                                <span class="fw-bold fs-6 text-dark d-block"><?= htmlspecialchars($q->booking_code) ?></span>
                                <span class="badge badge-light-<?= $si[0] ?> fs-8"><?= $si[1] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-3 pb-4">
                        <div class="d-flex flex-column gap-2 fs-7">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Customer</span>
                                <span class="fw-semibold text-end"><?= htmlspecialchars($q->customer_name) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Kendaraan</span>
                                <span class="fw-semibold"><?= htmlspecialchars($q->plate_number) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Layanan</span>
                                <span class="fw-semibold text-end"><?= htmlspecialchars($q->service_name) ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Jam</span>
                                <span class="fw-semibold"><?= htmlspecialchars($q->slot_label) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer pt-0 pb-4 d-flex gap-2">
                        <?php if ($q->status === 'pending'): ?>
                            <button class="btn btn-sm btn-light-success flex-fill btn-status"
                                data-id="<?= $q->id ?>" data-status="confirmed" data-code="<?= $q->booking_code ?>">
                                <i class="fa-solid fa-check me-1"></i>Konfirmasi
                            </button>
                        <?php elseif ($q->status === 'confirmed'): ?>
                            <button class="btn btn-sm btn-light-info flex-fill btn-status"
                                data-id="<?= $q->id ?>" data-status="in_progress" data-code="<?= $q->booking_code ?>">
                                <i class="fa-solid fa-play me-1"></i>Mulai Servis
                            </button>
                        <?php elseif ($q->status === 'in_progress'): ?>
                            <button class="btn btn-sm btn-light-warning flex-fill btn-status"
                                data-id="<?= $q->id ?>" data-status="waiting_payment" data-code="<?= $q->booking_code ?>">
                                <i class="fa-solid fa-file-invoice me-1"></i>Buat Invoice
                            </button>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/booking/' . $q->id) ?>" class="btn btn-sm btn-light">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const CSRF_NAME = '<?= $csrf_name ?>';
        let CSRF_HASH = '<?= $csrf_hash ?>';

        document.querySelectorAll('.btn-status').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const status = this.dataset.status;
                const code = this.dataset.code;

                const labels = {
                    confirmed: 'Konfirmasi booking <b>' + code + '</b>?',
                    in_progress: 'Mulai servis untuk <b>' + code + '</b>?',
                    waiting_payment: 'Tandai selesai & buat invoice untuk <b>' + code + '</b>?',
                };

                Swal.fire({
                    title: 'Konfirmasi',
                    html: labels[status] || 'Update status?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, lanjutkan',
                    cancelButtonText: 'Batal',
                }).then(function(res) {
                    if (!res.isConfirmed) return;

                    const data = {};
                    data[CSRF_NAME] = CSRF_HASH;
                    data.status = status;

                    $.ajax({
                        url: BASE_URL + 'admin/booking/' + id + '/status',
                        method: 'POST',
                        headers: {
                            'X-CSRF-Token': CSRF_HASH
                        },
                        data: data,
                        success: function(r) {
                            CSRF_HASH = r.csrf_hash || CSRF_HASH;
                            if (r.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: r.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                if (status === 'waiting_payment') {
                                    setTimeout(() => {
                                        window.location.href = BASE_URL + 'admin/booking/' + id;
                                    }, 1600);
                                } else {
                                    setTimeout(() => location.reload(), 1600);
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: r.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menghubungi server.'
                            });
                        }
                    });
                });
            });
        });
    });
</script>