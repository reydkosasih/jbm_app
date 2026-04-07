<?php
$parts_total = count($parts);
$parts_active = 0;
$parts_low = count($low_stock);
$stock_value = 0;

foreach ($parts as $part_item) {
    $stock_value += ((float) ($part_item->stock ?? 0) * (float) ($part_item->selling_price ?? 0));
    if (!empty($part_item->is_active)) {
        $parts_active++;
    }
}
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">Spare part inventory</div>
            <h2 class="admin-page-hero__title fw-bold">Jaga stok sparepart tetap sehat dengan ringkasan inventori yang lebih cepat dibaca.</h2>
            <p class="admin-page-hero__desc">Halaman sparepart dirancang ulang agar admin bisa melihat stok rendah, nilai inventori, dan melakukan penyesuaian stok tanpa kehilangan konteks item.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-boxes-stacked"></i><?= $parts_total ?> item tercatat</span>
                <span class="admin-chip"><i class="fa-solid fa-triangle-exclamation"></i><?= $parts_low ?> stok rendah</span>
                <span class="admin-chip"><i class="fa-solid fa-sack-dollar"></i>Nilai stok Rp <?= number_format($stock_value, 0, ',', '.') ?></span>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-box-open fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Total sparepart</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $parts_total ?></div>
                        <div class="text-muted fs-7 mt-2">Semua item inventori yang tersimpan.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-badge-check fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Aktif</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $parts_active ?></div>
                        <div class="text-muted fs-7 mt-2">Item siap dipakai untuk transaksi dan servis.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(245, 158, 11, 0.16); color: #d97706;"><i class="fa-solid fa-triangle-exclamation fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Perlu restock</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $parts_low ?></div>
                        <div class="text-muted fs-7 mt-2">Item yang sudah menyentuh batas minimum.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(14, 165, 233, 0.16); color: #0284c7;"><i class="fa-solid fa-plus fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Aksi cepat</div>
                        <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalAddPart">Tambah sparepart</button>
                        <div class="text-muted fs-7 mt-2">Masukkan item inventori baru langsung dari sini.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($low_stock)): ?>
    <div class="admin-surface-card mb-6">
        <div class="card-body p-5 p-lg-7">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-4">
                <div>
                    <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Peringatan stok</div>
                    <h3 class="fw-bold mb-2">Item dengan stok minimum</h3>
                    <p class="text-muted mb-0"><?= implode(', ', array_map(fn($s) => htmlspecialchars($s->name), $low_stock)) ?></p>
                </div>
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalAddPart">
                    <i class="fa-solid fa-plus me-2"></i>Tambah Sparepart
                </button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="admin-surface-card overflow-hidden">
    <div class="card-header border-0 pt-6 pb-0 px-5 px-lg-7">
        <div>
            <h3 class="fw-bold mb-1">Daftar sparepart</h3>
            <div class="admin-table-meta">Pantau stok, margin jual, dan akses aksi stok dalam satu tabel inventori.</div>
        </div>
    </div>
    <div class="card-body p-0 pt-5">
        <div class="table-responsive">
            <table class="table table-row-dashed align-middle gs-0 gy-0 fs-7 mb-0 admin-data-table">
                <thead>
                    <tr>
                        <th class="ps-5">SKU</th>
                        <th class="min-w-200px">Nama Sparepart</th>
                        <th class="min-w-80px text-end">Stok</th>
                        <th class="min-w-80px text-end">Min. Stok</th>
                        <th class="min-w-100px text-end">Harga Beli</th>
                        <th class="min-w-100px text-end">Harga Jual</th>
                        <th class="min-w-80px">Status</th>
                        <th class="min-w-100px text-end pe-5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($parts)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-10">Belum ada sparepart.</td>
                        </tr>
                        <?php else: foreach ($parts as $p):
                            $low = $p->stock <= $p->min_stock; ?>
                            <tr>
                                <td class="ps-5 text-muted fs-8"><?= htmlspecialchars($p->sku ?? '—') ?></td>
                                <td>
                                    <div class="fw-semibold text-gray-900"><?= htmlspecialchars($p->name) ?></div>
                                </td>
                                <td class="text-end <?= $low ? 'text-danger fw-bold' : '' ?>">
                                    <?= $p->stock ?>
                                    <?php if ($low): ?><i class="fa-solid fa-triangle-exclamation text-danger ms-1 fs-8"></i><?php endif; ?>
                                </td>
                                <td class="text-end text-muted"><?= $p->min_stock ?></td>
                                <td class="text-end">Rp <?= number_format($p->purchase_price ?? 0, 0, ',', '.') ?></td>
                                <td class="text-end fw-bold">Rp <?= number_format($p->selling_price, 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge badge-light-<?= $p->is_active ? 'success' : 'danger' ?> px-4 py-2">
                                        <?= $p->is_active ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-5">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <button class="btn btn-icon btn-sm btn-light-info btn-stock"
                                            data-id="<?= $p->id ?>" data-name="<?= htmlspecialchars($p->name) ?>" title="Atur Stok">
                                            <i class="fa-solid fa-boxes-stacked fs-8"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm btn-light-primary btn-edit"
                                            data-id="<?= $p->id ?>"
                                            data-name="<?= htmlspecialchars($p->name) ?>"
                                            data-sku="<?= htmlspecialchars($p->sku ?? '') ?>"
                                            data-purchase="<?= $p->purchase_price ?? 0 ?>"
                                            data-selling="<?= $p->selling_price ?>"
                                            data-min="<?= $p->min_stock ?>"
                                            data-active="<?= $p->is_active ?>"
                                            title="Edit">
                                            <i class="fa-solid fa-pen fs-8"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                    <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Add Part -->
<div class="modal fade" id="modalAddPart" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label required">Nama Sparepart</label>
                    <input type="text" id="addName" class="form-control" required>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">SKU</label>
                        <input type="text" id="addSku" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" id="addStock" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Min. Stok</label>
                        <input type="number" id="addMinStock" class="form-control" value="5" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Harga Beli (Rp)</label>
                        <input type="number" id="addPurchase" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-12">
                        <label class="form-label required">Harga Jual (Rp)</label>
                        <input type="number" id="addSelling" class="form-control" value="0" min="0">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnAddPart">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Part -->
<div class="modal fade" id="modalEditPart" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="mb-3">
                    <label class="form-label required">Nama</label>
                    <input type="text" id="editName" class="form-control" required>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">SKU</label>
                        <input type="text" id="editSku" class="form-control">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Min. Stok</label>
                        <input type="number" id="editMinStock" class="form-control" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Harga Beli (Rp)</label>
                        <input type="number" id="editPurchase" class="form-control" min="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label required">Harga Jual (Rp)</label>
                        <input type="number" id="editSelling" class="form-control" min="0">
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
                <button class="btn btn-primary" id="btnEditPart">Simpan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Stock Adjustment -->
<div class="modal fade" id="modalStock" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atur Stok: <span id="stockPartName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="stockPartId">
                <div class="mb-3">
                    <label class="form-label required">Tipe</label>
                    <div class="d-flex gap-4">
                        <label class="d-flex gap-2 align-items-center"><input type="radio" name="stock_type" value="in" class="form-check-input mt-0" checked><span>Masuk</span></label>
                        <label class="d-flex gap-2 align-items-center"><input type="radio" name="stock_type" value="out" class="form-check-input mt-0"><span>Keluar</span></label>
                        <label class="d-flex gap-2 align-items-center"><input type="radio" name="stock_type" value="adjustment" class="form-check-input mt-0"><span>Penyesuaian</span></label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Jumlah</label>
                    <input type="number" id="stockQty" class="form-control" value="1" min="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <input type="text" id="stockNote" class="form-control" placeholder="Opsional">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnSaveStock">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Add part
    document.getElementById('btnAddPart').addEventListener('click', function() {
        $.ajax({
            url: BASE_URL + 'admin/spare-parts/store',
            method: 'POST',
            data: {
                name: document.getElementById('addName').value,
                sku: document.getElementById('addSku').value,
                stock: document.getElementById('addStock').value,
                min_stock: document.getElementById('addMinStock').value,
                purchase_price: document.getElementById('addPurchase').value,
                selling_price: document.getElementById('addSelling').value,
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

    // Edit part — open modal
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editId').value = this.dataset.id;
            document.getElementById('editName').value = this.dataset.name;
            document.getElementById('editSku').value = this.dataset.sku;
            document.getElementById('editMinStock').value = this.dataset.min;
            document.getElementById('editPurchase').value = this.dataset.purchase;
            document.getElementById('editSelling').value = this.dataset.selling;
            document.getElementById('editActive').value = this.dataset.active;
            new bootstrap.Modal(document.getElementById('modalEditPart')).show();
        });
    });

    // Save edit
    document.getElementById('btnEditPart').addEventListener('click', function() {
        const id = document.getElementById('editId').value;
        $.ajax({
            url: BASE_URL + 'admin/spare-parts/' + id + '/update',
            method: 'POST',
            data: {
                name: document.getElementById('editName').value,
                sku: document.getElementById('editSku').value,
                min_stock: document.getElementById('editMinStock').value,
                purchase_price: document.getElementById('editPurchase').value,
                selling_price: document.getElementById('editSelling').value,
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

    // Stock adjustment — open modal
    document.querySelectorAll('.btn-stock').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('stockPartId').value = this.dataset.id;
            document.getElementById('stockPartName').textContent = this.dataset.name;
            document.getElementById('stockQty').value = 1;
            document.getElementById('stockNote').value = '';
            document.querySelector('input[name="stock_type"][value="in"]').checked = true;
            new bootstrap.Modal(document.getElementById('modalStock')).show();
        });
    });

    // Save stock
    document.getElementById('btnSaveStock').addEventListener('click', function() {
        const id = document.getElementById('stockPartId').value;
        $.ajax({
            url: BASE_URL + 'admin/spare-parts/' + id + '/adjust-stock',
            method: 'POST',
            data: {
                type: document.querySelector('input[name="stock_type"]:checked').value,
                qty: document.getElementById('stockQty').value,
                note: document.getElementById('stockNote').value,
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