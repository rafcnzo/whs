<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-boxes"></i> Daftar Vendor</h4>
                <h6>Kelola Semua Vendor</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalVendor">
                    <i class="fas fa-plus"></i> Tambah Vendor
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelVendor" class="table table-hover table-striped" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Nama Vendor</th>
                                <th>Badan Hukum</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($vendor)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                        <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data vendor</h5>
                                            <p class="text-secondary">Klik tombol <strong>Tambah Vendor</strong> untuk mulai menambahkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($vendor as $s): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo htmlspecialchars($s->nama_vendor) ?></td>
                                    <td>
                                        <?php
                                            $badan_hukum_labels = [
                                                'P' => 'PT (Perseroan Terbatas)',
                                                'C' => 'CV (Commanditaire Vennootschap)',
                                                'F' => 'Firma',
                                                'K' => 'Koperasi',
                                                'Y' => 'Yayasan',
                                                'N' => 'Non Badan Hukum'
                                            ];
                                            $bh = htmlspecialchars($s->badan_hukum);
                                            echo isset($badan_hukum_labels[$bh]) ? $badan_hukum_labels[$bh] : $bh;
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $s->status ? 'success' : 'secondary' ?>">
                                            <?php echo $s->status ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-btn"
                                                data-id="<?php echo $s->id ?>"
                                                data-nama_vendor="<?php echo htmlspecialchars($s->nama_vendor) ?>"
                                                data-badan_hukum="<?php echo htmlspecialchars($s->badan_hukum) ?>"
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
<div class="modal fade" id="modalVendor" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formVendor">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Form Vendor</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="vendorId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nama Vendor</label>
                            <input type="text" name="nama_vendor" class="form-control" placeholder="Contoh: Berdikari Jaya" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Badan Hukum</label>
                            <select name="badan_hukum" id="badan_hukum" required>
                                <option value="">-- Pilih Badan Hukum --</option>
                                <option value="P">PT (Perseroan Terbatas)</option>
                                <option value="C">CV (Commanditaire Vennootschap)</option>
                                <option value="F">Firma</option>
                                <option value="K">Koperasi</option>
                                <option value="Y">Yayasan</option>
                                <option value="N">Non Badan Hukum</option>
                            </select>
                        </div>
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
                    <button type="submit" class="btn btn-primary">Simpan Vendor</button>
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
        let msg = 'Terjadi kesalahan sistem.';

        if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }

        // Cek 403 Forbidden (Role tidak sesuai)
        if (xhr.status === 403) {
            title = 'Akses Ditolak!';
        } 
        // Cek 401 Unauthorized (Session habis)
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
        $('#formVendor')[0].reset();
        $('#vendorId').val('');
        $('.modal-title').text('Tambah Vendor');
        // $('#modalVendor').modal('show'); // Uncomment jika tombol di HTML tidak punya data-bs-target
    });

    $(document).on('click', '.edit-btn', function() {
        const d = $(this).data();
        $('#vendorId').val(d.id);
        $('[name="nama_vendor"]').val(d.nama_vendor);
        $('[name="status"]').val(d.status);
        
        $('.modal-title').text('Edit Vendor');
        $('#modalVendor').modal('show');
    });

    $('#formVendor').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#vendorId').val();
        const url = id ? '<?php echo url("vendor") ?>/' + id : '<?php echo url("vendor") ?>';
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
                $('#modalVendor').modal('hide');
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
            text: "Data vendor akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("vendor") ?>/' + id,
                    method: 'DELETE',
                    success: function() {
                        Swal.fire('Deleted!', 'Vendor berhasil dihapus', 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr); // Panggil Helper Error
                    }
                });
            }
        });
    });

    $('#modalVendor').on('hidden.bs.modal', function() {
        $(this)[0].reset();
        $('#vendorId').val('');
        $('.modal-title').text('Tambah Vendor');
    });
});
</script>