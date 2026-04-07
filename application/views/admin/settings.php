<?php
// Helper to get setting value or default
function sv($settings, $key, $default = '')
{
    return htmlspecialchars($settings[$key] ?? $default);
}
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">Workshop settings</div>
            <h2 class="admin-page-hero__title fw-bold">Atur identitas bengkel, rekening pembayaran, dan aturan operasional dari satu workspace.</h2>
            <p class="admin-page-hero__desc">Area settings dirapikan agar setiap kelompok pengaturan lebih mudah dipahami, terutama saat admin perlu mengecek data profil bengkel dari perangkat mobile.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-shop"></i><?= sv($settings, 'workshop_name', 'JBM Bengkel Mobil') ?></span>
                <span class="admin-chip"><i class="fa-solid fa-credit-card"></i>Pengaturan pembayaran tersedia</span>
                <span class="admin-chip"><i class="fa-solid fa-gears"></i>Operasional dapat diubah per tab</span>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-building fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Profil bengkel</div>
                        <div class="fs-5 fw-bold mt-1">Brand dan kontak</div>
                        <div class="text-muted fs-7 mt-2">Nama, alamat, WhatsApp, dan lokasi map.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-landmark fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Pembayaran</div>
                        <div class="fs-5 fw-bold mt-1">Info rekening</div>
                        <div class="text-muted fs-7 mt-2">Dipakai pada invoice dan instruksi transfer.</div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(14, 165, 233, 0.16); color: #0284c7;"><i class="fa-solid fa-sliders fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Operasional</div>
                        <div class="fs-5 fw-bold mt-1">Jam kerja, kapasitas booking, dan reminder</div>
                        <div class="text-muted fs-7 mt-2">Semua pengaturan inti operasional diringkas per tab agar mudah dikelola.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="admin-surface-card mb-6">
    <div class="card-body p-5 p-lg-7">
        <div class="d-flex flex-wrap align-items-end justify-content-between gap-4 mb-4">
            <div>
                <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Navigasi pengaturan</div>
                <h3 class="fw-bold mb-0">Pilih kelompok konfigurasi yang ingin diperbarui</h3>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="admin-chip"><i class="fa-solid fa-floppy-disk"></i>Simpan per section</span>
                <span class="admin-chip"><i class="fa-solid fa-mobile-screen-button"></i>Lebih nyaman di mobile</span>
            </div>
        </div>
        <ul class="nav nav-tabs nav-line-tabs fs-6">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tabInfo">Informasi Bengkel</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabPayment">Pembayaran</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tabOps">Operasional</a></li>
        </ul>
    </div>
</div>

<div class="tab-content">

    <!-- TAB: INFO -->
    <div class="tab-pane fade show active" id="tabInfo">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Informasi Bengkel</h3>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label required">Nama Bengkel</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_name" value="<?= sv($settings, 'workshop_name', 'JBM Bengkel Mobil') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tagline</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_tagline" value="<?= sv($settings, 'workshop_tagline') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. Telepon</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_phone" value="<?= sv($settings, 'workshop_phone') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_whatsapp" value="<?= sv($settings, 'workshop_whatsapp') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea class="form-control setting-input" data-key="workshop_address" rows="2"><?= sv($settings, 'workshop_address') ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kota</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_city" value="<?= sv($settings, 'workshop_city') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control setting-input" data-key="workshop_email" value="<?= sv($settings, 'workshop_email') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude (Google Maps)</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_lat" value="<?= sv($settings, 'workshop_lat') ?>" placeholder="-6.123456">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude (Google Maps)</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_lng" value="<?= sv($settings, 'workshop_lng') ?>" placeholder="106.789012">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary btn-save-section" data-section="info">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Informasi Bengkel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: PAYMENT -->
    <div class="tab-pane fade" id="tabPayment">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Informasi Pembayaran</h3>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" class="form-control setting-input" data-key="bank_name" value="<?= sv($settings, 'bank_name') ?>" placeholder="BCA, Mandiri, dll">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">No. Rekening</label>
                        <input type="text" class="form-control setting-input" data-key="bank_account" value="<?= sv($settings, 'bank_account') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" class="form-control setting-input" data-key="bank_holder" value="<?= sv($settings, 'bank_holder') ?>">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary btn-save-section" data-section="payment">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Info Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: OPERATIONAL -->
    <div class="tab-pane fade" id="tabOps">
        <div class="card card-flush">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Operasional</h3>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Jam Buka (Senin–Jumat)</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_hours_weekday" value="<?= sv($settings, 'workshop_hours_weekday', '08:00 - 17:00') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Jam Buka (Sabtu)</label>
                        <input type="text" class="form-control setting-input" data-key="workshop_hours_saturday" value="<?= sv($settings, 'workshop_hours_saturday', '08:00 - 15:00') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Maks Booking/Hari</label>
                        <input type="number" class="form-control setting-input" data-key="max_booking_per_day" value="<?= sv($settings, 'max_booking_per_day', '20') ?>" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Reminder Servis (hari sebelum)</label>
                        <input type="number" class="form-control setting-input" data-key="reminder_days_before_service" value="<?= sv($settings, 'reminder_days_before_service', '7') ?>" min="1">
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary btn-save-section" data-section="ops">
                            <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Pengaturan Operasional
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.querySelectorAll('.btn-save-section').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const section = this.dataset.section;
            const inputs = this.closest('.card-body').querySelectorAll('.setting-input');
            const payload = {};
            inputs.forEach(function(inp) {
                payload[inp.dataset.key] = inp.value;
            });

            Swal.fire({
                icon: 'question',
                title: 'Simpan pengaturan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, simpan'
            }).then(function(res) {
                if (!res.isConfirmed) return;
                $.ajax({
                    url: BASE_URL + 'admin/settings/save',
                    method: 'POST',
                    data: {
                        settings: JSON.stringify(payload)
                    },
                    success: function(r) {
                        if (r.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tersimpan!',
                                timer: 1200,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: r.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menghubungi server.'
                        });
                    }
                });
            });
        });
    });
</script>