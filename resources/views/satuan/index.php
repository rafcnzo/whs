<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-boxes"></i> Daftar Satuan</h4>
                <h6>Kelola semua satuan barang</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalSatuan">
                    <i class="fas fa-plus"></i> Tambah Satuan
                </button>
            </div>
        </div>

        <!-- Tabel Satuan -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelSatuan" class="table table-hover table-striped" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Satuan</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($satuan)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                        <i class="fa-solid fa-bitcoin-sign fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data satuan</h5>
                                            <p class="text-secondary">Klik tombol <strong>Tambah Satuan</strong> untuk mulai menambahkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($satuan as $s): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo htmlspecialchars($s->nama_satuan) ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $s->status ? 'success' : 'secondary' ?>">
                                            <?php echo $s->status ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-btn"
                                                data-id="<?php echo $s->id ?>"
                                                data-nama_satuan="<?php echo htmlspecialchars($s->nama_satuan) ?>"
                                                data-status="<?php echo $s->status ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger hapus-btn" data-id="<?php echo $s->id ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalSatuan" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formSatuan">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Form Satuan</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="satuanId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nama Satuan</label>
                            <input type="text" name="nama_satuan" class="form-control" placeholder="Contoh: Kg, Pcs, Ltr, dll" required>
                    </div>

                    <div class="mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Satuan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JS KHUSUS HALAMAN INI -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {

    function handleAjaxError(xhr) {
        let title = 'Gagal!';
        let msg = 'Terjadi kesalahan saat memproses data.';

        if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }

        if (xhr.status === 403) {
            title = 'Akses Ditolak!';
        }
        else if (xhr.status === 401) {
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

    $('.page-btn .btn-primary').on('click', function(){
        $('#formSatuan')[0].reset();
        $('#satuanId').val('');
        $('.modal-title').text('Tambah Satuan');
    });

    $(document).on('click', '.edit-btn', function() {
        const d = $(this).data();
        $('#satuanId').val(d.id);
        $('[name="nama_satuan"]').val(d.nama_satuan);
        $('[name="status"]').val(d.status);
        $('.modal-title').text('Edit Satuan');
        $('#modalSatuan').modal('show');
    });

    $('#formSatuan').on('submit', function(e) {
        e.preventDefault();
        const id = $('#satuanId').val();
        const url = id ? '<?php echo url("satuan") ?>/' + id : '<?php echo url("satuan") ?>';
        const method = id ? 'PUT' : 'POST';

        const btn = $(this).find('button[type="submit"]');
        const btnText = btn.text();
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $('#modalSatuan').modal('hide');
                Swal.fire('Success!', res.message, 'success').then(() => location.reload());
            },
            error: function(xhr) {
                handleAjaxError(xhr); // Panggil Helper Error
            },
            complete: function() {
                btn.prop('disabled', false).text(btnText);
            }
        });
    });

    $(document).on('click', '.hapus-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data satuan akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("satuan") ?>/' + id,
                    method: 'DELETE',
                    success: function() {
                        Swal.fire('Deleted!', 'Satuan berhasil dihapus', 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr); // Panggil Helper Error
                    }
                });
            }
        });
    });

    $('#modalSatuan').on('hidden.bs.modal', function() {
        $(this)[0].reset();
        $('#satuanId').val('');
        $('.modal-title').text('Tambah Satuan');
    });
});
</script>