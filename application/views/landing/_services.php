<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Services Section -->
<div class="py-18 bg-light">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-14">
            <span class="badge badge-light-primary rounded-pill px-4 py-2 mb-3 fs-8 fw-semibold">LAYANAN KAMI</span>
            <h2 class="fw-bolder fs-2hx text-gray-900 mb-4">Apa yang Bisa Kami Lakukan?</h2>
            <p class="text-muted fs-5 mx-auto" style="max-width:550px;">Kami menyediakan berbagai layanan perawatan dan perbaikan kendaraan dengan standar kualitas tertinggi.</p>
        </div>

        <!-- Service Cards -->
        <div class="row gy-6">
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm service-card">
                            <div class="card-body p-8">
                                <div class="d-flex align-items-center gap-4 mb-5">
                                    <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-2" style="width:56px;height:56px;">
                                        <i class="<?= htmlspecialchars($service['icon'] ?? 'ki-wrench') ?> fs-1 text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0 text-gray-800"><?= htmlspecialchars($service['name']) ?></h5>
                                        <span class="text-muted fs-8"><i class="fa-regular fa-clock me-1"></i><?= (int)$service['duration_min'] ?> menit</span>
                                    </div>
                                </div>
                                <p class="text-gray-600 fs-6 mb-6 lh-lg"><?= htmlspecialchars($service['description'] ?? '') ?></p>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted fs-8">Mulai dari</span>
                                        <div class="fw-bold text-primary fs-5">Rp <?= number_format($service['base_price'], 0, ',', '.') ?></div>
                                    </div>
                                    <?php if ($this->session->userdata('user_id')): ?>
                                        <a href="<?= base_url('my/booking') ?>?service=<?= (int)$service['id_service'] ?>" class="btn btn-primary btn-sm">
                                            Booking <i class="fa-solid fa-arrow-right ms-1 fs-9"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('register') ?>" class="btn btn-primary btn-sm">
                                            Booking <i class="fa-solid fa-arrow-right ms-1 fs-9"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-12 text-muted">
                    <i class="fa-solid fa-wrench fs-1 d-block mb-4 opacity-30"></i>
                    <p>Layanan akan segera tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>