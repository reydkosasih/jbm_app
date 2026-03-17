<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Hero Section -->
<div class="landing-hero d-flex align-items-center position-relative overflow-hidden" id="home">
    <!-- Animated background circles -->
    <div class="position-absolute" style="width:600px;height:600px;background:radial-gradient(circle,rgba(0,158,247,0.15) 0%,transparent 70%);top:-100px;right:-100px;pointer-events:none;"></div>
    <div class="position-absolute" style="width:400px;height:400px;background:radial-gradient(circle,rgba(80,205,137,0.1) 0%,transparent 70%);bottom:-100px;left:-50px;pointer-events:none;"></div>

    <div class="container py-20 py-lg-28 position-relative z-index-1">
        <div class="row align-items-center gy-10">
            <div class="col-lg-6">
                <!-- Badge -->
                <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-10 rounded-pill px-4 py-2 mb-6">
                    <span class="badge bg-primary rounded-pill">Terpercaya</span>
                    <span class="text-white fs-7 fw-semibold">10+ Tahun Melayani Pelanggan</span>
                </div>

                <!-- Headline -->
                <h1 class="text-white fw-bolder lh-sm mb-6" style="font-size:clamp(2rem,4vw,3.5rem);">
                    Servis Mobil <br />
                    <span style="background:linear-gradient(90deg,#009ef7,#50cd89);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;">
                        Terpercaya
                    </span>
                    di <?= htmlspecialchars($settings['workshop_city'] ?? 'Karawang Timur') ?>
                </h1>

                <p class="text-gray-300 fs-5 mb-10 lh-lg">
                    <?= htmlspecialchars($settings['workshop_tagline'] ?? 'Mekanik bersertifikat, peralatan modern, dan pelayanan transparan. Booking online mudah & cepat.') ?>
                </p>

                <!-- CTA Buttons -->
                <div class="d-flex flex-wrap gap-4">
                    <?php if ($this->session->userdata('user_id')): ?>
                        <a href="<?= base_url('my/booking') ?>" class="btn btn-primary btn-lg px-8">
                            <i class="fa-solid fa-calendar-plus me-2"></i>Booking Sekarang
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('register') ?>" class="btn btn-primary btn-lg px-8">
                            <i class="fa-solid fa-calendar-plus me-2"></i>Booking Sekarang
                        </a>
                    <?php endif; ?>
                    <a href="#services" class="btn btn-outline btn-outline-light text-white btn-lg px-8" data-kt-scroll-toggle="true">
                        <i class="fa-solid fa-list me-2"></i>Lihat Layanan
                    </a>
                </div>

                <!-- Trust indicators -->
                <div class="d-flex flex-wrap gap-6 mt-10">
                    <div class="d-flex align-items-center gap-2 text-gray-300">
                        <i class="fa-solid fa-shield-halved text-success"></i>
                        <span class="fs-7">Garansi Servis</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-gray-300">
                        <i class="fa-solid fa-clock text-warning"></i>
                        <span class="fs-7">Tepat Waktu</span>
                    </div>
                    <div class="d-flex align-items-center gap-2 text-gray-300">
                        <i class="fa-solid fa-tag text-info"></i>
                        <span class="fs-7">Harga Transparan</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 d-none d-lg-flex justify-content-center">
                <!-- Visual stat cards -->
                <div class="position-relative" style="width:420px;height:380px;">
                    <div class="card shadow-lg border-0 position-absolute" style="top:20px;left:0;width:200px;border-radius:16px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-success bg-opacity-15 rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                    <i class="fa-solid fa-check text-success fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-gray-800 fs-3 counter" data-target="<?= $stats['total_customers'] ?? 500 ?>">0</div>
                                    <div class="text-muted fs-9">Pelanggan Puas</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-lg border-0 position-absolute" style="top:140px;right:0;width:220px;border-radius:16px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="bg-primary bg-opacity-15 rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                    <i class="fa-solid fa-wrench text-primary fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-gray-800 fs-3"><?= $stats['certified_mechanics'] ?? 3 ?></div>
                                    <div class="text-muted fs-9">Mekanik Bersertifikat</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-lg border-0 position-absolute" style="bottom:10px;left:30px;width:180px;border-radius:16px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-warning bg-opacity-15 rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                    <i class="fa-solid fa-star text-warning fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-gray-800 fs-3"><?= number_format($stats['star_rating'] ?? 4.9, 1) ?></div>
                                    <div class="text-muted fs-9">Rating Bintang</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Curve bottom -->
<div class="landing-curve landing-dark-color mb-0">
    <svg viewBox="15 12 1470 48" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M0 11C3.93573 11.3356 7.85984 11.6689 11.7725 12H1488.16C1492.1 11.6689 1496.04 11.3356 1500 11V12H1488.16C913.668 60.3476 586.282 60.6117 11.7725 12H0V11Z" fill="currentColor"></path>
    </svg>
</div>