<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Invoice <?= htmlspecialchars($payment->invoice_number) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a2e;
            background: #fff;
        }

        .invoice-wrapper {
            max-width: 800px;
            margin: 32px auto;
            padding: 40px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
        }

        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 36px;
            padding-bottom: 20px;
            border-bottom: 2px solid #009ef7;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: #009ef7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a2e;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 12px;
            color: #6c757d;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-meta h2 {
            font-size: 26px;
            font-weight: 800;
            color: #009ef7;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .invoice-meta .inv-num {
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }

        .invoice-meta .inv-date {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
        }

        /* Parties */
        .parties {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 30px;
        }

        .party-box {
            flex: 1;
        }

        .party-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            margin-bottom: 8px;
        }

        .party-name {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .party-info {
            font-size: 12px;
            color: #5e6278;
            line-height: 1.6;
        }

        /* Booking info */
        .booking-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
        }

        .booking-info dt {
            font-size: 11px;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 3px;
        }

        .booking-info dd {
            font-size: 13px;
            font-weight: 600;
            color: #1a1a2e;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .items-table thead tr {
            background: #009ef7;
            color: white;
        }

        .items-table thead th {
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .items-table tbody tr:nth-child(odd) {
            background: #f8f9fa;
        }

        .items-table tbody td {
            padding: 10px 14px;
            font-size: 13px;
            border-bottom: 1px solid #e9ecef;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .text-right {
            text-align: right;
        }

        /* Totals */
        .totals {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 32px;
        }

        .totals-box {
            min-width: 280px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 6px 0;
            border-bottom: 1px solid #f1f1f4;
        }

        .totals-row.grand {
            font-size: 16px;
            font-weight: 700;
            color: #009ef7;
            border-bottom: none;
            padding-top: 10px;
        }

        /* Payment method */
        .payment-method {
            background: #e8f7ff;
            border-radius: 8px;
            padding: 14px 20px;
            margin-bottom: 28px;
            font-size: 13px;
        }

        .payment-method strong {
            color: #009ef7;
        }

        /* Status badge */
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .5px;
        }

        .status-paid {
            background: #d4edda;
            color: #155724;
        }

        .status-unpaid {
            background: #fff3cd;
            color: #856404;
        }

        /* Footer */
        .invoice-footer {
            text-align: center;
            font-size: 11px;
            color: #9a9a9a;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        @media print {
            body {
                background: white;
            }

            .invoice-wrapper {
                border: none;
                margin: 0;
                padding: 20px;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Print button (hidden on print) -->
    <div class="no-print" style="text-align:center;padding:20px 0 0;">
        <button onclick="window.print()" style="background:#009ef7;color:white;border:none;padding:10px 28px;border-radius:6px;font-size:14px;cursor:pointer;margin-right:8px;">
            &#128438; Cetak Invoice
        </button>
        <button onclick="window.close()" style="background:#f8f9fa;color:#1a1a2e;border:1px solid #dee2e6;padding:10px 20px;border-radius:6px;font-size:14px;cursor:pointer;">
            Tutup
        </button>
    </div>

    <div class="invoice-wrapper">

        <!-- HEADER -->
        <div class="invoice-header">
            <div class="brand-logo">
                <div class="brand-icon">&#9874;</div>
                <div>
                    <div class="brand-name"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></div>
                    <div class="brand-sub"><?= htmlspecialchars($settings['workshop_address'] ?? '') ?></div>
                    <div class="brand-sub"><?= htmlspecialchars($settings['workshop_phone'] ?? '') ?></div>
                </div>
            </div>
            <div class="invoice-meta">
                <h2>INVOICE</h2>
                <div class="inv-num"><?= htmlspecialchars($payment->invoice_number) ?></div>
                <div class="inv-date">Tanggal: <?= date('d F Y', strtotime($payment->created_at)) ?></div>
                <div style="margin-top:8px;">
                    <span class="status-badge <?= $payment->status === 'paid' ? 'status-paid' : 'status-unpaid' ?>">
                        <?= $payment->status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- PARTIES -->
        <div class="parties">
            <div class="party-box">
                <div class="party-label">Dari</div>
                <div class="party-name"><?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?></div>
                <div class="party-info">
                    <?= nl2br(htmlspecialchars($settings['workshop_address'] ?? '')) ?><br>
                    Telp: <?= htmlspecialchars($settings['workshop_phone'] ?? '') ?>
                </div>
            </div>
            <div class="party-box" style="text-align:right">
                <div class="party-label">Kepada</div>
                <div class="party-name"><?= htmlspecialchars($booking->customer_name) ?></div>
                <div class="party-info">
                    <?= htmlspecialchars($booking->customer_phone ?? '') ?><br>
                    <?= htmlspecialchars($booking->customer_email ?? '') ?>
                </div>
            </div>
        </div>

        <!-- BOOKING INFO -->
        <div class="booking-info">
            <div>
                <dt>Kode Booking</dt>
                <dd><?= htmlspecialchars($booking->booking_code) ?></dd>
            </div>
            <div>
                <dt>Kendaraan</dt>
                <dd><?= htmlspecialchars($booking->plate_number) ?></dd>
            </div>
            <div>
                <dt>Layanan</dt>
                <dd><?= htmlspecialchars($booking->service_name) ?></dd>
            </div>
            <div>
                <dt>Tanggal Servis</dt>
                <dd><?= date('d/m/Y', strtotime($booking->booking_date)) ?></dd>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Deskripsi</th>
                    <th style="text-align:center">Tipe</th>
                    <th style="text-align:right">Qty</th>
                    <th style="text-align:right">Harga Satuan</th>
                    <th style="text-align:right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1;
                foreach ($items as $item): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($item->item_name) ?></td>
                        <td style="text-align:center">
                            <span style="background:<?= $item->item_type === 'service' ? '#e8f7ff' : '#fff8e1' ?>;color:<?= $item->item_type === 'service' ? '#0066cc' : '#b45309' ?>;padding:2px 10px;border-radius:4px;font-size:11px;font-weight:600">
                                <?= $item->item_type === 'service' ? 'Jasa' : 'Sparepart' ?>
                            </span>
                        </td>
                        <td class="text-right"><?= $item->quantity ?></td>
                        <td class="text-right">Rp <?= number_format($item->unit_price, 0, ',', '.') ?></td>
                        <td class="text-right" style="font-weight:600">Rp <?= number_format($item->subtotal, 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- TOTALS -->
        <div class="totals">
            <div class="totals-box">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span>Rp <?= number_format($payment->total_amount, 0, ',', '.') ?></span>
                </div>
                <div class="totals-row">
                    <span>Diskon</span>
                    <span>Rp 0</span>
                </div>
                <div class="totals-row grand">
                    <span>TOTAL</span>
                    <span>Rp <?= number_format($payment->total_amount, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- PAYMENT METHOD -->
        <div class="payment-method">
            <strong>Metode Pembayaran:</strong>
            <?php if ($payment->method === 'transfer'): ?>
                Transfer Bank — <?= htmlspecialchars($settings['bank_name'] ?? '') ?>
                (<?= htmlspecialchars($settings['bank_account'] ?? '') ?> a.n. <?= htmlspecialchars($settings['bank_holder'] ?? '') ?>)
            <?php else: ?>
                Tunai
            <?php endif; ?>
            <?php if ($payment->status === 'paid' && !empty($payment->paid_at)): ?>
                &nbsp;·&nbsp; <strong>Dibayar pada:</strong> <?= date('d/m/Y H:i', strtotime($payment->paid_at)) ?>
            <?php endif; ?>
        </div>

        <!-- FOOTER -->
        <div class="invoice-footer">
            <p>Terima kasih telah mempercayakan servis kendaraan Anda kepada <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel Mobil') ?>.</p>
            <p style="margin-top:4px">Invoice ini dicetak secara digital dan sah tanpa tanda tangan.</p>
        </div>

    </div>
</body>

</html>