<?php
$year = date('Y');
?>
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Laporan Keuangan</h1>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-tabs nav-line-tabs mb-6 fs-6" id="reportTabs">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tabHarian">Laporan Harian</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tabBulanan">Laporan Bulanan</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tabTahunan">Laporan Tahunan</a>
    </li>
</ul>

<div class="tab-content">

    <!-- TAB: HARIAN -->
    <div class="tab-pane fade show active" id="tabHarian">
        <div class="card card-flush mb-5">
            <div class="card-body py-4">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    <div>
                        <label class="form-label fs-7 mb-1">Pilih Tanggal</label>
                        <input type="date" id="dailyDate" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                    </div>
                    <button class="btn btn-sm btn-primary" id="btnLoadDaily">
                        <i class="fa-solid fa-chart-bar me-1"></i>Tampilkan
                    </button>
                </div>
            </div>
        </div>

        <div id="dailyResult" class="d-none">
            <!-- Summary Cards -->
            <div class="row g-4 mb-5" id="dailySummary"></div>
            <!-- Table -->
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title" id="dailyTitle">Transaksi Harian</h3>
                    <div class="card-toolbar">
                        <a href="#" id="btnExportDaily" class="btn btn-sm btn-light">
                            <i class="fa-solid fa-file-csv me-1"></i>Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed fs-7 align-middle gs-5 gy-3 mb-0">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-5">Invoice</th>
                                    <th>Customer</th>
                                    <th>Layanan</th>
                                    <th>Metode</th>
                                    <th class="text-end pe-5">Total</th>
                                </tr>
                            </thead>
                            <tbody id="dailyBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: BULANAN -->
    <div class="tab-pane fade" id="tabBulanan">
        <div class="card card-flush mb-5">
            <div class="card-body py-4">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    <div>
                        <label class="form-label fs-7 mb-1">Bulan</label>
                        <select id="monthlyMonth" class="form-select form-select-sm w-150px">
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $m == date('n') ? 'selected' : '' ?>>
                                    <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fs-7 mb-1">Tahun</label>
                        <input type="number" id="monthlyYear" class="form-control form-control-sm w-100px" value="<?= $year ?>" min="2020">
                    </div>
                    <button class="btn btn-sm btn-primary" id="btnLoadMonthly">
                        <i class="fa-solid fa-chart-line me-1"></i>Tampilkan
                    </button>
                </div>
            </div>
        </div>

        <div id="monthlyResult" class="d-none">
            <div class="row g-4 mb-5" id="monthlySummary"></div>
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <h3 class="card-title">Pendapatan per Minggu</h3>
                </div>
                <div class="card-body"><canvas id="chartMonthly" style="max-height:300px"></canvas></div>
            </div>
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Rekap Harian</h3>
                    <div class="card-toolbar">
                        <a href="#" id="btnExportMonthly" class="btn btn-sm btn-light">
                            <i class="fa-solid fa-file-csv me-1"></i>Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed fs-7 align-middle gs-5 gy-3 mb-0">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-5">Tanggal</th>
                                    <th class="text-end">Transaksi</th>
                                    <th class="text-end pe-5">Total</th>
                                </tr>
                            </thead>
                            <tbody id="monthlyBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: TAHUNAN -->
    <div class="tab-pane fade" id="tabTahunan">
        <div class="card card-flush mb-5">
            <div class="card-body py-4">
                <div class="d-flex align-items-end gap-3 flex-wrap">
                    <div>
                        <label class="form-label fs-7 mb-1">Tahun</label>
                        <input type="number" id="yearlyYear" class="form-control form-control-sm w-120px" value="<?= $year ?>" min="2020">
                    </div>
                    <button class="btn btn-sm btn-primary" id="btnLoadYearly">
                        <i class="fa-solid fa-chart-line me-1"></i>Tampilkan
                    </button>
                </div>
            </div>
        </div>

        <div id="yearlyResult" class="d-none">
            <div class="row g-4 mb-5" id="yearlySummary"></div>
            <div class="card card-flush mb-5">
                <div class="card-header">
                    <h3 class="card-title">Pendapatan Bulanan</h3>
                </div>
                <div class="card-body"><canvas id="chartYearly" style="max-height:320px"></canvas></div>
            </div>
            <div class="card card-flush">
                <div class="card-header">
                    <h3 class="card-title">Rekap Bulanan</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed fs-7 align-middle gs-5 gy-3 mb-0">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-5">Bulan</th>
                                    <th class="text-end">Transaksi</th>
                                    <th class="text-end pe-5">Total</th>
                                </tr>
                            </thead>
                            <tbody id="yearlyBody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    function fmt(n) {
        return 'Rp ' + parseInt(n || 0).toLocaleString('id-ID');
    }

    function summaryCard(label, value, color) {
        return '<div class="col-sm-4"><div class="card card-flush border-0 bg-light-' + color + '"><div class="card-body py-4"><span class="text-muted fs-8 d-block mb-1">' + label + '</span><span class="fs-3 fw-bold text-' + color + '">' + value + '</span></div></div></div>';
    }

    // DAILY
    document.getElementById('btnLoadDaily').addEventListener('click', function() {
        const date = document.getElementById('dailyDate').value;
        $.getJSON(BASE_URL + 'admin/reports/daily?date=' + date, function(r) {
            if (!r.success) return;
            const rows = r.data || [];
            let html = '';
            rows.forEach(function(p) {
                html += '<tr><td class="ps-5 fw-semibold">' + p.invoice_number + '</td><td>' + p.customer_name + '</td><td>' + p.service_name + '</td><td>' + (p.payment_method || '-') + '</td><td class="text-end pe-5 fw-bold">' + fmt(p.total_amount) + '</td></tr>';
            });
            document.getElementById('dailyBody').innerHTML = html || '<tr><td colspan="5" class="text-center text-muted py-6">Tidak ada transaksi.</td></tr>';

            const cash = rows.filter(x => x.payment_method === 'cash').reduce((s, x) => s + parseFloat(x.total_amount || 0), 0);
            const tf = rows.filter(x => x.payment_method === 'transfer').reduce((s, x) => s + parseFloat(x.total_amount || 0), 0);
            const tot = rows.reduce((s, x) => s + parseFloat(x.total_amount || 0), 0);
            document.getElementById('dailySummary').innerHTML = summaryCard('Tunai', fmt(cash), 'success') + summaryCard('Transfer', fmt(tf), 'info') + summaryCard('Grand Total', fmt(tot), 'primary');
            document.getElementById('dailyTitle').textContent = 'Transaksi ' + date;
            document.getElementById('dailyResult').classList.remove('d-none');
            document.getElementById('btnExportDaily').href = BASE_URL + 'admin/reports/export?type=daily&date=' + date;
        });
    });

    // MONTHLY
    let chartMonthly = null;
    document.getElementById('btnLoadMonthly').addEventListener('click', function() {
        const month = document.getElementById('monthlyMonth').value;
        const year = document.getElementById('monthlyYear').value;
        $.getJSON(BASE_URL + 'admin/reports/monthly?month=' + month + '&year=' + year, function(r) {
            if (!r.success) return;
            const days = r.data || [];
            let html = '';
            days.forEach(function(d) {
                html += '<tr><td class="ps-5">' + d.date + '</td><td class="text-end">' + (d.count || 0) + '</td><td class="text-end pe-5 fw-bold">' + fmt(d.total) + '</td></tr>';
            });
            document.getElementById('monthlyBody').innerHTML = html || '<tr><td colspan="3" class="text-center text-muted py-6">Tidak ada data.</td></tr>';
            const tot = days.reduce((s, d) => s + parseFloat(d.total || 0), 0);
            const cnt = days.reduce((s, d) => s + parseInt(d.count || 0), 0);
            document.getElementById('monthlySummary').innerHTML = summaryCard('Total Transaksi', cnt, 'info') + summaryCard('Total Pendapatan', fmt(tot), 'primary');
            document.getElementById('monthlyResult').classList.remove('d-none');
            document.getElementById('btnExportMonthly').href = BASE_URL + 'admin/reports/export?type=monthly&month=' + month + '&year=' + year;

            // Chart
            const labels = days.map(d => d.date.split('-')[2]);
            const totals = days.map(d => parseFloat(d.total || 0));
            if (chartMonthly) chartMonthly.destroy();
            chartMonthly = new Chart(document.getElementById('chartMonthly'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: totals,
                        backgroundColor: 'rgba(0,158,247,0.7)',
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    });

    // YEARLY
    let chartYearly = null;
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    document.getElementById('btnLoadYearly').addEventListener('click', function() {
        const year = document.getElementById('yearlyYear').value;
        $.getJSON(BASE_URL + 'admin/reports/yearly?year=' + year, function(r) {
            if (!r.success) return;
            const months = r.data || [];
            let html = '';
            months.forEach(function(m, i) {
                html += '<tr><td class="ps-5">' + monthNames[i] + '</td><td class="text-end">' + (m.count || 0) + '</td><td class="text-end pe-5 fw-bold">' + fmt(m.total) + '</td></tr>';
            });
            document.getElementById('yearlyBody').innerHTML = html;
            const tot = months.reduce((s, m) => s + parseFloat(m.total || 0), 0);
            const cnt = months.reduce((s, m) => s + parseInt(m.count || 0), 0);
            document.getElementById('yearlySummary').innerHTML = summaryCard('Total Transaksi', cnt, 'info') + summaryCard('Total Pendapatan', fmt(tot), 'primary');
            document.getElementById('yearlyResult').classList.remove('d-none');

            const labels = months.map((m, i) => monthNames[i]);
            const totals = months.map(m => parseFloat(m.total || 0));
            if (chartYearly) chartYearly.destroy();
            chartYearly = new Chart(document.getElementById('chartYearly'), {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: totals,
                        fill: true,
                        backgroundColor: 'rgba(0,158,247,0.1)',
                        borderColor: '#009ef7',
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#009ef7'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    });
</script>