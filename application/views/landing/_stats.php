<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Stats Section -->
<div class="py-15 bg-body" id="stats">
    <div class="container">
        <div class="row gy-6 justify-content-center text-center">
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 p-6">
                    <div class="counter-number mb-2" data-target="<?= $stats['total_customers'] ?? 500 ?>">0</div>
                    <div class="fw-bold text-gray-700 fs-6">Total Pelanggan</div>
                    <div class="text-muted fs-8 mt-1">yang pernah dilayani</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 p-6">
                    <div class="counter-number mb-2" data-target="<?= $stats['years_experience'] ?? 10 ?>">0</div>
                    <div class="fw-bold text-gray-700 fs-6">Tahun Pengalaman</div>
                    <div class="text-muted fs-8 mt-1">melayani dengan tulus</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 p-6">
                    <div class="counter-number mb-2" data-target="<?= $stats['certified_mechanics'] ?? 8 ?>">0</div>
                    <div class="fw-bold text-gray-700 fs-6">Mekanik Bersertifikat</div>
                    <div class="text-muted fs-8 mt-1">profesional & berpengalaman</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100 p-6">
                    <div class="d-flex align-items-center justify-content-center gap-1 mb-2">
                        <span class="counter-number" data-target="4" data-decimal="true"><?= number_format($stats['star_rating'] ?? 4.9, 1) ?></span>
                        <i class="fa-solid fa-star text-warning fs-2" style="margin-top:-4px;"></i>
                    </div>
                    <div class="fw-bold text-gray-700 fs-6">Rating Kepuasan</div>
                    <div class="text-muted fs-8 mt-1">berdasarkan review pelanggan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Counter animation
    (function() {
        function animateCounter(el) {
            var target = parseInt(el.getAttribute('data-target'), 10);
            var duration = 2000;
            var step = target / (duration / 16);
            var current = 0;
            var timer = setInterval(function() {
                current += step;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = Math.floor(current).toLocaleString('id-ID');
            }, 16);
        }

        var observed = false;
        var statsSection = document.getElementById('stats');
        if (!statsSection) return;

        var observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting && !observed) {
                observed = true;
                document.querySelectorAll('.counter-number').forEach(animateCounter);
            }
        }, {
            threshold: 0.3
        });

        observer.observe(statsSection);
    })();
</script>