<!-- Admin Dashboard -->
<div class="row g-5 g-xl-8 mb-8">
    <!-- KPI Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary card-xl-stretch">
            <div class="card-body d-flex align-items-center gap-4 py-8">
                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-25 rounded" style="width:60px;height:60px;flex-shrink:0;">
                    <i class="fa-solid fa-calendar-check text-white fs-2"></i>
                </div>
                <div>
                    <div class="fs-2hx fw-bolder text-white"><?= $today_bookings ?></div>
                    <div class="text-white opacity-75 fw-semibold fs-6">Booking Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning card-xl-stretch">
            <div class="card-body d-flex align-items-center gap-4 py-8">
                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-25 rounded" style="width:60px;height:60px;flex-shrink:0;">
                    <i class="fa-solid fa-clock text-white fs-2"></i>
                </div>
                <div>
                    <div class="fs-2hx fw-bolder text-white"><?= $pending_count ?></div>
                    <div class="text-white opacity-75 fw-semibold fs-6">Menunggu Konfirmasi</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success card-xl-stretch">
            <div class="card-body d-flex align-items-center gap-4 py-8">
                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-25 rounded" style="width:60px;height:60px;flex-shrink:0;">
                    <i class="fa-solid fa-money-bill text-white fs-2"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bolder text-white">Rp <?= number_format($revenue_today, 0, ',', '.') ?></div>
                    <div class="text-white opacity-75 fw-semibold fs-6">Pendapatan Hari Ini</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info card-xl-stretch">
            <div class="card-body d-flex align-items-center gap-4 py-8">
                <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-25 rounded" style="width:60px;height:60px;flex-shrink:0;">
                    <i class="fa-solid fa-chart-line text-white fs-2"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bolder text-white">Rp <?= number_format($revenue_month, 0, ',', '.') ?></div>
                    <div class="text-white opacity-75 fw-semibold fs-6">Pendapatan Bulan Ini</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pending alerts -->
<?php if ($pending_payments > 0): ?>
    <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
        <i class="fa-solid fa-money-bill-wave fs-2 text-warning me-4"></i>
        <div class="flex-grow-1">
            <strong><?= $pending_payments ?> pembayaran</strong> menunggu konfirmasi.
        </div>
        <a href="<?= base_url('admin/payments?status=waiting_confirmation') ?>" class="btn btn-sm btn-warning ms-2">
            Tinjau Sekarang
        </a>
    </div>
<?php endif; ?>
<?php if ($low_stock_count > 0): ?>
    <div class="alert alert-danger d-flex align-items-center p-5 mb-5">
        <i class="fa-solid fa-triangle-exclamation fs-2 text-danger me-4"></i>
        <div class="flex-grow-1">
            <strong><?= $low_stock_count ?> suku cadang</strong> stok menipis (di bawah minimum).
        </div>
        <a href="<?= base_url('admin/spare-parts') ?>" class="btn btn-sm btn-danger ms-2">Kelola Stok</a>
    </div>
<?php endif; ?>

<div class="row g-5 g-xl-8">
    <!-- Revenue chart -->
    <div class="col-xl-7">
        <div class="card card-flush h-100">
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold">Pendapatan <?= date('Y') ?></h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="260"></canvas>
            </div>
        </div>
    </div>

    <!-- Today's queue -->
    <div class="col-xl-5">
        <div class="card card-flush h-100">
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold">Antrean Hari Ini</h3>
                <div class="card-toolbar">
                    <a href="<?= base_url('admin/queue') ?>" class="btn btn-sm btn-light">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($today_queue)): ?>
                    <div class="text-center py-10">
                        <i class="fa-regular fa-calendar fs-3x text-muted mb-3"></i>
                        <p class="text-muted fs-6">Tidak ada antrean hari ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3">
                            <tbody>
                                <?php foreach (array_slice($today_queue, 0, 8) as $q): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge badge-light-secondary fs-8"><?= htmlspecialchars($q->slot_label) ?></span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold fs-7 d-block"><?= htmlspecialchars($q->customer_name) ?></span>
                                            <span class="text-muted fs-8"><?= htmlspecialchars($q->plate_number) ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $sc = ['confirmed' => 'primary', 'in_progress' => 'info'];
                                            $sl = ['confirmed' => 'Menunggu', 'in_progress' => 'Diservis'];
                                            ?>
                                            <span class="badge badge-light-<?= $sc[$q->status] ?? 'secondary' ?>">
                                                <?= $sl[$q->status] ?? $q->status ?>
                                            </span>
                                        </td>
                                        <td class="pe-4">
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