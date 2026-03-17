<!-- Service Status -->
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Status Servis</h1>
</div>

<?php if (empty($bookings)): ?>
    <div class="card card-flush">
        <div class="card-body text-center py-20">
            <i class="fa-solid fa-magnifying-glass fs-3x text-muted mb-5"></i>
            <h4 class="text-muted">Tidak Ada Kendaraan yang Sedang Diservis</h4>
            <p class="text-muted fs-6">Booking baru untuk menjadwalkan servis kendaraan Anda.</p>
            <a href="<?= base_url('my/booking') ?>" class="btn btn-primary mt-4">
                <i class="fa-solid fa-calendar-plus me-2"></i>Booking Sekarang
            </a>
        </div>
    </div>
<?php else: ?>

    <?php foreach ($bookings as $b): ?>
        <div class="card card-flush mb-6" id="status_card_<?= $b->id ?>" data-booking-id="<?= $b->id ?>" data-booking-code="<?= htmlspecialchars($b->booking_code) ?>">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-light-info rounded" style="width:50px;height:50px;">
                            <i class="fa-solid fa-wrench text-info fs-3 spinning"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0"><?= htmlspecialchars($b->plate_number) ?> — <?= htmlspecialchars($b->service_name) ?></h3>
                            <span class="text-muted fs-7"><?= htmlspecialchars($b->booking_code) ?></span>
                        </div>
                    </div>
                </div>
                <div class="card-toolbar">
                    <a href="<?= base_url('my/booking/' . $b->id) ?>" class="btn btn-sm btn-light">Detail</a>
                </div>
            </div>
            <div class="card-body pt-4">
                <!-- Live status tracking (will poll via JS) -->
                <div class="status-container" data-id="<?= $b->id ?>">
                    <div class="d-flex align-items-center gap-3 mb-5">
                        <span class="badge badge-light-info fs-6 px-4 py-3 status-badge">
                            <i class="fa-solid fa-wrench me-2"></i>Sedang Diservis
                        </span>
                        <span class="text-muted fs-7">Update terakhir: <span class="last-update">Baru saja</span></span>
                    </div>

                    <!-- Progress steps -->
                    <div class="d-flex justify-content-between mb-6 status-steps">
                        <?php
                        $steps = [
                            ['pending',         'Booking',      'fa-calendar'],
                            ['confirmed',       'Konfirmasi',   'fa-calendar-check'],
                            ['in_progress',     'Diservis',     'fa-wrench'],
                            ['waiting_payment', 'Pembayaran',   'fa-money-bill'],
                            ['completed',       'Selesai',      'fa-circle-check'],
                        ];
                        $cur_idx = 2; // in_progress
                        ?>
                        <?php foreach ($steps as $si => $step): ?>
                            <div class="d-flex flex-column align-items-center" style="width:20%;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle mb-2
                         <?= $si <= $cur_idx ? ($si === $cur_idx ? 'bg-info text-white pulse' : 'bg-success text-white') : 'bg-light text-muted' ?>"
                                    style="width:40px;height:40px;">
                                    <i class="fa-solid <?= $step[2] ?> fs-7"></i>
                                </div>
                                <span class="fs-9 text-center text-<?= $si <= $cur_idx ? 'gray-800 fw-semibold' : 'muted' ?>"><?= $step[1] ?></span>
                            </div>
                            <?php if ($si < 4): ?>
                                <div class="flex-grow-1 mt-3 mb-auto" style="height:2px;background:<?= $si < $cur_idx ? '#50cd89' : '#e4e6ea' ?>;"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($b->mechanic_notes)): ?>
                        <div class="alert alert-info d-flex align-items-center p-4">
                            <i class="fa-solid fa-comment-dots text-info me-3 fs-4"></i>
                            <div>
                                <strong>Pesan dari Mekanik: </strong><?= htmlspecialchars($b->mechanic_notes) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

<style>
    @keyframes spin {
        100% {
            transform: rotate(360deg);
        }
    }

    .spinning {
        animation: spin 2s linear infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(0, 158, 247, 0.4);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(0, 158, 247, 0);
        }
    }

    .pulse {
        animation: pulse 1.5s ease infinite;
    }
</style>

<script src="<?= base_url('assets/js/realtime.js') ?>"></script>
<script>
    // Poll status for each active booking every 30s
    document.querySelectorAll('[data-booking-code]').forEach(function(card) {
        var code = card.dataset.bookingCode;
        setInterval(function() {
            RealTime.checkStatus(code, card);
        }, 30000);
    });
</script>