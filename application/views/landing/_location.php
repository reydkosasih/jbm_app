<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Location Section -->
<div class="py-18 bg-light">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-14">
            <span class="badge badge-light-danger rounded-pill px-4 py-2 mb-3 fs-8 fw-semibold">LOKASI</span>
            <h2 class="fw-bolder fs-2hx text-gray-900 mb-4">Temukan Kami di Sini</h2>
            <p class="text-muted fs-5 mx-auto" style="max-width:500px;">Kami mudah dijangkau di lokasi strategis.</p>
        </div>

        <div class="row gy-8 align-items-stretch">
            <!-- Map -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <?php
                    $lat = $settings['workshop_lat'] ?? '-6.2946';
                    $lng = $settings['workshop_lng'] ?? '106.6674';
                    $maps_embed = $settings['workshop_maps_embed'] ?? '';
                    ?>
                    <?php if ($maps_embed): ?>
                        <iframe
                            src="<?= htmlspecialchars($maps_embed) ?>"
                            width="100%"
                            height="400"
                            style="border:0;display:block;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi <?= htmlspecialchars($settings['workshop_name'] ?? 'JBM Bengkel') ?>">
                        </iframe>
                    <?php else: ?>
                        <iframe
                            src="https://maps.google.com/maps?q=<?= urlencode($lat . ',' . $lng) ?>&output=embed&z=16"
                            width="100%"
                            height="400"
                            style="border:0;display:block;"
                            allowfullscreen=""
                            loading="lazy"
                            title="Lokasi Bengkel">
                        </iframe>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100 p-8">
                    <h5 class="fw-bold text-gray-900 mb-6 fs-5">Informasi Bengkel</h5>

                    <div class="d-flex align-items-start gap-4 mb-6">
                        <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-2 flex-shrink-0 mt-1" style="width:40px;height:40px;">
                            <i class="fa-solid fa-location-dot text-primary fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-gray-700 mb-1">Alamat</div>
                            <div class="text-muted fs-6"><?= htmlspecialchars($settings['workshop_address'] ?? '') ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-4 mb-6">
                        <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-2 flex-shrink-0 mt-1" style="width:40px;height:40px;">
                            <i class="fa-solid fa-clock text-success fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-gray-700 mb-1">Jam Operasional</div>
                            <div class="text-muted fs-6"><?= htmlspecialchars($settings['operating_hours'] ?? 'Senin - Sabtu: 08.00 - 17.00 WIB') ?></div>
                            <div class="text-danger fs-8 mt-1"><i class="fa-solid fa-ban me-1"></i>Tutup pada hari Minggu &amp; hari libur nasional</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-4 mb-6">
                        <div class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-2 flex-shrink-0 mt-1" style="width:40px;height:40px;">
                            <i class="fa-solid fa-phone text-warning fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-gray-700 mb-1">Telepon / WhatsApp</div>
                            <a href="tel:<?= htmlspecialchars($settings['workshop_phone'] ?? '') ?>" class="text-muted fs-6 text-decoration-none d-block">
                                <?= htmlspecialchars($settings['workshop_phone'] ?? '') ?>
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-4 mb-8">
                        <div class="d-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-2 flex-shrink-0 mt-1" style="width:40px;height:40px;">
                            <i class="fa-solid fa-envelope text-info fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-gray-700 mb-1">Email</div>
                            <a href="mailto:<?= htmlspecialchars($settings['workshop_email'] ?? '') ?>" class="text-muted fs-6 text-decoration-none">
                                <?= htmlspecialchars($settings['workshop_email'] ?? '') ?>
                            </a>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="d-flex flex-wrap gap-3">
                        <a href="https://www.google.com/maps?q=<?= urlencode($lat . ',' . $lng) ?>"
                            target="_blank"
                            class="btn btn-primary flex-grow-1">
                            <i class="fa-solid fa-map-location-dot me-2"></i>Petunjuk Arah
                        </a>
                        <a href="https://wa.me/<?= htmlspecialchars($settings['workshop_wa'] ?? '') ?>?text=Halo+JBM+Bengkel"
                            target="_blank"
                            class="btn btn-success">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>