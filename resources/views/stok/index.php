<div class="page-wrapper">
  <div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
      <div class="page-title">
        <h4><i class="fas fa-boxes"></i> Informasi Stok Barang</h4>
        <h6>Posisi stok terakhir dan mutasi kartu stok</h6>
      </div>
    </div>

    <!-- Tabel Stok -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table id="tabelStok" class="table table-hover table-striped" style="width:100%">
            <thead class="table-dark">
              <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Satuan</th>
                <th>Stok Saat Ini</th>
                <th width="120">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($barang)): ?>
                <tr>
                  <td colspan="5" class="text-center py-5">
                    <div class="empty-state">
                      <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                      <h5 class="text-muted">Belum ada data stok</h5>
                      <p class="text-secondary">Stok akan muncul otomatis dari transaksi penerimaan, penjualan, dan retur.</p>
                    </div>
                  </td>
                </tr>
              <?php else: ?>
                <?php $no = 1;foreach ($barang as $b): ?>
                  <tr>
                    <td><?php echo $no++?></td>
                    <td><?php echo htmlspecialchars($b->nama)?></td>
                    <td><?php echo htmlspecialchars($b->nama_satuan ?? '-')?></td>
                    <td><?php echo number_format($b->stok_terakhir)?></td>
                    <td>
                      <button class="btn btn-sm btn-info detail-btn" data-id="<?php echo $b->id?>">
                        <i class="fas fa-eye"></i>
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

<!-- Modal Mutasi -->
<div class="modal fade" id="modalMutasi" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Mutasi Stok Barang</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <thead class="table-dark">
            <tr>
              <th>Tanggal</th>
              <th>Transaksi</th>
              <th>Masuk</th>
              <th>Keluar</th>
              <th>Stok</th>
            </tr>
          </thead>
          <tbody id="mutasiBody"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- JS khusus halaman stok -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){

  // Detail mutasi stok
  $(document).on('click','.detail-btn',function(){
    const id = $(this).data('id');
    $.get('<?php echo url("stok/mutasi") ?>/'+id,function(res){
      $('#mutasiBody').empty();
      if(res && res.length){
        res.forEach(function(m){
          $('#mutasiBody').append(`
            <tr>
              <td>${new Date(m.created_at).toLocaleString('id-ID')}</td>
              <td>(ID : ${m.id_transaksi}) ${m.label_transaksi}</td>
              <td>${m.masuk}</td>
              <td>${m.keluar}</td>
              <td>${m.stok}</td>
            </tr>
          `);
        });
      } else {
        $('#mutasiBody').append('<tr><td colspan="5" class="text-center">Belum ada mutasi stok</td></tr>');
      }
      $('#modalMutasi').modal('show');
    },'json').fail(function(){
      Swal.fire('Error!','Gagal mengambil data mutasi stok','error');
    });
  });

});
</script>
