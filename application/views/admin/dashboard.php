<?php
$queue_active = 0;
$queue_status_colors = ['confirmed' => 'primary', 'in_progress' => 'info'];
$queue_status_labels = ['confirmed' => 'Menunggu', 'in_progress' => 'Diservis'];

foreach ($today_queue as $queue_item) {
    if (in_array($queue_item->status, ['confirmed', 'in_progress'], true)) {
        $queue_active++;
    }
}
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">Operations overview</div>
            <h2 class="admin-page-hero__title fw-bold">Lihat ritme bengkel hari ini dari booking, antrean servis, hingga pemasukan dalam satu dashboard.</h2>
            <p class="admin-page-hero__desc">Dashboard dirapikan agar admin bisa langsung menangkap pekerjaan yang perlu perhatian, membaca pergerakan pendapatan, dan masuk ke antrean aktif tanpa berpindah konteks.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-calendar-day"></i><?= $today_bookings ?> booking hari ini</span>
                <span class="admin-chip"><i class="fa-solid fa-money-bill-wave"></i><?= $pending_payments ?> pembayaran menunggu</span>
                <span class="admin-chip"><i class="fa-solid fa-box-open"></i><?= $low_stock_count ?> stok minimum</span>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-calendar-check fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Booking hari ini</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $today_bookings ?></div>
                        <div class="text-muted fs-7 mt-2">Slot servis yang masuk untuk tanggal aktif.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(245, 158, 11, 0.16); color: #d97706;"><i class="fa-solid fa-hourglass-half fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Menunggu tindak lanjut</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $pending_count ?></div>
                        <div class="text-muted fs-7 mt-2">Booking atau konfirmasi yang perlu diproses.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-wallet fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Pendapatan hari ini</div>
                        <div class="fs-3 fw-bold mt-1">Rp <?= number_format($revenue_today, 0, ',', '.') ?></div>
                        <div class="text-muted fs-7 mt-2">Performa kas harian yang sudah masuk.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(14, 165, 233, 0.16); color: #0284c7;"><i class="fa-solid fa-chart-line fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Pendapatan bulan ini</div>
                        <div class="fs-3 fw-bold mt-1">Rp <?= number_format($revenue_month, 0, ',', '.') ?></div>
                        <div class="text-muted fs-7 mt-2"><?= $queue_active ?> antrean aktif sedang berjalan.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($pending_payments > 0 || $low_stock_count > 0): ?>
    <div class="admin-surface-card mb-6">
        <div class="card-body p-5 p-lg-7">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-4">
                <div>
                    <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Perlu perhatian cepat</div>
                    <h3 class="fw-bold mb-2">Prioritas operasional hari ini</h3>
                    <p class="text-muted mb-0">Gunakan shortcut ini untuk langsung menangani transaksi pending dan sparepart dengan stok kritis.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <?php if ($pending_payments > 0): ?>
                        <a href="<?= base_url('admin/payments?status=waiting_confirmation') ?>" class="admin-chip text-decoration-none"><i class="fa-solid fa-money-bill-wave text-warning"></i><?= $pending_payments ?> pembayaran menunggu</a>
                    <?php endif; ?>
                    <?php if ($low_stock_count > 0): ?>
                        <a href="<?= base_url('admin/spare-parts') ?>" class="admin-chip text-decoration-none"><i class="fa-solid fa-triangle-exclamation text-danger"></i><?= $low_stock_count ?> stok minimum</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-5 g-xl-8">
    <div class="col-xl-7">
        <div class="card card-flush h-100">
            <div class="card-header">
                <h3 class="card-title fw-bold">Pendapatan <?= date('Y') ?></h3>
                <div class="card-toolbar">
                    <a href="<?= base_url('admin/reports') ?>" class="btn btn-sm btn-light-primary">Buka laporan</a>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card card-flush h-100">
            <div class="card-header">
                <h3 class="card-title fw-bold">Antrean Hari Ini</h3>
                <div class="card-toolbar">
                    <a href="<?= base_url('admin/queue') ?>" class="btn btn-sm btn-light-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($today_queue)): ?>
                    <div class="text-center py-12 px-5">
                        <i class="fa-regular fa-calendar fs-3x text-muted mb-3"></i>
                        <p class="text-muted fs-6 mb-0">Tidak ada antrean hari ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-0 fs-7 mb-0 admin-data-table">
                            <thead>
                                <tr>
                                    <th class="ps-5">Jam</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th class="text-end pe-5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($today_queue, 0, 8) as $q): ?>
                                    <tr>
                                        <td class="ps-5">
                                            <span class="badge badge-light-secondary fs-8"><?= htmlspecialchars($q->slot_label) ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold fs-7 d-block"><?= htmlspecialchars($q->customer_name) ?></span>
                                            <span class="admin-table-meta"><?= htmlspecialchars($q->plate_number) ?></span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-<?= $queue_status_colors[$q->status] ?? 'secondary' ?> px-4 py-2">
                                                <?= $queue_status_labels[$q->status] ?? $q->status ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-5">
                                            <a href="<?= base_url('admin/booking/' . $q->id) ?>" class="btn btn-icon btn-sm btn-light-primary">
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Revenue chart
    var monthlyRevenue = <?= json_encode($monthly_revenue) ?>;
    var labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    var revenueData = new Array(12).fill(0);
    monthlyRevenue.forEach(function(r) {
        revenueData[r.month - 1] = parseFloat(r.revenue) || 0;
    });

    var ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: revenueData,
                backgroundColor: 'rgba(0, 158, 247, 0.8)',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(v) {
                            return 'Rp ' + (v / 1000000).toFixed(1) + 'jt';
                        }
                    }
                }
            }
        }
    });
</script>