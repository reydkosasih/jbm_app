<div class="row g-5 g-xl-8">
    <div class="col-xl-6">
        <!-- Profile info -->
        <div class="card card-flush mb-5">
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold">Informasi Profil</h3>
            </div>
            <div class="card-body">
                <!-- Avatar -->
                <div class="d-flex align-items-center gap-5 mb-8">
                    <div class="symbol symbol-100px symbol-circle">
                        <?php if (!empty($user->avatar)): ?>
                            <img src="<?= base_url('uploads/avatars/' . $user->avatar) ?>" alt="Avatar" id="avatar_preview" />
                        <?php else: ?>
                            <div class="symbol-label fs-2 fw-bold bg-light-primary text-primary" id="avatar_placeholder">
                                <?= strtoupper(substr($user->name, 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1"><?= htmlspecialchars($user->name) ?></h4>
                        <p class="text-muted fs-7 mb-3"><?= htmlspecialchars($user->email) ?></p>
                        <label for="avatar_input" class="btn btn-sm btn-light-primary cursor-pointer">
                            <i class="fa-solid fa-camera me-2"></i>Ganti Foto
                        </label>
                        <input type="file" id="avatar_input" class="d-none" accept="image/*" />
                    </div>
                </div>

                <form id="profile_form" enctype="multipart/form-data">
                    <div class="mb-5">
                        <label class="form-label required fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user->name) ?>" required />
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control bg-light" value="<?= htmlspecialchars($user->email) ?>" disabled />
                        <div class="form-text text-muted">Email tidak dapat diubah.</div>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user->phone ?? '') ?>" />
                    </div>
                    <div class="mb-7">
                        <label class="form-label fw-semibold">Alamat</label>
                        <textarea name="address" class="form-control" rows="3"><?= htmlspecialchars($user->address ?? '') ?></textarea>
                    </div>
                    <button type="submit" id="profile_save_btn" class="btn btn-primary">
                        <span class="indicator-label"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan</span>
                        <span class="indicator-progress d-none"><span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <!-- Change password -->
        <div class="card card-flush">
            <div class="card-header pt-5">
                <h3 class="card-title fw-bold">Ubah Password</h3>
            </div>
            <div class="card-body">
                <form id="password_form">
                    <div class="mb-5">
                        <label class="form-label fw-semibold required">Password Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" class="form-control" />
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-semibold required">Password Baru</label>
                        <input type="password" id="new_password" name="new_password" class="form-control" />
                    </div>
                    <!-- Strength bar -->
                    <div class="mb-5">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="fs-8 text-muted">Kekuatan Password</span>
                            <span class="fs-8" id="pw_strength_label">-</span>
                        </div>
                        <div class="progress" style="height:4px;">
                            <div class="progress-bar" id="pw_strength_bar" style="width:0%;transition:width 0.3s;"></div>
                        </div>
                    </div>
                    <div class="mb-7">
                        <label class="form-label fw-semibold required">Konfirmasi Password Baru</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" />
                    </div>
                    <button type="submit" id="password_save_btn" class="btn btn-warning">
                        <span class="indicator-label"><i class="fa-solid fa-key me-2"></i>Ubah Password</span>
                        <span class="indicator-progress d-none"><span class="spinner-border spinner-border-sm me-2"></span>Memproses...</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var BASE_URL = '<?= base_url() ?>';
    var CSRF_TOKEN_NAME = '<?= $this->security->get_csrf_token_name() ?>';
    var CSRF_HASH = '<?= $this->security->get_csrf_hash() ?>';

    // Avatar preview
    document.getElementById('avatar_input').addEventListener('change', function() {
        var file = this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var prev = document.getElementById('avatar_preview');
            var plac = document.getElementById('avatar_placeholder');
            if (prev) {
                prev.src = e.target.result;
            } else if (plac) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.id = 'avatar_preview';
                img.className = 'w-100 h-100 rounded-circle object-fit-cover';
                plac.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);
    });

    // Profile form
    document.getElementById('profile_form').addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = document.getElementById('profile_save_btn');
        toggleBtn(btn, true);

        var fd = new FormData(this);
        var avatarFile = document.getElementById('avatar_input').files[0];
        if (avatarFile) fd.append('avatar', avatarFile);
        fd.append(CSRF_TOKEN_NAME, CSRF_HASH);

        $.ajax({
            url: BASE_URL + 'my/profile/update',
            method: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-Token': CSRF_HASH
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                }
                toggleBtn(btn, false);
            }
        });
    });

    // Password strength
    document.getElementById('new_password').addEventListener('input', function() {
        var val = this.value,
            score = 0;
        if (val.length >= 8) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        var bar = document.getElementById('pw_strength_bar');
        var lbl = document.getElementById('pw_strength_label');
        var cls = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
        var lbs = ['Lemah', 'Cukup', 'Baik', 'Kuat'];
        bar.className = 'progress-bar ' + (cls[score - 1] || 'bg-danger');
        bar.style.width = score > 0 ? (score * 25) + '%' : '0%';
        lbl.textContent = score > 0 ? lbs[score - 1] : '-';
    });

    // Password form
    document.getElementById('password_form').addEventListener('submit', function(e) {
        e.preventDefault();
        var np = document.getElementById('new_password').value;
        var cp = document.getElementById('confirm_password').value;
        if (np !== cp) {
            Swal.fire({
                icon: 'warning',
                title: 'Password Tidak Cocok'
            });
            return;
        }
        var btn = document.getElementById('password_save_btn');
        toggleBtn(btn, true);

        $.ajax({
            url: BASE_URL + 'my/profile/change-password',
            method: 'POST',
            data: {
                current_password: $('#current_password').val(),
                new_password: np,
                confirm_password: cp,
                [CSRF_TOKEN_NAME]: CSRF_HASH
            },
            headers: {
                'X-CSRF-Token': CSRF_HASH
            },
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message
                    });
                    document.getElementById('password_form').reset();
                    document.getElementById('pw_strength_bar').style.width = '0%';
                    document.getElementById('pw_strength_label').textContent = '-';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message
                    });
                }
                toggleBtn(btn, false);
            }
        });
    });

    function toggleBtn(btn, loading) {
        btn.disabled = loading;
        btn.querySelector('.indicator-label').classList.toggle('d-none', loading);
        btn.querySelector('.indicator-progress').classList.toggle('d-none', !loading);
    }
</script>