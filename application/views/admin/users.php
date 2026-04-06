<?php
$role_labels = ['admin' => 'Admin', 'kasir' => 'Kasir', 'mekanik' => 'Mekanik', 'customer' => 'Customer'];
$role_colors = ['admin' => 'danger', 'kasir' => 'info', 'mekanik' => 'warning', 'customer' => 'primary'];

$visible_total = count($users);
$active_total = 0;
$inactive_total = 0;
$admin_total = 0;
$staff_total = 0;

foreach ($users as $user_item) {
    if (!empty($user_item->is_active)) {
        $active_total++;
    } else {
        $inactive_total++;
    }

    if (($user_item->role ?? '') === 'admin') {
        $admin_total++;
    }

    if (in_array(($user_item->role ?? ''), ['kasir', 'mekanik'], true)) {
        $staff_total++;
    }
}
?>

<section class="admin-page-hero">
    <div class="row g-4 align-items-end">
        <div class="col-xl-7">
            <div class="admin-page-hero__eyebrow">User management</div>
            <h2 class="admin-page-hero__title fw-bold">Kelola akses admin, kasir, mekanik, dan customer dengan tampilan yang lebih ringkas.</h2>
            <p class="admin-page-hero__desc">Halaman pengguna dirapikan agar status akun, role, dan aksi aktivasi lebih mudah dibaca. Cocok untuk audit cepat saat operasional bengkel berjalan.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                <span class="admin-chip"><i class="fa-solid fa-users"></i><?= $visible_total ?> akun tercatat</span>
                <span class="admin-chip"><i class="fa-solid fa-user-shield"></i><?= $admin_total ?> admin</span>
                <span class="admin-chip"><i class="fa-solid fa-user-gear"></i><?= $staff_total ?> staff operasional</span>
            </div>
        </div>
        <div class="col-xl-5">
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4"><i class="fa-solid fa-id-card fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Total pengguna</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $visible_total ?></div>
                        <div class="text-muted fs-7 mt-2">Semua akun yang tersedia di sistem.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(16, 185, 129, 0.16); color: #059669;"><i class="fa-solid fa-user-check fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Akun aktif</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $active_total ?></div>
                        <div class="text-muted fs-7 mt-2">Siap digunakan untuk login dan operasional.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(239, 68, 68, 0.14); color: #dc2626;"><i class="fa-solid fa-user-slash fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Nonaktif</div>
                        <div class="fs-2hx fw-bold mt-1"><?= $inactive_total ?></div>
                        <div class="text-muted fs-7 mt-2">Akun yang dinonaktifkan sementara.</div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="admin-kpi-card">
                        <div class="admin-kpi-card__icon mb-4" style="background: rgba(14, 165, 233, 0.16); color: #0284c7;"><i class="fa-solid fa-user-plus fs-3"></i></div>
                        <div class="text-muted fs-8 text-uppercase fw-semibold">Aksi cepat</div>
                        <button class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                            Tambah pengguna baru
                        </button>
                        <div class="text-muted fs-7 mt-2">Buat akun operator tanpa pindah halaman.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="admin-surface-card mb-6">
    <div class="card-body p-5 p-lg-7 d-flex flex-wrap align-items-center justify-content-between gap-4">
        <div>
            <div class="text-muted fs-8 text-uppercase fw-semibold mb-2">Kontrol pengguna</div>
            <h3 class="fw-bold mb-0">Tambah akun baru atau ubah status akun yang sudah ada</h3>
        </div>
        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#modalAddUser">
            <i class="fa-solid fa-user-plus me-2"></i>Tambah Pengguna
        </button>
    </div>
</div>

<div class="admin-surface-card overflow-hidden">
    <div class="card-header border-0 pt-6 pb-0 px-5 px-lg-7">
        <div>
            <h3 class="fw-bold mb-1">Daftar pengguna</h3>
            <div class="admin-table-meta">Pantau role, kontak, dan status akun dengan struktur tabel yang lebih mudah dipindai.</div>
        </div>
    </div>
    <div class="card-body p-0 pt-5">
        <div class="table-responsive">
            <table class="table table-row-dashed align-middle gs-0 gy-0 fs-7 mb-0 admin-data-table">
                <thead>
                    <tr>
                        <th class="ps-5">Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end pe-5">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-12">Belum ada pengguna.</td>
                        </tr>
                        <?php else: foreach ($users as $i => $u): ?>
                            <tr>
                                <td class="ps-5">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="symbol symbol-40px">
                                            <?php if (!empty($u->avatar)): ?>
                                                <img src="<?= base_url('uploads/avatars/' . $u->avatar) ?>" class="rounded-circle" alt="">
                                            <?php else: ?>
                                                <div class="symbol-label bg-light-primary">
                                                    <span class="text-primary fw-bold fs-7"><?= strtoupper(substr($u->name, 0, 1)) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-gray-900"><?= htmlspecialchars($u->name) ?></div>
                                            <div class="admin-table-meta mt-1">User #<?= $i + 1 ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-gray-900"><?= htmlspecialchars($u->email) ?></div>
                                </td>
                                <td>
                                    <span class="admin-table-meta fs-7"><?= htmlspecialchars($u->phone ?? '—') ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-light-<?= $role_colors[$u->role] ?? 'secondary' ?> px-4 py-2">
                                        <?= $role_labels[$u->role] ?? ucfirst($u->role) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-light-<?= $u->is_active ? 'success' : 'danger' ?> px-4 py-2">
                                        <?= $u->is_active ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-5">
                                    <button class="btn btn-sm btn-light btn-toggle" data-id="<?= $u->id_user ?>" data-active="<?= $u->is_active ?>">
                                        <i class="fa-solid fa-<?= $u->is_active ? 'ban' : 'check' ?> fs-8"></i>
                                        <?= $u->is_active ? 'Nonaktifkan' : 'Aktifkan' ?>
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

<!-- Modal: Add User -->
<div class="modal fade" id="modalAddUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label required">Nama Lengkap</label>
                    <input type="text" id="addName" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label required">Email</label>
                    <input type="email" id="addEmail" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">No. HP</label>
                    <input type="text" id="addPhone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label required">Role</label>
                    <select id="addRole" class="form-select">
                        <option value="admin">Admin</option>
                        <option value="kasir">Kasir</option>
                        <option value="mekanik">Mekanik</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label required">Password</label>
                    <input type="password" id="addPassword" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" id="btnAddUser">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btnAddUser').addEventListener('click', function() {
        $.ajax({
            url: BASE_URL + 'admin/users/store',
            method: 'POST',
            data: {
                name: document.getElementById('addName').value,
                email: document.getElementById('addEmail').value,
                phone: document.getElementById('addPhone').value,
                role: document.getElementById('addRole').value,
                password: document.getElementById('addPassword').value,
            },
            success: function(r) {
                if (r.success) location.reload();
                else Swal.fire({
                    icon: 'error',
                    text: r.message
                });
            }
        });
    });

    document.querySelectorAll('.btn-toggle').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const active = this.dataset.active == '1';
            const action = active ? 'Nonaktifkan' : 'Aktifkan';
            Swal.fire({
                icon: 'question',
                title: action + ' pengguna ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then(function(res) {
                if (!res.isConfirmed) return;
                $.ajax({
                    url: BASE_URL + 'admin/users/' + id + '/toggle',
                    method: 'POST',
                    success: function(r) {
                        if (r.success) location.reload();
                        else Swal.fire({
                            icon: 'error',
                            text: r.message
                        });
                    }
                });
            });
        });
    });
</script>