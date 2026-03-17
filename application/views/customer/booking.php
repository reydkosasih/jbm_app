<!-- Booking Wizard -->
<div class="card card-flush">
    <div class="card-header pt-5">
        <div class="card-title flex-column">
            <h2 class="fw-bold mb-1">Booking Servis Baru</h2>
            <div class="text-muted fw-semibold fs-6">Ikuti langkah-langkah untuk menjadwalkan servis kendaraan Anda.</div>
        </div>
    </div>
    <div class="card-body py-8">

        <!-- Stepper indicators -->
        <div class="stepper stepper-links d-flex flex-column" id="booking_stepper">
            <div class="stepper-nav mb-10 justify-content-center">
                <div class="stepper-item current" data-kt-stepper-element="nav" data-step="1">
                    <h3 class="stepper-title">1. Kendaraan</h3>
                </div>
                <div class="stepper-item" data-kt-stepper-element="nav" data-step="2">
                    <h3 class="stepper-title">2. Layanan</h3>
                </div>
                <div class="stepper-item" data-kt-stepper-element="nav" data-step="3">
                    <h3 class="stepper-title">3. Jadwal</h3>
                </div>
                <div class="stepper-item" data-kt-stepper-element="nav" data-step="4">
                    <h3 class="stepper-title">4. Konfirmasi</h3>
                </div>
            </div>

            <!-- Step 1: Vehicle -->
            <div class="current" data-kt-stepper-element="content" id="step1">
                <div class="row g-5">
                    <?php if (!empty($vehicles)): ?>
                        <?php foreach ($vehicles as $v): ?>
                            <div class="col-md-6">
                                <label class="cursor-pointer">
                                    <input type="radio" name="vehicle_id" value="<?= $v->id ?>" class="d-none vehicle-radio"
                                        data-brand="<?= htmlspecialchars($v->brand) ?>"
                                        data-model="<?= htmlspecialchars($v->model) ?>"
                                        data-year="<?= $v->year ?>"
                                        data-plate="<?= htmlspecialchars($v->plate_number) ?>"
                                        data-color="<?= htmlspecialchars($v->color) ?>" />
                                    <div class="card card-bordered vehicle-card h-100">
                                        <div class="card-body d-flex align-items-center gap-4">
                                            <div class="d-flex align-items-center justify-content-center bg-light-primary rounded" style="width:60px;height:60px;flex-shrink:0;">
                                                <i class="fa-solid fa-car text-primary fs-2"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-5"><?= htmlspecialchars($v->brand . ' ' . $v->model) ?></div>
                                                <div class="text-muted fs-7"><?= $v->year ?> · <?= htmlspecialchars($v->color) ?></div>
                                                <span class="badge badge-light-primary mt-1"><?= htmlspecialchars($v->plate_number) ?></span>
                                            </div>
                                            <div class="ms-auto">
                                                <i class="fa-solid fa-circle-check fs-2 text-primary check-icon d-none"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-warning d-flex align-items-center p-5">
                                <i class="fa-solid fa-triangle-exclamation fs-2 text-warning me-4"></i>
                                <div>
                                    Anda belum memiliki kendaraan terdaftar.
                                    <a href="<?= base_url('my/vehicles') ?>" class="link-warning fw-bold">Tambah kendaraan sekarang</a>.
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12 mt-4">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#addVehicleModal"
                            class="btn btn-light-primary btn-sm">
                            <i class="fa-solid fa-plus me-2"></i>Tambah Kendaraan Baru
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 2: Service -->
            <div data-kt-stepper-element="content" id="step2">
                <div class="row g-5">
                    <?php foreach ($services as $s): ?>
                        <div class="col-md-6">
                            <label class="cursor-pointer">
                                <input type="radio" name="service_id" value="<?= $s->id ?>" class="d-none service-radio"
                                    data-name="<?= htmlspecialchars($s->name) ?>"
                                    data-price="<?= $s->price ?>"
                                    data-duration="<?= $s->duration ?>" />
                                <div class="card card-bordered service-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="d-flex align-items-center justify-content-center bg-light-info rounded me-4" style="width:50px;height:50px;flex-shrink:0;">
                                                <i class="fa-solid fa-wrench text-info fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6"><?= htmlspecialchars($s->name) ?></div>
                                                <div class="text-muted fs-8"><?= $s->duration ?> menit</div>
                                            </div>
                                            <i class="fa-solid fa-circle-check fs-2 text-info check-icon d-none ms-auto"></i>
                                        </div>
                                        <p class="text-gray-600 fs-7 mb-3"><?= htmlspecialchars($s->description ?? '') ?></p>
                                        <div class="fs-5 fw-bold text-info">
                                            Rp <?= number_format($s->price, 0, ',', '.') ?>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Step 3: Date & Slot -->
            <div data-kt-stepper-element="content" id="step3">
                <div class="row g-5 justify-content-center">
                    <div class="col-md-6">
                        <div class="mb-6">
                            <label class="form-label fw-semibold required">Pilih Tanggal</label>
                            <input type="date" id="booking_date" class="form-control form-control-lg"
                                min="<?= date('Y-m-d') ?>"
                                max="<?= date('Y-m-d', strtotime('+30 days')) ?>" />
                            <div class="form-text text-muted">Bengkel tutup setiap hari Minggu.</div>
                        </div>

                        <div id="slot_section" class="d-none">
                            <label class="form-label fw-semibold required">Pilih Waktu</label>
                            <div id="slot_list" class="row g-3"></div>
                            <div id="slot_loading" class="text-center py-8 d-none">
                                <div class="spinner-border text-primary"></div>
                                <p class="text-muted mt-3">Memuat slot waktu...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Confirmation -->
            <div data-kt-stepper-element="content" id="step4">
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <div class="card bg-light-primary mb-6">
                            <div class="card-body">
                                <h4 class="fw-bold text-primary mb-4">Konfirmasi Booking</h4>
                                <table class="table table-borderless fs-6">
                                    <tr>
                                        <td class="text-muted fw-semibold py-2" style="width:40%">Kendaraan</td>
                                        <td class="fw-bold" id="confirm_vehicle">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold py-2">Layanan</td>
                                        <td class="fw-bold" id="confirm_service">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold py-2">Estimasi Harga</td>
                                        <td class="fw-bold text-primary" id="confirm_price">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold py-2">Tanggal</td>
                                        <td class="fw-bold" id="confirm_date">-</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold py-2">Jam</td>
                                        <td class="fw-bold" id="confirm_slot">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="form-label fw-semibold">Catatan Tambahan (opsional)</label>
                            <textarea id="booking_notes" name="notes" class="form-control" rows="3"
                                placeholder="Jelaskan keluhan atau hal khusus yang ingin disampaikan ke mekanik..."></textarea>
                        </div>

                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-5">
                            <i class="fa-solid fa-circle-info fs-2tx text-warning me-4"></i>
                            <div>
                                <span class="fw-bold text-gray-700">Catatan:</span>
                                <span class="text-gray-600 fs-7">
                                    Harga merupakan estimasi. Biaya akhir dapat berbeda tergantung hasil pemeriksaan mekanik.
                                    Pembatalan gratis sampai H-1 sebelum jadwal servis.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation buttons -->
            <div class="d-flex justify-content-between mt-10">
                <button type="button" id="prev_btn" class="btn btn-light d-none" onclick="changeStep(-1)">
                    <i class="fa-solid fa-arrow-left me-2"></i>Sebelumnya
                </button>
                <div class="ms-auto">
                    <button type="button" id="next_btn" class="btn btn-primary" onclick="changeStep(1)">
                        Selanjutnya<i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                    <button type="button" id="submit_btn" class="btn btn-success d-none" onclick="submitBooking()">
                        <span class="indicator-label"><i class="fa-solid fa-check me-2"></i>Konfirmasi Booking</span>
                        <span class="indicator-progress d-none">
                            <span class="spinner-border spinner-border-sm me-2"></span>Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

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

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';

    var currentStep = 1;
    var totalSteps = 4;
    var selectedSlot = null;
    var selectedSlotLabel = '';

    // ─── Stepper ────────────────────────────────────────────────────────────────

    function changeStep(dir) {
        if (dir === 1 && !validateStep(currentStep)) return;

        var nextStep = currentStep + dir;
        if (nextStep < 1 || nextStep > totalSteps) return;

        // Hide current step content
        document.getElementById('step' + currentStep).classList.remove('current');

        // Update stepper nav
        document.querySelectorAll('[data-kt-stepper-element="nav"]').forEach(function(el) {
            el.classList.remove('current', 'completed');
        });

        currentStep = nextStep;

        document.getElementById('step' + currentStep).classList.add('current');

        for (var i = 1; i < currentStep; i++) {
            document.querySelector('[data-step="' + i + '"]').classList.add('completed');
        }
        document.querySelector('[data-step="' + currentStep + '"]').classList.add('current');

        document.getElementById('prev_btn').classList.toggle('d-none', currentStep === 1);
        document.getElementById('next_btn').classList.toggle('d-none', currentStep === totalSteps);
        document.getElementById('submit_btn').classList.toggle('d-none', currentStep !== totalSteps);

        if (currentStep === 4) buildConfirmation();
    }

    function validateStep(step) {
        if (step === 1) {
            if (!document.querySelector('.vehicle-radio:checked')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kendaraan',
                    text: 'Silakan pilih salah satu kendaraan.'
                });
                return false;
            }
        }
        if (step === 2) {
            if (!document.querySelector('.service-radio:checked')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Layanan',
                    text: 'Silakan pilih layanan servis.'
                });
                return false;
            }
        }
        if (step === 3) {
            if (!document.getElementById('booking_date').value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Tanggal',
                    text: 'Silakan pilih tanggal booking.'
                });
                return false;
            }
            if (!selectedSlot) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Waktu',
                    text: 'Silakan pilih slot waktu.'
                });
                return false;
            }
        }
        return true;
    }

    // ─── Vehicle card selection ──────────────────────────────────────────────────

    document.querySelectorAll('.vehicle-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.vehicle-card').forEach(function(card) {
                card.classList.remove('border-primary', 'bg-light-primary');
                card.querySelector('.check-icon').classList.add('d-none');
            });
            var card = this.closest('label').querySelector('.vehicle-card');
            card.classList.add('border-primary', 'bg-light-primary');
            card.querySelector('.check-icon').classList.remove('d-none');
        });
    });

    document.querySelectorAll('.service-radio').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.service-card').forEach(function(card) {
                card.classList.remove('border-info', 'bg-light-info');
                card.querySelector('.check-icon').classList.add('d-none');
            });
            var card = this.closest('label').querySelector('.service-card');
            card.classList.add('border-info', 'bg-light-info');
            card.querySelector('.check-icon').classList.remove('d-none');
        });
    });

    // ─── Date / Slot picker ──────────────────────────────────────────────────────

    document.getElementById('booking_date').addEventListener('change', function() {
        var date = this.value;
        if (!date) return;

        // Disable Sundays
        var day = new Date(date).getDay();
        if (day === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Bengkel Tutup',
                text: 'Kami tutup setiap hari Minggu.'
            });
            this.value = '';
            return;
        }

        selectedSlot = null;
        var section = document.getElementById('slot_section');
        var list = document.getElementById('slot_list');
        var loading = document.getElementById('slot_loading');

        section.classList.remove('d-none');
        list.innerHTML = '';
        loading.classList.remove('d-none');

        $.get(BASE_URL + 'api/slots', {
            date: date
        }, function(res) {
            loading.classList.add('d-none');
            if (!res.success || !res.slots.length) {
                list.innerHTML = '<div class="col-12"><div class="alert alert-warning">Tidak ada slot tersedia untuk tanggal ini.</div></div>';
                return;
            }
            res.slots.forEach(function(slot) {
                var full = slot.is_full == 1 || slot.is_full === true;
                list.innerHTML += '<div class="col-6 col-md-4">' +
                    '<button type="button" class="btn w-100 btn-slot ' + (full ? 'btn-light-danger disabled' : 'btn-light-primary') + '"' +
                    ' data-slot-id="' + slot.id + '" data-slot-label="' + slot.label + '" ' + (full ? 'disabled' : '') + '>' +
                    '<span class="fw-bold">' + slot.start_time.substring(0, 5) + ' – ' + slot.end_time.substring(0, 5) + '</span>' +
                    '<br><small class="' + (full ? 'text-danger' : 'text-muted') + '">' + (full ? 'Penuh' : slot.label) + '</small>' +
                    '</button></div>';
            });

            // Slot click handler
            document.querySelectorAll('.btn-slot:not([disabled])').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.btn-slot').forEach(function(b) {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-light-primary');
                    });
                    this.classList.remove('btn-light-primary');
                    this.classList.add('btn-primary');
                    selectedSlot = this.dataset.slotId;
                    selectedSlotLabel = this.querySelector('span').textContent.trim();
                });
            });
        });
    });

    // ─── Confirmation ────────────────────────────────────────────────────────────

    function buildConfirmation() {
        var vr = document.querySelector('.vehicle-radio:checked');
        var sr = document.querySelector('.service-radio:checked');
        var dt = document.getElementById('booking_date').value;

        if (vr) document.getElementById('confirm_vehicle').textContent = vr.dataset.plate + ' — ' + vr.dataset.brand + ' ' + vr.dataset.model + ' ' + vr.dataset.year;
        if (sr) {
            document.getElementById('confirm_service').textContent = sr.dataset.name + ' (' + sr.dataset.duration + ' menit)';
            document.getElementById('confirm_price').textContent = 'Rp ' + Number(sr.dataset.price).toLocaleString('id-ID');
        }
        if (dt) {
            var opts = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('confirm_date').textContent = new Date(dt + 'T00:00:00').toLocaleDateString('id-ID', opts);
        }
        document.getElementById('confirm_slot').textContent = selectedSlotLabel || '-';
    }

    // ─── Submit ──────────────────────────────────────────────────────────────────

    function submitBooking() {
        var btn = document.getElementById('submit_btn');
        var label = btn.querySelector('.indicator-label');
        var prog = btn.querySelector('.indicator-progress');
        btn.disabled = true;
        label.classList.add('d-none');
        prog.classList.remove('d-none');

        var vr = document.querySelector('.vehicle-radio:checked');
        var sr = document.querySelector('.service-radio:checked');

        $.ajax({
            url: BASE_URL + 'my/booking/store',
            method: 'POST',
            data: {
                vehicle_id: vr ? vr.value : '',
                service_id: sr ? sr.value : '',
                slot_id: selectedSlot,
                booking_date: document.getElementById('booking_date').value,
                notes: document.getElementById('booking_notes').value,
                [CSRF_TOKEN_NAME]: CSRF_HASH
            },
            headers: {
                'X-CSRF-Token': CSRF_HASH
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Booking Berhasil!',
                        html: 'Kode Booking: <strong>' + res.booking_code + '</strong><br>Tunggu konfirmasi dari kami.',
                        confirmButtonText: 'Lihat Detail'
                    }).then(function() {
                        window.location.href = res.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                    btn.disabled = false;
                    label.classList.remove('d-none');
                    prog.classList.add('d-none');
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg
                });
                btn.disabled = false;
                label.classList.remove('d-none');
                prog.classList.add('d-none');
            }
        });
    }

    // ─── Inline add vehicle ───────────────────────────────────────────────────────

    function saveVehicle() {
        var btn = document.getElementById('save_vehicle_btn');
        var label = btn.querySelector('.indicator-label');
        var prog = btn.querySelector('.indicator-progress');
        btn.disabled = true;
        label.classList.add('d-none');
        prog.classList.remove('d-none');

        $.ajax({
            url: BASE_URL + 'my/vehicles/store',
            method: 'POST',
            data: {
                plate_number: $('#v_plate').val(),
                brand: $('#v_brand').val(),
                model: $('#v_model').val(),
                year: $('#v_year').val(),
                color: $('#v_color').val(),
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
                    btn.disabled = false;
                    label.classList.remove('d-none');
                    prog.classList.add('d-none');
                }
            }
        });
    }
</script>