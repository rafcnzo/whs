<div class="page-wrapper">
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-truck-loading"></i> Daftar Pengadaan</h4>
                <h6>Kelola semua transaksi pengadaan barang</h6>
            </div>
            <div class="page-btn">
                <a href="<?php echo url('pengadaan/barang/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pengadaan
                </a>
            </div>
        </div>

        <!-- Tabel Pengadaan -->
        <div class="card">
            <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                <div class="col-md-3">
                    <label>Dari Tanggal</label>
                    <input type="date" id="filterStart" class="form-control"
                        value="<?php echo date('Y-m-01'); ?>"> <!-- default: awal bulan -->
                </div>
                <div class="col-md-3">
                    <label>Sampai Tanggal</label>
                    <input type="date" id="filterEnd" class="form-control"
                        value="<?php echo date('Y-m-d'); ?>"> <!-- default: hari ini -->
                </div>
                <div class="col-md-3">
                    <label>Status</label>
                    <select id="filterStatus" class="form-control">
                    <option value="">Semua</option>
                    <option value="S">Selesai</option>
                    <option value="D">Draft</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" id="resetFilter" class="btn btn-success w-100">
                    <i class="fas fa-undo"></i> Reset Filter
                    </button>
                </div>
                </div>
            </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelPengadaan" class="table table-hover table-striped" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>ID Pengadaan</th>
                                <th>Tanggal</th>
                                <th>Vendor</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Subtotal</th>
                                <th>PPN</th>
                                <th>Total</th>
                                <th width="170">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pengadaan)): ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada data pengadaan</h5>
                                            <p class="text-secondary">Klik tombol <strong>Tambah Pengadaan</strong> untuk mulai menambahkan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($pengadaan as $p): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo 'PGD-' . str_pad($p->id,4,'0',STR_PAD_LEFT) ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($p->created_at)) ?></td>
                                    <td><?php echo htmlspecialchars($p->nama_vendor ?? '-') ?></td>
                                    <td><?php echo htmlspecialchars($p->username ?? '-') ?></td>
                                    <td>
                                        <?php
                                            // Menentukan label warna dan teks status
                                            if ($p->status == 'D') {
                                                $badgeClass = 'secondary';
                                                $statusText = 'Draft';
                                            } elseif ($p->status == 'P') {
                                                $badgeClass = 'warning';
                                                $statusText = 'Dalam Proses';
                                            } elseif ($p->status == 'S') {
                                                $badgeClass = 'success';
                                                $statusText = 'Selesai';
                                            } else {
                                                $badgeClass = 'secondary';
                                                $statusText = $p->status;
                                            }
                                        ?>
                                        <span class="badge bg-<?php echo $badgeClass; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td>Rp                                                                                                                                                                                                                                                             <?php echo number_format($p->subtotal_nilai) ?></td>
                                    <td>Rp                                                                                                                                                                                                                                                             <?php echo number_format($p->ppn) ?></td>
                                    <td>Rp                                                                                                                                                                                                                                                             <?php echo number_format($p->total_nilai) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info detail-btn" data-id="<?php echo $p->id ?>">
                                            <i class="fas fa-list"></i>
                                        </button>
                                        <?php if ($p->status == 'D'): ?>
                                            <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $p->id ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger hapus-btn" data-id="<?php echo $p->id ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
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

