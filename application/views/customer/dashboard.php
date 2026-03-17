<!-- Customer Dashboard -->
<div class="d-flex flex-column flex-xl-row gap-7 gap-lg-10 mb-7">
    <!-- Welcome card -->
    <div class="card card-flush flex-row-fluid overflow-hidden" style="background: linear-gradient(135deg, #0f3460 0%, #1a1a2e 100%);">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <span class="fw-bold text-white fs-2hx lh-1">
                    Selamat Datang, <?= htmlspecialchars(explode(' ', $user->name)[0]) ?>!
                </span>
                <span class="text-white opacity-75 pt-1 fw-semibold fs-6"><?= date('l, d F Y') ?></span>
            </div>
        </div>
        <div class="card-body d-flex align-items-end pt-0">
            <div class="d-flex align-items-center flex-column mt-3 w-100">
                <div class="d-flex justify-content-between fw-bold fs-6 text-white opacity-75 w-100 mt-auto mb-2">
                    <span>Profile Lengkap</span>
                    <span><?= (!empty($user->phone) && !empty($user->address)) ? '100%' : '60%' ?></span>
                </div>
                <div class="h-8px mx-3 w-100 bg-white bg-opacity-25 rounded">
                    <div class="bg-white rounded h-8px" role="progressbar"
                        style="width: <?= (!empty($user->phone) && !empty($user->address)) ? '100%' : '60%' ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick stats -->
    <div class="card card-flush w-xl-300px">
        <div class="card-header pt-5">
            <div class="card-title d-flex flex-column">
                <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1"><?= count($vehicles) ?></span>
                <span class="text-gray-500 pt-1 fw-semibold fs-6">Kendaraan Terdaftar</span>
            </div>
        </div>
        <div class="card-body d-flex align-items-end pt-0">
            <a href="<?= base_url('my/vehicles') ?>" class="btn btn-sm btn-light-primary w-100">
                <i class="fa-solid fa-car me-2"></i>Kelola Kendaraan
            </a>
        </div>
    </div>
</div>

<!-- Active service banner -->
<?php if (!empty($active_bookings)): ?>
    <?php foreach ($active_bookings as $ab): ?>
        <div class="alert alert-primary d-flex align-items-center p-5 mb-7">
            <i class="fa-solid fa-wrench fs-2hx text-primary me-4 blink"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-primary">Kendaraan Sedang Diservis</h4>
                <span class="fs-6">
                    <strong><?= htmlspecialchars($ab->plate_number) ?></strong> —
                    <?= htmlspecialchars($ab->service_name) ?>
                    (<?= htmlspecialchars($ab->booking_code) ?>)
                </span>
            </div>
            <a href="<?= base_url('my/status') ?>" class="btn btn-sm btn-primary ms-auto">Lihat Status</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Action cards -->
<div class="row g-5 g-xl-8 mb-8">
    <div class="col-xl-3 col-sm-6">
        <a href="<?= base_url('my/booking') ?>" class="card bg-primary hoverable card-xl-stretch mb-xl-8">
            <div class="card-body text-center py-10">
                <i class="fa-solid fa-calendar-plus text-white mb-4" style="font-size:40px;"></i>
                <div class="text-white fw-bold fs-4">Booking Baru</div>
                <div class="text-white opacity-75 fs-7">Jadwalkan servis kendaraan</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?= base_url('my/status') ?>" class="card bg-info hoverable card-xl-stretch mb-xl-8">
            <div class="card-body text-center py-10">
                <i class="fa-solid fa-magnifying-glass text-white mb-4" style="font-size:40px;"></i>
                <div class="text-white fw-bold fs-4">Status Servis</div>
                <div class="text-white opacity-75 fs-7">Pantau progress kendaraan</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?= base_url('my/history') ?>" class="card bg-success hoverable card-xl-stretch mb-xl-8">
            <div class="card-body text-center py-10">
                <i class="fa-solid fa-clock-rotate-left text-white mb-4" style="font-size:40px;"></i>
                <div class="text-white fw-bold fs-4">Riwayat</div>
                <div class="text-white opacity-75 fs-7">Histori servis lengkap</div>
            </div>
        </a>
    </div>
    <div class="col-xl-3 col-sm-6">
        <a href="<?= base_url('my/vehicles') ?>" class="card bg-warning hoverable card-xl-stretch mb-xl-8">
            <div class="card-body text-center py-10">
                <i class="fa-solid fa-car text-white mb-4" style="font-size:40px;"></i>
                <div class="text-white fw-bold fs-4">Kendaraan Saya</div>
                <div class="text-white opacity-75 fs-7">Data kendaraan terdaftar</div>
            </div>
        </a>
    </div>
</div>

<!-- Recent bookings -->
<div class="card card-flush">
    <div class="card-header mt-6">
        <div class="card-title flex-column">
            <h3 class="fw-bold mb-1">Booking Terbaru</h3>
            <div class="text-muted fw-semibold fs-6">5 booking terakhir Anda</div>
        </div>
        <div class="card-toolbar">
            <a href="<?= base_url('my/history') ?>" class="btn btn-sm btn-light">Lihat Semua</a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recent)): ?>
            <div class="text-center py-15">
                <i class="fa-regular fa-calendar-xmark fs-3x text-muted mb-4"></i>
                <p class="text-muted fs-6">Belum ada booking.</p>
                <a href="<?= base_url('my/booking') ?>" class="btn btn-sm btn-primary">Booking Sekarang</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted bg-light">
                            <th class="ps-4 min-w-150px rounded-start">Kode Booking</th>
                            <th class="min-w-120px">Layanan</th>
                            <th class="min-w-100px">Plat Nomor</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-80px text-end rounded-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $b): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-dark fw-bold text-hover-primary fs-7"><?= htmlspecialchars($b->booking_code) ?></span>
                                </td>
                                <td><span class="text-gray-700 fs-7"><?= htmlspecialchars($b->service_name) ?></span></td>
                                <td><span class="badge badge-light-secondary fs-8"><?= htmlspecialchars($b->plate_number) ?></span></td>
                                <td><span class="text-gray-600 fs-7"><?= date('d/m/Y', strtotime($b->booking_date)) ?></span></td>
                                <td><?= $this->_status_badge($b->status) ?></td>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url('my/booking/' . $b->id) ?>" class="btn btn-icon btn-sm btn-light-primary">
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

<?php
// Helper function for status badge
function _status_badge($status)
{
    $map = [
        'pending'              => ['warning',  'Menunggu'],
        'confirmed'            => ['primary',  'Dikonfirmasi'],
        'in_progress'          => ['info',     'Diservis'],
        'completed'            => ['success',  'Selesai'],
        'cancelled'            => ['danger',   'Dibatalkan'],
        'waiting_payment'      => ['warning',  'Menunggu Bayar'],
        'waiting_confirmation' => ['warning',  'Konfirmasi Bayar'],
        'paid'                 => ['success',  'Lunas'],
    ];
    $s   = $map[$status] ?? ['secondary', ucfirst($status)];
    return '<span class="badge badge-light-' . $s[0] . '">' . $s[1] . '</span>';
}
?>

<style>
    .blink {
        animation: blink 1.5s step-start infinite;
    }

    @keyframes blink {
        50% {
            opacity: 0.3;
        }
    }
</style>