<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Gallery Section -->
<div class="py-18 bg-light">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-14">
            <span class="badge badge-light-info rounded-pill px-4 py-2 mb-3 fs-8 fw-semibold">GALERI</span>
            <h2 class="fw-bolder fs-2hx text-gray-900 mb-4">Dokumentasi Bengkel Kami</h2>
            <p class="text-muted fs-5 mx-auto" style="max-width:500px;">Lihat fasilitas dan proses pengerjaan kami yang modern dan profesional.</p>
        </div>

        <?php if (!empty($gallery)): ?>
            <!-- Gallery Grid -->
            <div class="row gy-4">
                <?php foreach ($gallery as $index => $item): ?>
                    <div class="col-6 col-md-4 col-lg-4">
                        <div class="gallery-item rounded-3 overflow-hidden shadow-sm position-relative"
                            style="cursor:pointer;"
                            data-bs-toggle="modal" data-bs-target="#gallery_modal"
                            data-title="<?= htmlspecialchars($item['title']) ?>"
                            data-desc="<?= htmlspecialchars($item['description'] ?? '') ?>"
                            data-img="<?= base_url('uploads/gallery/' . $item['image']) ?>">
                            <img src="<?= base_url('uploads/gallery/' . $item['image']) ?>"
                                alt="<?= htmlspecialchars($item['title']) ?>"
                                class="w-100"
                                style="height:220px;object-fit:cover;"
                                onerror="this.src='<?= base_url('theme/assets/media/misc/image.png') ?>';" />
                            <div class="position-absolute inset-0 d-flex align-items-end p-4"
                                style="background:linear-gradient(transparent,rgba(0,0,0,0.65));top:0;left:0;right:0;bottom:0;">
                                <span class="text-white fw-semibold fs-7"><?= htmlspecialchars($item['title']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Gallery Modal (Lightbox) -->
            <div class="modal fade" id="gallery_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="gallery_modal_title"></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <img id="gallery_modal_img" src="" alt="" class="w-100 rounded-bottom" style="max-height:500px;object-fit:cover;" />
                        </div>
                        <div class="modal-footer border-0">
                            <p class="text-muted fs-7 mb-0" id="gallery_modal_desc"></p>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.querySelectorAll('.gallery-item').forEach(function(el) {
                    el.addEventListener('click', function() {
                        document.getElementById('gallery_modal_title').textContent = this.dataset.title;
                        document.getElementById('gallery_modal_img').src = this.dataset.img;
                        document.getElementById('gallery_modal_desc').textContent = this.dataset.desc;
                    });
                });
            </script>

        <?php else: ?>
            <div class="text-center py-12 text-muted">
                <i class="fa-regular fa-images fs-1 d-block mb-4 opacity-30"></i>
                <p>Galeri sedang dipersiapkan.</p>
            </div>
        <?php endif; ?>
    </div>
</div>