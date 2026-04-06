<?php
$status_map = [
    'pending'    => ['warning', 'Menunggu'],
    'confirmed'  => ['primary', 'Dikonfirmasi'],
    'in_progress' => ['info',    'Diservis'],
];

$queue_total = count($queue);
$pending_total = 0;
$confirmed_total = 0;
$progress_total = 0;

foreach ($queue as $queue_item) {
    if ($queue_item->status === 'pending') {
        $pending_total++;
    } elseif ($queue_item->status === 'confirmed') {
        $confirmed_total++;
    } elseif ($queue_item->status === 'in_progress') {
        $progress_total++;
    }
}
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">Live queue</div>
            <h2 class="admin-page-hero__title fw-bold">Kendalikan antrean servis hari ini dengan status yang lebih jelas dan aksi yang lebih cepat.</h2>
            <p class="admin-page-hero__desc">Halaman antrean dirancang ulang agar admin dan service advisor dapat melihat progress setiap kendaraan secara ringkas, lalu memindahkan status tanpa membuka banyak halaman.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-list-check"></i><?= $queue_total ?> antrean tampil</span>
                <span class="admin-chip"><i class="fa-solid fa-clock"></i><?= $pending_total ?> menunggu</span>
                <span class="admin-chip"><i class="fa-solid fa-screwdriver-wrench"></i><?= $progress_total ?> sedang diservis</span>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-car-side fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Total antrean</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $queue_total ?></div>
                        <div class="text-muted fs-7 mt-2">Semua kendaraan yang masuk hari ini.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(245, 158, 11, 0.16); color: #d97706;"><i class="fa-solid fa-hourglass-half fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Menunggu</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $pending_total ?></div>
                        <div class="text-muted fs-7 mt-2">Belum dikonfirmasi ke area servis.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(14, 165, 233, 0.16); color: #0284c7;"><i class="fa-solid fa-clipboard-check fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Siap dikerjakan</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $confirmed_total ?></div>
                        <div class="text-muted fs-7 mt-2">Sudah siap masuk pengerjaan mekanik.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-gauge-high fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Sedang diservis</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $progress_total ?></div>
                        <div class="text-muted fs-7 mt-2">Pekerjaan aktif yang sedang berjalan.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="admin-surface-card mb-6">
    <div class="card-body p-5 p-lg-7">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4">
            <div>
                <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Kontrol cepat</div>
                <h3 class="fw-bold mb-0">Refresh status antrean dan lanjutkan proses kendaraan</h3>
            </div>
            <button class="btn btn-light-primary btn-lg" onclick="location.reload()">
                <i class="fa-solid fa-rotate-right me-2"></i>Refresh antrean
            </button>
        </div>
    </div>
</div>

<?php if (empty($queue)): ?>
    <div class="admin-surface-card overflow-hidden">
        <div class="card-body text-center py-15 px-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary mb-5" style="width: 88px; height: 88px;">
                <i class="fa-regular fa-calendar-check fs-1"></i>
            </div>
            <h4 class="fw-bold mb-3">Tidak ada antrean hari ini</h4>
            <p class="text-muted fs-6 mb-0">Saat ada booking yang masuk dan terjadwal hari ini, antreannya akan muncul di sini.</p>
        </div>
    </div>
<?php else: ?>
    <div class="row g-4" id="queueCards">
        <?php foreach ($queue as $q):
            $si = $status_map[$q->status] ?? ['secondary', $q->status];
        ?>
            <div class="col-xl-4 col-md-6" id="card-<?= $q->id ?>">
                <div class="card card-flush h-100">
                    <div class="card-header pb-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-50px">
                                <div class="symbol-label bg-light-<?= $si[0] ?>">
                                    <i class="fa-solid fa-wrench fs-4 text-<?= $si[0] ?>"></i>
                                </div>
                            </div>
                            <div>
                                <span class="fw-bold fs-6 text-dark d-block"><?= htmlspecialchars($q->booking_code) ?></span>
                                <span class="badge badge-light-<?= $si[0] ?> fs-8 px-4 py-2 mt-2"><?= $si[1] ?></span>
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
                    <div class="card-footer pt-0 pb-4 d-flex gap-2 px-6">
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

                    $.ajax({
                        url: BASE_URL + 'admin/booking/' + id + '/status',
                        method: 'POST',
                        data: {
                            status: status
                        },
                        success: function(r) {
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