<div class="modal fade" id="modalDetail" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Ringkasan Pengadaan</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <!-- Info induk -->
        <div id="infoPengadaan" class="mb-3">
          <!-- diisi via JS -->
        </div>

        <!-- Detail barang -->
        <table class="table table-bordered">
          <thead class="table-dark">
            <tr>
              <th>Barang</th>
              <th>Satuan</th>
              <th>Harga Satuan</th>
              <th>Jumlah</th>
              <th>Sub Total</th>
            </tr>
          </thead>
          <tbody id="detailBody">
            <!-- isi via JS -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){

    function hitungTotal(){
        let subtotal = 0;
        $('#detailTable tbody tr').each(function(){
            let harga = parseInt($(this).find('.harga').val().replace(/\D/g,'')) || 0;
            let jumlah = parseInt($(this).find('.jumlah').val()) || 0;
            let sub = harga * jumlah;
            $(this).find('.sub_total').val(sub.toLocaleString('id-ID'));
            subtotal += sub;
        });
        $('#subtotal').val(subtotal.toLocaleString('id-ID'));
        let ppn = subtotal * 0.1;
        $('#ppn').val(ppn.toLocaleString('id-ID'));
        $('#total').val((subtotal+ppn).toLocaleString('id-ID'));
    }

    // Hitung otomatis
    $(document).on('keyup change','.harga,.jumlah',hitungTotal);
    $(document).on('click','.removeDetail',function(){ $(this).closest('tr').remove(); hitungTotal(); });
    // Hapus Pengadaan
    $(document).on('click','.hapus-btn',function(){
        const id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data pengadaan akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url: '<?php echo url("pengadaan/barang") ?>/' + id,
                    method: 'DELETE',
                    success: ()=> Swal.fire('Deleted!','Pengadaan berhasil dihapus','success').then(()=>location.reload()),
                    error: ()=> Swal.fire('Error!','Gagal menghapus data','error')
                });
            }
        });
    });

    $(document).on('click','.edit-btn',function(){
        const id = $(this).data('id');
        window.location.href = '<?php echo url("pengadaan/barang/edit") ?>/' + id;
    });

    function formatKode(prefix, id){
        return prefix + '-' + String(id).padStart(4,'0');
    }

    $(document).on('click','.detail-btn',function(){
    const id = $(this).data('id');
    $.get('<?php echo url("pengadaan/barang/dt") ?>/' + id,function(res){
      // isi info induk
      const p = res.pengadaan;
      const kodePengadaan = formatKode('PGD', p.id);
      let infoHtml = `
        <table class="table table-sm">
            <tr><th>ID Pengadaan</th><td>${kodePengadaan}</td></tr>
            <tr><th>Tanggal</th><td>${new Date(p.created_at).toLocaleString('id-ID')}</td></tr>
            <tr><th>Vendor</th><td>${p.nama_vendor}</td></tr>
            <tr><th>User</th><td>${p.username}</td></tr>
            <tr><th>Status</th><td>
                ${
                    p.status == 'S' ? 'Selesai' :
                    p.status == 'P' ? 'Proses' :
                    'Draft'
                }
            </td></tr>
            <tr><th>Subtotal</th><td>Rp ${parseInt(p.subtotal_nilai).toLocaleString('id-ID')}</td></tr>
            <tr><th>PPN</th><td>Rp ${parseInt(p.ppn).toLocaleString('id-ID')}</td></tr>
            <tr><th>Total</th><td>Rp ${parseInt(p.total_nilai).toLocaleString('id-ID')}</td></tr>
        </table>
      `;
      $('#infoPengadaan').html(infoHtml);

      // isi detail barang
      $('#detailBody').empty();
      if(res.detail && res.detail.length){
        res.detail.forEach(function(d){
          $('#detailBody').append(`
            <tr>
              <td>${d.nama_barang}</td>
              <td>${d.nama_satuan}</td>
              <td>Rp ${parseInt(d.harga_satuan).toLocaleString('id-ID')}</td>
              <td>${d.jumlah}</td>
              <td>Rp ${parseInt(d.sub_total).toLocaleString('id-ID')}</td>
            </tr>
          `);
        });
      } else {
        $('#detailBody').append('<tr><td colspan="5" class="text-center">Tidak ada detail barang</td></tr>');
      }

      $('#modalDetail').modal('show');
    },'json').fail(function(){
      Swal.fire('Error!','Gagal mengambil data pengadaan','error');
    });
  });

    function applyFilter(){
        const start = $('#filterStart').val();
        const end   = $('#filterEnd').val();
        const status= $('#filterStatus').val();

        $('#tabelPengadaan tbody tr').each(function(){
        const tglText = $(this).find('td:eq(1)').text(); // kolom tanggal
        const statusText = $(this).find('td:eq(4) span').text().trim();

        let show = true;

        // filter tanggal
        if(start || end){
            const parts = tglText.split(' ');
            const dateParts = parts[0].split('/');
            const jsDate = new Date(dateParts[2], dateParts[1]-1, dateParts[0]); // Y,M,D

            if(start){
            const startDate = new Date(start);
            if(jsDate < startDate) show = false;
            }
            if(end){
            const endDate = new Date(end);
            if(jsDate > endDate) show = false;
            }
        }

        // filter status
        if(status){
            if(status=='S' && statusText!='Selesai') show=false;
            if(status=='D' && statusText!='Draft') show=false;
        }

        $(this).toggle(show);
        });
    }

    $('#filterStart,#filterEnd,#filterStatus').on('change',applyFilter);

    // reset filter
    $('#resetFilter').on('click',function(){
        $('#filterStart').val('<?php echo date('Y-m-01'); ?>');
        $('#filterEnd').val('<?php echo date('Y-m-d'); ?>');
        $('#filterStatus').val('');
        applyFilter();
    });

    // jalankan filter default saat load
    applyFilter();

});
</script>
