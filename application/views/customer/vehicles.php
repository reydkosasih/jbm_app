<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Kendaraan Saya</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
        <i class="fa-solid fa-plus me-2"></i>Tambah Kendaraan
    </button>
</div>

<?php if (empty($vehicles)): ?>
    <div class="card card-flush">
        <div class="card-body text-center py-20">
            <i class="fa-solid fa-car fs-3x text-muted mb-5"></i>
            <h4 class="text-muted">Belum Ada Kendaraan</h4>
            <p class="text-muted fs-6">Tambahkan kendaraan Anda untuk memudahkan booking servis.</p>
            <button type="button" class="btn btn-primary mt-4" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                <i class="fa-solid fa-plus me-2"></i>Tambah Kendaraan Sekarang
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="row g-5">
        <?php foreach ($vehicles as $v): ?>
            <div class="col-md-6 col-xl-4">
                <div class="card card-flush h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="d-flex align-items-center justify-content-center bg-light-primary rounded" style="width:60px;height:60px;">
                                <i class="fa-solid fa-car text-primary fs-2"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0"><?= htmlspecialchars($v->brand . ' ' . $v->model) ?></h4>
                                <span class="badge badge-light-primary mt-1"><?= htmlspecialchars($v->plate_number) ?></span>
                            </div>
                        </div>
                        <div class="row g-3 fs-7">
                            <div class="col-6">
                                <span class="text-muted d-block">Tahun</span>
                                <span class="fw-semibold"><?= $v->year ?></span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Warna</span>
                                <span class="fw-semibold"><?= htmlspecialchars($v->color ?: '-') ?></span>
                            </div>
                            <?php if (!empty($v->engine_no)): ?>
                                <div class="col-12">
                                    <span class="text-muted d-block">No. Mesin</span>
                                    <span class="fw-semibold fs-8 text-break"><?= htmlspecialchars($v->engine_no) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer d-flex gap-2 py-3">
                        <button type="button" class="btn btn-sm btn-light-primary flex-grow-1 edit-vehicle-btn"
                            data-id="<?= $v->id ?>"
                            data-brand="<?= htmlspecialchars($v->brand) ?>"
                            data-model="<?= htmlspecialchars($v->model) ?>"
                            data-year="<?= $v->year ?>"
                            data-color="<?= htmlspecialchars($v->color) ?>"
                            data-engine="<?= htmlspecialchars($v->engine_no ?? '') ?>"
                            data-chassis="<?= htmlspecialchars($v->chassis_no ?? '') ?>">
                            <i class="fa-solid fa-pen me-1"></i>Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-light-danger delete-vehicle-btn" data-id="<?= $v->id ?>">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label required">Plat Nomor</label>
                        <input type="text" id="v_plate" class="form-control" placeholder="B 1234 ABC" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Merek</label>
                        <input type="text" id="v_brand" class="form-control" placeholder="Toyota" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Model</label>
                        <input type="text" id="v_model" class="form-control" placeholder="Avanza" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Tahun</label>
                        <input type="number" id="v_year" class="form-control" placeholder="2020" min="1991" max="<?= date('Y') ?>" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Warna</label>
                        <input type="text" id="v_color" class="form-control" placeholder="Putih" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Mesin</label>
                        <input type="text" id="v_engine" class="form-control" placeholder="Opsional" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Rangka</label>
                        <input type="text" id="v_chassis" class="form-control" placeholder="Opsional" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="save_vehicle_btn" class="btn btn-primary" onclick="saveVehicle()">
                    <span class="indicator-label">Simpan</span>
                    <span class="indicator-progress d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Kendaraan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_vehicle_id" />
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label required">Merek</label>
                        <input type="text" id="edit_v_brand" class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Model</label>
                        <input type="text" id="edit_v_model" class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label required">Tahun</label>
                        <input type="number" id="edit_v_year" class="form-control" min="1991" max="<?= date('Y') ?>" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Warna</label>
                        <input type="text" id="edit_v_color" class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Mesin</label>
                        <input type="text" id="edit_v_engine" class="form-control" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Rangka</label>
                        <input type="text" id="edit_v_chassis" class="form-control" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="update_vehicle_btn" class="btn btn-primary" onclick="updateVehicle()">
                    <span class="indicator-label">Perbarui</span>
                    <span class="indicator-progress d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';

    function saveVehicle() {
        var btn = document.getElementById('save_vehicle_btn');
        toggleBtn(btn, true);
        $.ajax({
            url: BASE_URL + 'my/vehicles/store',
            method: 'POST',
            data: {
                plate_number: $('#v_plate').val(),
                brand: $('#v_brand').val(),
                model: $('#v_model').val(),
                year: $('#v_year').val(),
                color: $('#v_color').val(),
                engine_no: $('#v_engine').val(),
                chassis_no: $('#v_chassis').val(),
                [CSRF_TOKEN_NAME]: CSRF_HASH
            },
            headers: {
                'X-CSRF-Token': CSRF_HASH
            },
            success: function(res) {
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addVehicleModal')).hide();
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        })
                        .then(function() {
                            window.location.reload();
                        });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                    toggleBtn(btn, false);
                }
            }
        });
    }

    document.querySelectorAll('.edit-vehicle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('edit_vehicle_id').value = this.dataset.id;
            document.getElementById('edit_v_brand').value = this.dataset.brand;
            document.getElementById('edit_v_model').value = this.dataset.model;
            document.getElementById('edit_v_year').value = this.dataset.year;
            document.getElementById('edit_v_color').value = this.dataset.color;
            document.getElementById('edit_v_engine').value = this.dataset.engine;
            document.getElementById('edit_v_chassis').value = this.dataset.chassis;
            new bootstrap.Modal(document.getElementById('editVehicleModal')).show();
        });
    });

    function updateVehicle() {
        var btn = document.getElementById('update_vehicle_btn');
        var id = document.getElementById('edit_vehicle_id').value;
        toggleBtn(btn, true);
        $.ajax({
            url: BASE_URL + 'my/vehicles/edit/' + id,
            method: 'POST',
            data: {
                brand: $('#edit_v_brand').val(),
                model: $('#edit_v_model').val(),
                year: $('#edit_v_year').val(),
                color: $('#edit_v_color').val(),
                engine_no: $('#edit_v_engine').val(),
                chassis_no: $('#edit_v_chassis').val(),
                [CSRF_TOKEN_NAME]: CSRF_HASH
            },
            headers: {
                'X-CSRF-Token': CSRF_HASH
            },
            success: function(res) {
                if (res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editVehicleModal')).hide();
                    Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        })
                        .then(function() {
                            window.location.reload();
                        });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                    toggleBtn(btn, false);
                }
            }
        });
    }

    document.querySelectorAll('.delete-vehicle-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.dataset.id;
            Swal.fire({
                icon: 'warning',
                title: 'Hapus Kendaraan?',
                text: 'Kendaraan yang masih memiliki booking aktif tidak dapat dihapus.',
                showCancelButton: true,
                confirmButtonText: 'Hapus',
                confirmButtonColor: '#f1416c',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    url: BASE_URL + 'my/vehicles/delete/' + id,
                    method: 'POST',
                    data: {
                        [CSRF_TOKEN_NAME]: CSRF_HASH
                    },
                    headers: {
                        'X-CSRF-Token': CSRF_HASH
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                    icon: 'success',
                                    title: 'Dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                })
                                .then(function() {
                                    window.location.reload();
                                });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: res.message
                            });
                        }
                    }
                });
            });
        });
    });

    function toggleBtn(btn, loading) {
        btn.disabled = loading;
        btn.querySelector('.indicator-label').classList.toggle('d-none', loading);
        btn.querySelector('.indicator-progress').classList.toggle('d-none', !loading);
    }
</script>