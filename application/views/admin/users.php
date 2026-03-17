<?php
$csrf_name = $this->security->get_csrf_token_name();
$csrf_hash = $this->security->get_csrf_hash();
$role_labels = ['admin' => 'Admin', 'kasir' => 'Kasir', 'mekanik' => 'Mekanik', 'customer' => 'Customer'];
$role_colors = ['admin' => 'danger', 'kasir' => 'info', 'mekanik' => 'warning', 'customer' => 'primary'];
?>
<div class="d-flex justify-content-between align-items-center mb-5">
    <h1 class="fs-2hx fw-bold">Manajemen Pengguna</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddUser">
        <i class="fa-solid fa-user-plus me-2"></i>Tambah Pengguna
    </button>
</div>

<div class="card card-flush">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4 fs-7">
                <thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-4 rounded-start min-w-50px">#</th>
                        <th class="min-w-200px">Nama</th>
                        <th class="min-w-180px">Email</th>
                        <th class="min-w-120px">No. HP</th>
                        <th class="min-w-100px">Role</th>
                        <th class="min-w-80px text-center">Status</th>
                        <th class="min-w-80px text-end rounded-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-10">Belum ada pengguna.</td>
                        </tr>
                        <?php else: foreach ($users as $i => $u): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $i + 1 ?></td>
                                <td>
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
                                        <span class="fw-semibold"><?= htmlspecialchars($u->name) ?></span>
                                    </div>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($u->email) ?></td>
                                <td><?= htmlspecialchars($u->phone ?? '—') ?></td>
                                <td>
                                    <span class="badge badge-light-<?= $role_colors[$u->role] ?? 'secondary' ?>">
                                        <?= $role_labels[$u->role] ?? ucfirst($u->role) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-<?= $u->is_active ? 'success' : 'danger' ?>">
                                        <?= $u->is_active ? 'Aktif' : 'Nonaktif' ?>
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light btn-toggle" data-id="<?= $u->id ?>" data-active="<?= $u->is_active ?>">
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
    const CSRF_NAME = '<?= $csrf_name ?>';
    let CSRF_HASH = '<?= $csrf_hash ?>';

    function csrfData(extra) {
        const d = {};
        d[CSRF_NAME] = CSRF_HASH;
        return Object.assign(d, extra || {});
    }

    function hdrs() {
        return {
            'X-CSRF-Token': CSRF_HASH
        };
    }

    document.getElementById('btnAddUser').addEventListener('click', function() {
        $.ajax({
            url: BASE_URL + 'admin/users/store',
            method: 'POST',
            headers: hdrs(),
            data: csrfData({
                name: document.getElementById('addName').value,
                email: document.getElementById('addEmail').value,
                phone: document.getElementById('addPhone').value,
                role: document.getElementById('addRole').value,
                password: document.getElementById('addPassword').value,
            }),
            success: r => {
                CSRF_HASH = r.csrf_hash || CSRF_HASH;
                if (r.success) location.reload();
                else Swal.fire({
                    icon: 'error',
                    text: r.message
                });
            }
        });
    });

    document.querySelectorAll('.btn-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const active = this.dataset.active == '1';
            const action = active ? 'Nonaktifkan' : 'Aktifkan';
            Swal.fire({
                icon: 'question',
                title: action + ' pengguna ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya'
            }).then(res => {
                if (!res.isConfirmed) return;
                $.ajax({
                    url: BASE_URL + 'admin/users/' + id + '/toggle',
                    method: 'POST',
                    headers: hdrs(),
                    data: csrfData(),
                    success: r => {
                        CSRF_HASH = r.csrf_hash || CSRF_HASH;
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