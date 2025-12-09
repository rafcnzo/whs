<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-boxes"></i> Daftar Barang</h4>
                <h6>Kelola semua barang gudang</h6>
            </div>
            <div class="page-btn">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBarang">
                    <i class="fas fa-plus"></i> Tambah Barang
                </button>
            </div>
        </div>

        <!-- Tabel Barang -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelBarang" class="table table-hover table-striped" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Status</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($barang)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data barang</h5>
                                            <p class="text-secondary">Klik tombol <strong>Tambah Barang</strong> untuk mulai menambahkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($barang as $b): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td>
                                        <?php
                                            $jenisLabels = [
                                                'makanan'         => ['label' => 'Makanan', 'class' => 'primary'],
                                                'minuman'         => ['label' => 'Minuman', 'class' => 'info'],
                                                'kebutuhan_bayi'  => ['label' => 'Kebutuhan Bayi', 'class' => 'success'],
                                                'grooming pria'   => ['label' => 'Grooming Pria', 'class' => 'warning'],
                                                'grooming wanita' => ['label' => 'Grooming Wanita', 'class' => 'danger'],
                                            ];
                                            $jenisKey = strtolower($b->jenis);
                                            $label    = isset($jenisLabels[$jenisKey]) ? $jenisLabels[$jenisKey]['label'] : htmlspecialchars($b->jenis);
                                            $class    = isset($jenisLabels[$jenisKey]) ? $jenisLabels[$jenisKey]['class'] : 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $class ?>">
                                            <?php echo $label ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($b->nama) ?></td>
                                    <td><?php echo htmlspecialchars($b->nama_satuan ?? '-') ?></td>
                                    <td>Rp                                                                                                                                                 <?php echo number_format($b->harga) ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $b->status ? 'success' : 'secondary' ?>">
                                            <?php echo $b->status ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-btn"
                                                data-id="<?php echo $b->id ?>"
                                                data-jenis="<?php echo $b->jenis ?>"
                                                data-nama="<?php echo htmlspecialchars($b->nama) ?>"
                                                data-satuan="<?php echo $b->id_satuan ?>"
                                                data-harga="<?php echo $b->harga ?>"
                                                data-status="<?php echo $b->status ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger hapus-btn" data-id="<?php echo $b->id ?>">
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
<div class="modal fade" id="modalBarang" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="formBarang">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Form Barang</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="barangId">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Jenis Barang</label>
                            <select name="jenis" class="form-control" required>
                                <option value="makanan">Makanan</option>
                                <option value="minuman">Minuman</option>
                                <option value="kebutuhan_bayi">Kebutuhan Bayi</option>
                                <option value="grooming pria">Grooming Pria</option>
                                <option value="grooming wanita">Grooming Wanita</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Satuan</label>
                            <select name="id_satuan" class="form-control" required>
                                <option value="">Pilih Satuan</option>
                                <?php foreach ($satuan as $s): ?>
                                    <option value="<?php echo $s->id ?>"><?php echo htmlspecialchars($s->nama_satuan) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Laptop ASUS" required>
                    </div>

                    <div class="mb-3">
                        <label>Harga (Rp)</label>
                        <input type="text" name="harga" class="form-control rupiah" placeholder="0" required>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="status" value="1" class="form-check-input" id="statusCheck" checked>
                        <label class="form-check-label">Aktif</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JS KHUSUS HALAMAN INI -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
    $('.rupiah').on('keyup', function() {
        let val = this.value.replace(/\D/g, '');
        this.value = formatRupiah(val);
    });

    $(document).ready(function() {

    function handleAjaxError(xhr) {
        let title = 'Gagal!';
        let msg = 'Terjadi kesalahan pada server.';

        if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }

        if (xhr.status === 403) {
            title = 'Akses Ditolak!'; // Spesifik untuk role yang tidak sesuai
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

    $(document).on('click', '.edit-btn', function() {
        const d = $(this).data();
        $('#barangId').val(d.id);
        $('[name="jenis"]').val(d.jenis);
        $('[name="nama"]').val(d.nama);
        $('[name="id_satuan"]').val(d.satuan);
        $('[name="harga"]').val(formatRupiah(d.harga));
        $('#statusCheck').prop('checked', d.status == 1);
        $('.modal-title').text('Edit Barang');
        $('#modalBarang').modal('show');
    });

    $('.page-btn .btn-primary').on('click', function(){
        $('#formBarang')[0].reset();
        $('#barangId').val('');
        $('.modal-title').text('Tambah Barang');
    });

    $('#formBarang').on('submit', function(e) {
        e.preventDefault();
        const id = $('#barangId').val();
        const url = id ? '<?php echo url("barang") ?>/' + id : '<?php echo url("barang") ?>';
        const method = id ? 'PUT' : 'POST';

        // Loading State
        const btn = $(this).find('button[type="submit"]');
        const btnText = btn.text();
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $('#modalBarang').modal('hide');
                Swal.fire('Berhasil!', res.message, 'success').then(() => location.reload());
            },
            error: function(xhr) {
                handleAjaxError(xhr);
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
            text: "Data barang akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo url("barang") ?>/' + id,
                    method: 'DELETE',
                    success: function(res) {
                        Swal.fire('Deleted!', res.message, 'success').then(() => location.reload());
                    },
                    error: function(xhr) {
                        handleAjaxError(xhr);
                    }
                });
            }
        });
    });

    $('#modalBarang').on('hidden.bs.modal', function() {
        $(this)[0].reset();
        $('#barangId').val('');
        $('.modal-title').text('Tambah Barang');
    });
});
</script>