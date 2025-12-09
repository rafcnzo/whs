<div class="page-wrapper">
  <div class="content container-fluid">

    <div class="page-header">
      <div class="page-title">
        <h4><i class="fas fa-boxes"></i> Daftar Penerimaan Barang</h4>
        <h6>Riwayat barang masuk ke gudang</h6>
      </div>
      <div class="page-btn">
        <a href="<?php echo url('penerimaan/barang/create') ?>" class="btn btn-primary">
          <i class="fas fa-plus"></i> Input Penerimaan
        </a>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <div class="row align-items-end">
          <div class="col-md-3">
            <label>Dari Tanggal</label>
            <input type="date" id="filterStart" class="form-control" value="<?php echo date('Y-m-01'); ?>">
          </div>
          <div class="col-md-3">
            <label>Sampai Tanggal</label>
            <input type="date" id="filterEnd" class="form-control" value="<?php echo date('Y-m-d'); ?>">
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

    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="tabelPenerimaan" class="table table-hover table-striped" style="width:100%">
            <thead class="table-dark">
              <tr>
                <th>No</th>
                <th>ID Penerimaan</th>
                <th>Tanggal</th>
                <th>ID Pengadaan</th>
                <th>User</th>
                <th>Status</th>
                <th width="150">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($penerimaan)): ?>
                <tr>
                  <td colspan="7" class="text-center py-5">
                    <div class="empty-state">
                      <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                      <h5 class="text-muted">Belum ada data penerimaan</h5>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php $no = 1;foreach ($penerimaan as $p): ?>
                <tr>
                  <td><?php echo $no++ ?></td>
                  <td><?php echo 'PNR-' . str_pad($p->id,4,'0',STR_PAD_LEFT) ?></td>
                  <td><?php echo date('d/m/Y H:i', strtotime($p->created_at)) ?></td>
                  <td>
                      <a href="javascript:void(0)" class="text-decoration-none">
                        <?php echo 'PGD-' . str_pad($p->id_pengadaan_asli,4,'0',STR_PAD_LEFT) ?>
                      </a>
                  </td>
                  <td><?php echo htmlspecialchars($p->username ?? '-') ?></td>
                  <td>
                    <span class="badge bg-<?php echo $p->status == 'S' ? 'success' : 'secondary' ?>">
                      <?php echo $p->status == 'S' ? 'Selesai' : 'Draft' ?>
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-info detail-btn" data-id="<?php echo $p->id ?>" title="Detail">
                      <i class="fas fa-eye"></i>
                    </button>
                    <?php if($p->status == 'D'): ?>
                        <a href="<?php echo url('penerimaan/barang/edit/' . $p->id) ?>" class="btn btn-sm btn-warning" title="Lanjutkan Draft">
                            <i class="fas fa-edit"></i>
                        </a>
                    <button class="btn btn-sm btn-danger hapus-btn" data-id="<?php echo $p->id ?>" title="Hapus/Void">
                      <i class="fas fa-trash"></i>
                    </button>
                    <?php else: ?>
                        <a href="<?php echo url('penerimaan/barang/retur/' . $p->id) ?>" class="btn btn-sm btn-dark" title="Retur Barang">
                            <i class="fas fa-undo"></i>
                        </a>
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
        <h5 class="modal-title">Ringkasan Penerimaan</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="infoPenerimaan" class="mb-3"></div>
        <table class="table table-bordered table-sm">
          <thead class="table-dark">
            <tr>
              <th>Barang</th>
              <th>Satuan</th>
              <th>Harga</th>
              <th>Jml Terima</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody id="detailBodyModal"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function(){
        
        function formatKode(prefix, id){
            return prefix + '-' + String(id).padStart(4,'0');
        }

        // Hapus
        $(document).on('click','.hapus-btn',function(){
            const id = $(this).data('id');
            Swal.fire({
            title: 'Hapus / Void Penerimaan?',
            text: "Jika status 'Selesai', stok akan dikembalikan (berkurang)!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            confirmButtonColor: '#d33'
            }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                url: '<?php echo url("penerimaan/barang") ?>/' + id,
                method: 'DELETE',
                success: (res)=> {
                    // Parse JSON jika string
                    let data = typeof res == 'string' ? JSON.parse(res) : res;
                    Swal.fire('Deleted!', data.message, 'success').then(()=>location.reload());
                },
                error: (xhr)=> Swal.fire('Error!','Gagal menghapus data','error')
                });
            }
            });
        });

        // Detail
        $(document).on('click','.detail-btn',function(){
            const id = $(this).data('id');
            $('#detailBodyModal').html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
            $('#modalDetail').modal('show');

            $.get('<?php echo url("penerimaan/barang/dt") ?>/' + id, function(res){
                const p = res.penerimaan;
                const kodePenerimaan = formatKode('PNR', p.id);
                const kodePengadaan = formatKode('PGD', p.id_pengadaan);
                
                let infoHtml = `
                    <div class="row">
                        <div class="col-6">
                            <strong>ID Penerimaan:</strong> ${kodePenerimaan}<br>
                            <strong>Tanggal:</strong> ${new Date(p.created_at).toLocaleString('id-ID')}<br>
                        </div>
                        <div class="col-6 text-end">
                             <strong>ID Pengadaan:</strong> ${kodePengadaan}<br>
                             <strong>Status:</strong> <span class="badge bg-${p.status=='S'?'success':'secondary'}">${p.status=='S'?'Selesai':'Draft'}</span>
                        </div>
                    </div>
                `;
                $('#infoPenerimaan').html(infoHtml);

                $('#detailBodyModal').empty();
                if(res.detail && res.detail.length){
                    res.detail.forEach(function(d){
                    $('#detailBodyModal').append(`
                        <tr>
                        <td>${d.nama_barang}</td>
                        <td>${d.nama_satuan}</td>
                        <td>Rp ${parseInt(d.harga_satuan_terima).toLocaleString('id-ID')}</td>
                        <td class="text-center">${d.jumlah_terima}</td>
                        <td class="text-end">Rp ${parseInt(d.sub_total_terima).toLocaleString('id-ID')}</td>
                        </tr>
                    `);
                    });
                }
            },'json');
        });

        // Filter Logic (Fixed Index)
        function applyFilter(){
            const start = $('#filterStart').val();
            const end   = $('#filterEnd').val();
            const status= $('#filterStatus').val();

            $('#tabelPenerimaan tbody tr').each(function(){
                // Kolom Tanggal index 2 (karena ada No dan ID)
                const tglText = $(this).find('td:eq(2)').text(); 
                // Kolom Status index 5
                const statusText = $(this).find('td:eq(5) span').text().trim(); 

                let show = true;
                
                // Date Filter
                if(start || end){
                    const parts = tglText.split(' ');
                    const dateParts = parts[0].split('/'); // dd/mm/yyyy
                    const jsDate = new Date(dateParts[2], dateParts[1]-1, dateParts[0]);

                    if(start){
                        const startDate = new Date(start);
                        if(jsDate < startDate) show = false;
                    }
                    if(end){
                        const endDate = new Date(end);
                        endDate.setHours(23,59,59);
                        if(jsDate > endDate) show = false;
                    }
                }

                // Status Filter
                if(status){
                    if(status=='S' && statusText!='Selesai') show=false;
                    if(status=='D' && statusText!='Draft') show=false;
                }

                $(this).toggle(show);
            });
        }

        $('#filterStart,#filterEnd,#filterStatus').on('change', applyFilter);
        $('#resetFilter').on('click',function(){
            $('#filterStart').val('<?php echo date('Y-m-01'); ?>');
            $('#filterEnd').val('<?php echo date('Y-m-d'); ?>');
            $('#filterStatus').val('');
            applyFilter();
        });
        
        applyFilter();
    });
</script>