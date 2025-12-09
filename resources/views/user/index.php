<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-users-cog"></i> Manajemen User & Role</h4>
                <h6>Kelola Pengguna dan Hak Akses</h6>
            </div>
        </div>

        <!-- USER SECTION -->
        <div class="row">
        <!-- USER SECTION -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Daftar User</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalUser">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($user)): ?>
                                <tr><td colspan="5" class="text-center py-5">Belum ada data user</td></tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($user as $u): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo htmlspecialchars($u->username) ?></td>
                                    <td><?php echo htmlspecialchars($u->nama_role) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-user"
                                                data-id="<?php echo $u->id ?>"
                                                data-username="<?php echo $u->username ?>"
                                                data-id_role="<?php echo $u->id_role ?>"
                                                data-status="<?php echo $u->status ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger hapus-user" data-id="<?php echo $u->id ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach?>
                            <?php endif?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ROLE SECTION -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-shield"></i> Daftar Role</h5>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalRole">
                        <i class="fas fa-plus"></i> Tambah Role
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Role</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($role)): ?>
                                <tr><td colspan="3" class="text-center py-5">Belum ada data role</td></tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($role as $r): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo htmlspecialchars($r->nama_role) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-role"
                                                data-id="<?php echo $r->id ?>"
                                                data-nama_role="<?php echo $r->nama_role ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger hapus-role" data-id="<?php echo $r->id ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach?>
                            <?php endif?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>


<!-- Modal User -->
<div class="modal fade" id="modalUser">
    <div class="modal-dialog">
        <form id="formUser">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Form User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="userId">
                    <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
                    <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
                    <select name="id_role" class="form-control mb-3" required>
                        <option value="">-- Pilih Role --</option>
                        <?php foreach ($role as $r): ?>
                            <option value="<?php echo $r->id ?>"><?php echo $r->nama_role ?></option>
                        <?php endforeach?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Role -->
<div class="modal fade" id="modalRole">
    <div class="modal-dialog">
        <form id="formRole">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title">Form Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="roleId">
                    <input type="text" name="nama_role" class="form-control mb-3" placeholder="Nama Role" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
$(function() {

    function handleAjaxError(xhr) {
        let title = 'Gagal!';
        let msg = 'Terjadi kesalahan sistem.';

        if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }

        if (xhr.status === 403) {
            title = 'Akses Ditolak!';
        } else if (xhr.status === 401) {
            title = 'Sesi Habis';
            msg = 'Silakan login kembali.';
            setTimeout(() => window.location.reload(), 2000);
        }

        Swal.fire({
            icon: 'error',
            title: title,
            text: msg,
            confirmButtonColor: '#d33'
        });
    }
    $('.btn-tambah-user').click(function(){
        $('#formUser')[0].reset();
        $('#userId').val('');
        $('#modalUser .modal-title').text('Tambah User');
        $('#modalUser').modal('show');
    });

    $('.edit-user').click(function() {
        const d = $(this).data();
        $('#userId').val(d.id);
        $('[name="username"]').val(d.username);
        $('[name="id_role"]').val(d.id_role);
        $('[name="status"]').val(d.status);
        $('#modalUser .modal-title').text('Edit User');
        $('#modalUser').modal('show');
    });

    $('#formUser').submit(function(e) {
        e.preventDefault();
        const id = $('#userId').val();
        const url = id ? '<?php echo url("user") ?>/' + id : '<?php echo url("user") ?>';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url, method,
            data: $(this).serialize(),
            dataType: 'json'
        })
        .done(res => Swal.fire('Success', res.message, 'success').then(() => location.reload()))
        .fail((xhr) => handleAjaxError(xhr)); // Panggil Helper
    });

    $('.hapus-user').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus user?',
            text: 'Data user akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            confirmButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("user") ?>/' + id,
                    method: 'DELETE'
                })
                .done(() => Swal.fire('Deleted!', 'User berhasil dihapus', 'success').then(() => location.reload()))
                .fail((xhr) => handleAjaxError(xhr)); // Panggil Helper
            }
        });
    });

    $('#modalUser').on('hidden.bs.modal', function() {
        $('#formUser')[0].reset();
        $('#userId').val('');
        $('.modal-title', this).text('Tambah User');
    });


    $('.btn-tambah-role').click(function(){ // Pastikan tombol tambah role punya class ini
        $('#formRole')[0].reset();
        $('#roleId').val('');
        $('#modalRole .modal-title').text('Tambah Role');
        $('#modalRole').modal('show');
    });

    $('.edit-role').click(function() {
        const d = $(this).data();
        $('#roleId').val(d.id);
        $('[name="nama_role"]').val(d.nama_role);
        $('#modalRole .modal-title').text('Edit Role');
        $('#modalRole').modal('show');
    });

    $('#formRole').submit(function(e) {
        e.preventDefault();
        const id = $('#roleId').val();
        const url = id ? '<?php echo url("role") ?>/' + id : '<?php echo url("role") ?>';
        const method = id ? 'PUT' : 'POST';

        $.ajax({
            url, method,
            data: $(this).serialize(),
            dataType: 'json'
        })
        .done(res => Swal.fire('Success', res.message, 'success').then(() => location.reload()))
        .fail((xhr) => handleAjaxError(xhr)); // Panggil Helper
    });

    $('.hapus-role').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus role?',
            text: 'Data role akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            confirmButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("role") ?>/' + id,
                    method: 'DELETE'
                })
                .done(() => Swal.fire('Deleted!', 'Role berhasil dihapus', 'success').then(() => location.reload()))
                .fail((xhr) => handleAjaxError(xhr)); // Panggil Helper
            }
        });
    });

    $('#modalRole').on('hidden.bs.modal', function() {
        $('#formRole')[0].reset();
        $('#roleId').val('');
        $('.modal-title', this).text('Tambah Role');
    });
});
</script>
