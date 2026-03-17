<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Testimonials Section -->
<div class="py-18 bg-body">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-14">
            <span class="badge badge-light-warning rounded-pill px-4 py-2 mb-3 fs-8 fw-semibold">TESTIMONI</span>
            <h2 class="fw-bolder fs-2hx text-gray-900 mb-4">Kata Pelanggan Kami</h2>
            <p class="text-muted fs-5 mx-auto" style="max-width:500px;">Kepuasan pelanggan adalah prioritas utama kami.</p>
        </div>

        <?php if (!empty($testimonials)): ?>
            <!-- Carousel -->
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-inner">
                    <?php
                    $chunks = array_chunk($testimonials, 3);
                    foreach ($chunks as $idx => $chunk):
                    ?>
                        <div class="carousel-item <?= ($idx === 0) ? 'active' : '' ?>">
                            <div class="row gy-6 justify-content-center">
                                <?php foreach ($chunk as $t): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="testimonial-card p-8 h-100">
                                            <!-- Stars -->
                                            <div class="d-flex gap-1 mb-4">
                                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                                    <i class="fa-<?= ($s <= (int)$t['rating']) ? 'solid' : 'regular' ?> fa-star text-warning fs-6"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <!-- Content -->
                                            <p class="text-gray-700 fs-6 lh-lg mb-6 fst-italic">"<?= htmlspecialchars($t['content']) ?>"</p>
                                            <!-- Author -->
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if (!empty($t['avatar'])): ?>
                                                    <img src="<?= base_url('uploads/avatars/' . $t['avatar']) ?>" class="rounded-circle" style="width:44px;height:44px;object-fit:cover;" alt="" />
                                                <?php else: ?>
                                                    <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle text-white fw-bold fs-5 flex-shrink-0" style="width:44px;height:44px;">
                                                        <?= strtoupper(substr($t['name'], 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold text-gray-800 fs-7"><?= htmlspecialchars($t['name']) ?></div>
                                                    <div class="text-muted fs-9">Pelanggan Setia</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Controls -->
                <?php if (count($chunks) > 1): ?>
                    <div class="d-flex justify-content-center gap-3 mt-8">
                        <button class="btn btn-light btn-sm btn-icon rounded-circle" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                            <i class="fa-solid fa-chevron-left"></i>
                        </button>
                        <div class="carousel-indicators position-static d-flex align-items-center gap-2 m-0">
                            <?php foreach ($chunks as $idx => $chunk): ?>
                                <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="<?= $idx ?>"
                                    class="<?= ($idx === 0) ? 'active' : '' ?>"
                                    style="width:8px;height:8px;border-radius:50%;background:#009ef7;border:none;outline:none;opacity:<?= ($idx === 0) ? '1' : '0.4' ?>;">
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <button class="btn btn-light btn-sm btn-icon rounded-circle" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-12 text-muted">
                <i class="fa-solid fa-comment-dots fs-1 d-block mb-4 opacity-30"></i>
                <p>Testimoni akan segera hadir.</p>
            </div>
        <?php endif; ?>
    </div>
</div>