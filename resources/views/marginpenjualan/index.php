<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-percentage"></i> Margin Penjualan</h4>
                <h6>Kelola persentase keuntungan (Markup Harga)</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-primary" id="btnTambah">
                    <i class="fas fa-plus"></i> Tambah Margin
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelMargin" class="table table-hover table-striped" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Persentase (%)</th>
                                <th>Status</th>
                                <th>Dibuat Oleh</th>
                                <th>Tanggal Dibuat</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($margin)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-percentage fa-3x mb-3"></i><br>
                                        Belum ada data margin.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($margin as $m): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td>
                                        <span class="fw-bold fs-5 text-primary"><?php echo $m->persen ?>%</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $m->status ? 'success' : 'secondary' ?>">
                                            <?php echo $m->status ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($m->username ?? 'System') ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($m->created_at)) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-btn"
                                            data-id="<?php echo $m->id ?>"
                                            data-persen="<?php echo $m->persen ?>"
                                            data-status="<?php echo $m->status ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger hapus-btn" data-id="<?php echo $m->id ?>">
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

<div class="modal fade" id="modalMargin" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Tambah Margin</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formMargin">
                <div class="modal-body">
                    <input type="hidden" name="id" id="marginId">

                    <div class="mb-3">
                        <label class="form-label">Persentase Keuntungan (%)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="persen" id="persen" class="form-control" placeholder="Misal: 10" required>
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">Contoh: Isi 10 untuk keuntungan 10%.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    function handleAjaxError(xhr) {
        let title = 'Gagal!';
        let msg = 'Terjadi kesalahan sistem.';

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

    $('#btnTambah').click(function() {
        $('#formMargin')[0].reset();
        $('#marginId').val('');
        $('#modalTitle').text('Tambah Margin');
        $('#modalMargin').modal('show');
    });

    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        const persen = $(this).data('persen');
        const status = $(this).data('status');

        $('#marginId').val(id);
        $('#persen').val(persen);
        $('#status').val(status);

        $('#modalTitle').text('Edit Margin');
        $('#modalMargin').modal('show');
    });

    $('#formMargin').on('submit', function(e) {
        e.preventDefault();

        const id = $('#marginId').val();
        const url = id ? '<?php echo url("marginpenjualan") ?>/' + id : '<?php echo url("marginpenjualan") ?>';
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
                if(res.status === 'success') {
                    $('#modalMargin').modal('hide');
                    Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Gagal!', res.message, 'error');
                }
            },
            error: function(xhr) {
                handleAjaxError(xhr); // Pakai helper ini
            },
            complete: function() {
                btn.prop('disabled', false).text(btnText);
            }
        });
    });

    $(document).on('click', '.hapus-btn', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus Margin?',
            text: "Data ini tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("marginpenjualan") ?>/' + id,
                    method: 'DELETE',
                    success: function(res) {
                        Swal.fire('Deleted!', 'Data berhasil dihapus.', 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr); // Pakai helper ini juga
                    }
                });
            }
        });
    });
});
</script>