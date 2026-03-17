<?php
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
?>
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Galeri Bengkel</h1>
</div>

<!-- Upload Area -->
<div class="card card-flush mb-6">
    <div class="card-header">
        <h3 class="card-title">Upload Foto</h3>
    </div>
    <div class="card-body">
        <div id="uploadDropzone" class="border border-dashed border-primary rounded-2 p-8 text-center" style="cursor:pointer">
            <i class="fa-solid fa-cloud-arrow-up fs-2x text-primary mb-3"></i>
            <p class="text-muted fs-7 mb-2">Klik atau seret foto ke area ini</p>
            <p class="text-muted fs-8">Format: JPG, PNG, WEBP — Maks. 2MB per file. Multiple files didukung.</p>
            <input type="file" id="galleryFiles" accept="image/jpeg,image/png,image/webp" multiple class="d-none">
        </div>
        <div id="uploadPreviews" class="d-flex flex-wrap gap-3 mt-4"></div>
        <div id="uploadProgress" class="mt-3 d-none">
            <div class="progress h-8px">
                <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" id="uploadBar" style="width:0%"></div>
            </div>
            <p class="text-muted fs-8 mt-1 mb-0" id="uploadStatus">Mengupload...</p>
        </div>
        <div class="mt-4">
            <button class="btn btn-primary" id="btnUploadGallery" disabled>
                <i class="fa-solid fa-upload me-2"></i>Upload Foto
            </button>
        </div>
    </div>
</div>

<!-- Gallery Grid -->
<div class="row g-4" id="galleryGrid">
    <?php if (empty($gallery)): ?>
        <div class="col-12 text-center text-muted py-10">
            <i class="fa-regular fa-images fs-3x mb-4"></i>
            <p>Belum ada foto galeri.</p>
        </div>
        <?php else: foreach ($gallery as $g): ?>
            <div class="col-xl-3 col-md-4 col-sm-6" id="gallery-item-<?= $g['id_gallery'] ?>">
                <div class="card card-flush overflow-hidden position-relative">
                    <div class="card-body p-0">
                        <img src="<?= base_url('uploads/gallery/' . $g['image']) ?>"
                            alt="<?= htmlspecialchars($g['title'] ?? '') ?>"
                            class="w-100 gallery-thumb"
                            style="height:200px;object-fit:cover;cursor:pointer"
                            data-src="<?= base_url('uploads/gallery/' . $g['image']) ?>">
                    </div>
                    <div class="card-footer py-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-8 text-truncate" style="max-width:120px">
                            <?= htmlspecialchars($g['title'] ?? basename($g['image'])) ?>
                        </span>
                        <div class="d-flex gap-2">
                            <span class="badge badge-light-<?= $g['is_active'] ? 'success' : 'danger' ?> fs-9">
                                <?= $g['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                            <button class="btn btn-icon btn-sm btn-light-danger btn-del-gallery" data-id="<?= $g['id_gallery'] ?>">
                                <i class="fa-solid fa-trash fs-9"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
    <?php endforeach;
    endif; ?>
</div>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark border-0">
            <div class="modal-body p-2 text-center">
                <img src="" id="lightboxImg" class="img-fluid rounded" style="max-height:80vh">
            </div>
        </div>
    </div>
</div>

<script>
    const CSRF_NAME = '<?= $csrf_name ?>';
    let CSRF_HASH = '<?= $csrf_hash ?>';

    function hdrs() {
        return {
            'X-CSRF-Token': CSRF_HASH
        };
    }

    // Dropzone click
    const dropzone = document.getElementById('uploadDropzone');
    const fileInput = document.getElementById('galleryFiles');
    const previews = document.getElementById('uploadPreviews');
    const uploadBtn = document.getElementById('btnUploadGallery');

    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', e => {
        e.preventDefault();
        dropzone.classList.add('border-primary');
    });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('border-primary'));
    dropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropzone.classList.remove('border-primary');
        fileInput.files = e.dataTransfer.files;
        handleFiles(fileInput.files);
    });
    fileInput.addEventListener('change', function() {
        handleFiles(this.files);
    });

    function handleFiles(files) {
        previews.innerHTML = '';
        Array.from(files).forEach(function(f) {
            if (!f.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'rounded-2 border';
                img.style.cssText = 'width:100px;height:80px;object-fit:cover';
                previews.appendChild(img);
            };
            reader.readAsDataURL(f);
        });
        uploadBtn.disabled = files.length === 0;
    }

    // Upload
    uploadBtn.addEventListener('click', function() {
        const files = fileInput.files;
        if (!files.length) return;

        const bar = document.getElementById('uploadBar');
        const status = document.getElementById('uploadStatus');
        const prog = document.getElementById('uploadProgress');
        prog.classList.remove('d-none');
        uploadBtn.disabled = true;

        let done = 0;

        Array.from(files).forEach(function(file) {
            const fd = new FormData();
            fd.append('image', file);
            fd.append(CSRF_NAME, CSRF_HASH);

            $.ajax({
                url: BASE_URL + 'admin/gallery/upload',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: hdrs(),
                success: function(r) {
                    CSRF_HASH = r.csrf_hash || CSRF_HASH;
                    done++;
                    const pct = Math.round((done / files.length) * 100);
                    bar.style.width = pct + '%';
                    status.textContent = 'Mengupload ' + done + '/' + files.length + '...';
                    if (done === files.length) {
                        setTimeout(() => location.reload(), 800);
                    }
                },
                error: function() {
                    done++;
                    Swal.fire({
                        icon: 'error',
                        text: 'Gagal upload ' + file.name
                    });
                }
            });
        });
    });

    // Delete gallery
    document.querySelectorAll('.btn-del-gallery').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                icon: 'warning',
                title: 'Hapus foto ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus'
            }).then(function(res) {
                if (!res.isConfirmed) return;
                const data = {};
                data[CSRF_NAME] = CSRF_HASH;
                $.ajax({
                    url: BASE_URL + 'admin/gallery/' + id + '/delete',
                    method: 'POST',
                    headers: hdrs(),
                    data: data,
                    success: function(r) {
                        CSRF_HASH = r.csrf_hash || CSRF_HASH;
                        if (r.success) {
                            document.getElementById('gallery-item-' + id).remove();
                            Swal.fire({
                                icon: 'success',
                                title: 'Dihapus!',
                                timer: 900,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: r.message
                            });
                        }
                    }
                });
            });
        });
    });

    // Lightbox
    document.querySelectorAll('.gallery-thumb').forEach(function(img) {
        img.addEventListener('click', function() {
            document.getElementById('lightboxImg').src = this.dataset.src;
            new bootstrap.Modal(document.getElementById('lightboxModal')).show();
        });
    });
</script>