<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Manajemen Layanan</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddService">
        <i class="fa-solid fa-plus me-2"></i>Tambah Layanan
    </button>
</div>

<div class="card card-flush">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 fs-7">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start min-w-50px">#</th>
                        <th class="min-w-200px">Nama Layanan</th>
                        <th class="min-w-250px">Deskripsi</th>
                        <th class="min-w-110px text-end">Harga Dasar</th>
                        <th class="min-w-90px text-center">Durasi</th>
                        <th class="min-w-80px text-center">Status</th>
                        <th class="min-w-80px text-end rounded-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($services)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-10">Belum ada layanan.</td>
                        </tr>
                        <?php else: foreach ($services as $i => $s): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $i + 1 ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($s['name']) ?></td>
                                <td class="text-gray-600"><?= htmlspecialchars(mb_strimwidth($s['description'] ?? '', 0, 80, '...')) ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($s['base_price'], 0, ',', '.') ?></td>
                                <td class="text-center text-muted"><?= $s['duration_min'] ?> mnt</td>
                                <td class="text-center">
                                    <span class="badge badge-light-<?= $s['is_active'] ? 'success' : 'danger' ?>">
                                        <?= $s['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
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