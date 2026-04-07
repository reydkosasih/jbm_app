<?php
$service_total = count($services);
$service_active = 0;
$service_inactive = 0;
$average_price = 0;

foreach ($services as $service_item) {
    $average_price += (float) ($service_item['base_price'] ?? 0);
    if (!empty($service_item['is_active'])) {
        $service_active++;
    } else {
        $service_inactive++;
    }
}

$average_price = $service_total > 0 ? $average_price / $service_total : 0;
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">Service catalog</div>
            <h2 class="admin-page-hero__title fw-bold">Susun daftar layanan bengkel dengan struktur harga dan durasi yang lebih mudah dipantau.</h2>
            <p class="admin-page-hero__desc">Halaman layanan dirapikan agar admin cepat mengaudit katalog servis, menambah layanan baru, dan memperbarui detail tanpa tenggelam di tabel lama.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-screwdriver-wrench"></i><?= $service_total ?> layanan tercatat</span>
                <span class="admin-chip"><i class="fa-solid fa-circle-check"></i><?= $service_active ?> aktif</span>
                <span class="admin-chip"><i class="fa-solid fa-clock"></i>Rata-rata <?= $service_total > 0 ? number_format($average_price, 0, ',', '.') : 0 ?> / layanan</span>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-list fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Total layanan</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $service_total ?></div>
                        <div class="text-muted fs-7 mt-2">Semua item katalog servis bengkel.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-badge-check fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Aktif</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $service_active ?></div>
                        <div class="text-muted fs-7 mt-2">Layanan yang tampil dalam operasional.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(239, 68, 68, 0.14); color: #dc2626;"><i class="fa-solid fa-ban fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Nonaktif</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $service_inactive ?></div>
                        <div class="text-muted fs-7 mt-2">Disimpan untuk arsip atau revisi.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(14, 165, 233, 0.16); color: #0284c7;"><i class="fa-solid fa-plus fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Aksi cepat</div>
                        <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalAddService">Tambah layanan</button>
                        <div class="text-muted fs-7 mt-2">Buat item baru tanpa pindah halaman.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="admin-surface-card mb-6">
    <div class="card-body p-5 p-lg-7 d-flex flex-wrap align-items-center justify-content-between gap-4">
        <div>
            <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Kontrol katalog</div>
            <h3 class="fw-bold mb-0">Kelola harga, durasi, dan status layanan</h3>
        </div>
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalAddService">
            <i class="fa-solid fa-plus me-2"></i>Tambah Layanan
        </button>
    </div>
</div>

<div class="admin-surface-card overflow-hidden">
    <div class="card-header border-0 pt-6 pb-0 px-5 px-lg-7">
        <div>
            <h3 class="fw-bold mb-1">Daftar layanan</h3>
            <div class="admin-table-meta">Daftar harga dan parameter layanan dengan tabel yang lebih mudah dibaca di desktop maupun mobile.</div>
        </div>
    </div>
    <div class="card-body p-0 pt-5">
        <div class="table-responsive">
            <table class="table table-row-dashed align-middle gs-0 gy-0 fs-7 mb-0 admin-data-table">
                <thead>
                    <tr>
                        <th class="ps-5">Layanan</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Harga dasar</th>
                        <th class="text-center">Durasi</th>
                        <th>Status</th>
                        <th class="text-end pe-5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-12">Belum ada layanan.</td>
                        </tr>
                        <?php else: foreach ($services as $i => $s): ?>
                            <tr>
                                <td class="ps-5">
                                    <div class="fw-semibold text-gray-900"><?= htmlspecialchars($s['name']) ?></div>
                                    <div class="admin-table-meta mt-1">Layanan #<?= $i + 1 ?></div>
                                </td>
                                <td class="text-gray-600"><?= htmlspecialchars(mb_strimwidth($s['description'] ?? '', 0, 80, '...')) ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($s['base_price'], 0, ',', '.') ?></td>
                                <td class="text-center text-muted"><?= $s['duration_min'] ?> mnt</td>
                                <td>
                                    <span class="badge badge-light-<?= $s['is_active'] ? 'success' : 'danger' ?> px-4 py-2">
                                        <?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-5">
                                    <button class="btn btn-icon btn-sm btn-light-primary btn-edit"
                                        data-id="<?= $s['id_service'] ?>"
                                        data-name="<?= htmlspecialchars($s['name']) ?>"
                                        data-desc="<?= htmlspecialchars($s['description'] ?? '') ?>"
                                        data-price="<?= $s['base_price'] ?>"
                                        data-duration="<?= $s['duration_min'] ?>"
                                        data-active="<?= $s['is_active'] ?>"
                                        title="Edit">
                                        <i class="fa-solid fa-pen fs-8"></i>
                                    </button>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Service -->
<div class="modal fade" id="modalAddService" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label required">Nama Layanan</label>
                    <input type="text" id="addName" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="addDesc" class="form-control" rows="3"></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label required">Harga Dasar (Rp)</label>
                        <input type="number" id="addPrice" class="form-control" min="0" value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Durasi (menit)</label>
                        <input type="number" id="addDuration" class="form-control" min="15" value="60">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnAddService">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Service -->
<div class="modal fade" id="modalEditService" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label required">Nama Layanan</label>
                    <input type="text" id="editName" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea id="editDesc" class="form-control" rows="3"></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label required">Harga Dasar (Rp)</label>
                        <input type="number" id="editPrice" class="form-control" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Durasi (menit)</label>
                        <input type="number" id="editDuration" class="form-control" min="15">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Status</label>
                        <select id="editActive" class="form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnEditService">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnAddService').addEventListener('click', function() {
        $.ajax({
            url: BASE_URL + 'admin/services/store',
            method: 'POST',
            data: {
                name: document.getElementById('addName').value,
                description: document.getElementById('addDesc').value,
                base_price: document.getElementById('addPrice').value,
                duration_min: document.getElementById('addDuration').value,
            },
            success: r => {
                if (r.success) location.reload();
                else Swal.fire({
                    icon: 'error',
                    text: r.message
                });
            }
        });
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editId').value = this.dataset.id;
            document.getElementById('editName').value = this.dataset.name;
            document.getElementById('editDesc').value = this.dataset.desc;
            document.getElementById('editPrice').value = this.dataset.price;
            document.getElementById('editDuration').value = this.dataset.duration;
            document.getElementById('editActive').value = this.dataset.active;
            new bootstrap.Modal(document.getElementById('modalEditService')).show();
        });
    });

    document.getElementById('btnEditService').addEventListener('click', function() {
        const id = document.getElementById('editId').value;
        $.ajax({
            url: BASE_URL + 'admin/services/' + id + '/update',
            method: 'POST',
            data: {
                name: document.getElementById('editName').value,
                description: document.getElementById('editDesc').value,
                base_price: document.getElementById('editPrice').value,
                duration_min: document.getElementById('editDuration').value,
                is_active: document.getElementById('editActive').value,
            },
            success: r => {
                if (r.success) location.reload();
                else Swal.fire({
                    icon: 'error',
                    text: r.message
                });
            }
        });
    });
</script>