<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice <?= htmlspecialchars($payment->invoice_number) ?></title>
    <style>
        /* dompdf-compatible CSS — avoid flexbox/grid unsupported in older dompdf */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #1a1a2e;
        }

        .header-table {
            width: 100%;
            margin-bottom: 24px;
            border-bottom: 2px solid #009ef7;
            padding-bottom: 16px;
        }

        .brand-name {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a2e;
        }

        .brand-sub {
            font-size: 11px;
            color: #6c757d;
            margin-top: 2px;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #009ef7;
            letter-spacing: 2px;
        }

        .inv-num {
            font-size: 13px;
            font-weight: bold;
        }

        .inv-date {
            font-size: 11px;
            color: #6c757d;
        }

        .parties-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .section-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .party-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .party-info {
            font-size: 11px;
            color: #5e6278;
            line-height: 1.5;
        }

        .booking-box {
            background: #f8f9fa;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .booking-table {
            width: 100%;
        }

        .booking-table td {
            font-size: 12px;
            padding: 3px 8px 3px 0;
        }

        .booking-label {
            font-size: 10px;
            color: #6c757d;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead tr {
            background-color: #009ef7;
        }

        .items-table thead th {
            color: white;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            text-align: left;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .items-table tbody td {
            padding: 8px 10px;
            font-size: 12px;
            border-bottom: 1px solid #eeeeee;
        }

        .text-right {
            text-align: right;
        }

        .totals-table {
            width: 280px;
            float: right;
            margin-bottom: 24px;
        }

        .totals-table td {
            padding: 5px 8px;
            font-size: 12px;
        }

        .totals-table .grand td {
            font-size: 15px;
            font-weight: bold;
            color: #009ef7;
            padding-top: 8px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .payment-box {
            background: #e8f7ff;
            padding: 12px 16px;
            margin-bottom: 24px;
            border-radius: 4px;
            font-size: 12px;
        }

        .status-badge {
            padding: 3px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-unpaid {
            background: #fff3cd;
            color: #856404;
        }

        .footer {
            text-align: center;
            font-size: 11px;
            color: #9a9a9a;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td style="width:60%">
                <div class="brand-name"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></div>
                <div class="brand-sub"><?= htmlspecialchars($settings['workshop_address'] ?? '') ?></div>
                <div class="brand-sub">Telp: <?= htmlspecialchars($settings['workshop_phone'] ?? '') ?></div>
            </td>
            <td style="text-align:right">
                <div class="invoice-title">INVOICE</div>
                <div class="inv-num"><?= htmlspecialchars($payment->invoice_number) ?></div>
                <div class="inv-date">Tanggal: <?= date('d F Y', strtotime($payment->created_at)) ?></div>
                <div style="margin-top:6px">
                    <span class="status-badge <?= $payment->status === 'paid' ? 'status-paid' : 'status-unpaid' ?>">
                        <?= $payment->status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' ?>
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- PARTIES -->
    <table class="parties-table">
        <tr>
            <td style="width:50%">
                <div class="section-label">Dari</div>
                <div class="party-name"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></div>
                <div class="party-info"><?= nl2br(htmlspecialchars($settings['workshop_address'] ?? '')) ?></div>
            </td>
            <td style="width:50%; text-align:right">
                <div class="section-label">Kepada</div>
                <div class="party-name"><?= htmlspecialchars($booking->customer_name) ?></div>
                <div class="party-info">
                    <?= htmlspecialchars($booking->customer_phone ?? '') ?><br>
                    <?= htmlspecialchars($booking->customer_email ?? '') ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- BOOKING INFO -->
    <div class="booking-box">
        <table class="booking-table">
            <tr>
                <td><span class="booking-label">Kode Booking</span><?= htmlspecialchars($booking->booking_code) ?></td>
                <td><span class="booking-label">Kendaraan</span><?= htmlspecialchars($booking->plate_number) ?></td>
                <td><span class="booking-label">Layanan</span><?= htmlspecialchars($booking->service_name) ?></td>
                <td><span class="booking-label">Tanggal</span><?= date('d/m/Y', strtotime($booking->booking_date)) ?></td>
            </tr>
        </table>
    </div>

    <!-- ITEMS TABLE -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px">#</th>
                <th>Deskripsi</th>
                <th style="width:70px">Tipe</th>
                <th class="text-right" style="width:40px">Qty</th>
                <th class="text-right" style="width:120px">Harga Satuan</th>
                <th class="text-right" style="width:120px">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($items as $item): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($item->description) ?></td>
                    <td><?= $item->type === 'jasa' ? 'Jasa' : 'Sparepart' ?></td>
                    <td class="text-right"><?= $item->quantity ?></td>
                    <td class="text-right">Rp <?= number_format($item->unit_price, 0, ',', '.') ?></td>
                    <td class="text-right" style="font-weight:bold">Rp <?= number_format((float)$item->quantity * (float)$item->unit_price, 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="clearfix">
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp <?= number_format($payment->total_amount, 0, ',', '.') ?></td>
            </tr>
            <tr>
                <td>Diskon</td>
                <td class="text-right">Rp 0</td>
            </tr>
            <tr class="grand">
                <td>TOTAL</td>
                <td class="text-right">Rp <?= number_format($payment->total_amount, 0, ',', '.') ?></td>
            </tr>
        </table>
    </div>

    <!-- PAYMENT INFO -->
    <div class="payment-box">
        <strong>Metode Pembayaran:</strong>
        <?php if ($payment->method === 'transfer'): ?>
            Transfer Bank — <?= htmlspecialchars($settings['bank_name'] ?? '') ?>
            (<?= htmlspecialchars($settings['bank_account'] ?? '') ?> a.n. <?= htmlspecialchars($settings['bank_holder'] ?? '') ?>)
        <?php else: ?>
            Tunai
        <?php endif; ?>
        <?php if ($payment->status === 'paid' && !empty($payment->paid_at)): ?>
            &nbsp;&middot;&nbsp; <strong>Dibayar:</strong> <?= date('d/m/Y H:i', strtotime($payment->paid_at)) ?>
        <?php endif; ?>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p>Terima kasih telah mempercayakan servis kendaraan Anda kepada <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?>.</p>
        <p>Invoice ini digenerate secara digital.</p>
    </div>

</body>

</html>