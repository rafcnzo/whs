<div class="page-wrapper">
  <div class="content container-fluid">
    <div class="page-header">
      <div class="page-title">
        <h4><i class="fas fa-box-open"></i> Input Penerimaan Barang</h4>
        <h6>Penerimaan barang masuk berdasarkan PO (Pengadaan)</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-dark text-white">
            <h5><i class="fas fa-list"></i> Daftar Barang yang Dipesan</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="detailTable">
                  <thead class="table-dark">
                      <tr>
                      <th>Barang</th>
                      <th>Satuan</th>
                      <th>Jml Pesan</th>
                      <th width="120">Jml Terima</th>
                      <th>Harga/Satuan</th>
                      <th>Subtotal</th>
                      </tr>
                  </thead>
                  <tbody id="detailBody">
                      <tr><td colspan="6" class="text-center text-muted">Pilih Pengadaan terlebih dahulu</td></tr>
                  </tbody>
                </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card border-primary">
          <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-clipboard-check"></i> Data Penerimaan</h5>
          </div>
          <div class="card-body">
            <form id="formPenerimaan">
              
              <input type="hidden" name="status" id="inputStatus" value="D">

              <div class="mb-3">
                <label class="fw-bold">No. Pengadaan (PO)</label>
                <select name="id_pengadaan" id="id_pengadaan" class="form-control" required>
                  <option value="">-- Pilih Pengadaan --</option>
                  <?php foreach ($pengadaan as $p): ?>
                    <option value="<?php echo $p->id ?>">
                        <?php echo 'PGD-' . str_pad($p->id, 4, '0', STR_PAD_LEFT) ?> (<?php echo htmlspecialchars($p->nama_vendor) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
                <small class="text-muted">Hanya menampilkan PO status "Proses".</small>
              </div>

              <div class="mb-3">
                  <label>Penerima (User)</label>
                  <input type="text" class="form-control bg-light" value="<?php echo $_SESSION['user']['username'] ?? '-' ?>" readonly>
              </div>

              <div class="d-grid gap-2 mt-4">
                  <button type="button" class="btn btn-secondary btn-action" data-status="D">
                      <i class="fas fa-save"></i> Simpan Draft
                  </button>
                  <button type="button" class="btn btn-success btn-action" data-status="S">
                      <i class="fas fa-check-circle"></i> Terima & Selesai
                  </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function(){

  // Load Detail Pengadaan
  $('#id_pengadaan').on('change',function(){
    const id = $(this).val();
    if(!id){ 
        $('#detailBody').html('<tr><td colspan="6" class="text-center text-muted">Pilih Pengadaan terlebih dahulu</td></tr>'); 
        return; 
    }
    
    // Loading indicator
    $('#detailBody').html('<tr><td colspan="6" class="text-center">Loading data...</td></tr>');

    $.get('<?php echo url("penerimaan/barang/detailpengadaan") ?>/'+id, function(res){
      $('#detailBody').empty();
      
      if(res.detail && res.detail.length){
        res.detail.forEach(function(d){
          // Auto fill jumlah terima = jumlah pesan (UX memudahkan user)
          $('#detailBody').append(`
            <tr>
                <td>
                    <strong>${d.nama_barang}</strong>
                    <input type="hidden" class="id_barang" value="${d.id_barang}">
                </td>
                <td>${d.nama_satuan}</td>
                <td class="text-center"><span class="badge bg-info">${d.jumlah}</span></td>
                <td>
                    <input type="number" class="form-control jumlah_terima" min="0" max="${d.jumlah}" value="${d.jumlah}">
                </td>
                <td>
                    <input type="text" class="form-control harga_terima bg-light" value="${d.harga_satuan}" readonly>
                </td>
                <td>
                    <input type="text" class="form-control sub_total_terima bg-light" readonly>
                </td>
            </tr>
          `);
        });
        hitung(); // Hitung awal
      } else {
         $('#detailBody').html('<tr><td colspan="6" class="text-center text-danger">Data detail tidak ditemukan</td></tr>');
      }
    },'json').fail(function(){
        $('#detailBody').html('<tr><td colspan="6" class="text-center text-danger">Gagal mengambil data</td></tr>');
    });
  });

  // Hitung Subtotal
  function hitung(){
    $('#detailBody tr').each(function(){
      // Pastikan element ada
      if($(this).find('.jumlah_terima').length){
          const jumlah = parseInt($(this).find('.jumlah_terima').val()) || 0;
          const harga  = parseInt($(this).find('.harga_terima').val()) || 0;
          const sub    = jumlah * harga;
          $(this).find('.sub_total_terima').val(sub.toLocaleString('id-ID'));
      }
    });
  }
  $(document).on('keyup change','.jumlah_terima', hitung);

  // LOGIC TOMBOL SIMPAN
  $('.btn-action').on('click', function(e){
    e.preventDefault();
    
    // 1. Set Status
    const status = $(this).data('status');
    $('#inputStatus').val(status);

    // 2. Kumpulkan Data
    let detail = [];
    let valid = true;

    $('#detailBody tr').each(function(){
        // Cek apakah baris ini valid (ada input jumlah)
        if($(this).find('.id_barang').length){
            const jml = $(this).find('.jumlah_terima').val();
            if(jml === '' || jml < 0) valid = false;

            detail.push({
                id_barang: $(this).find('.id_barang').val(),
                jumlah_terima: jml,
                harga_satuan_terima: $(this).find('.harga_terima').val(),
                sub_total_terima: $(this).find('.sub_total_terima').val().replace(/\D/g,'')
            });
        }
    });

    if(!valid || detail.length === 0){
        Swal.fire('Warning', 'Pastikan data barang dan jumlah terima valid (tidak boleh kosong/minus).', 'warning');
        return;
    }

    // 3. Konfirmasi jika Selesai
    if(status === 'S'){
        Swal.fire({
            title: 'Terima Barang?',
            text: "Stok akan bertambah dan status Pengadaan akan ditutup (Selesai).",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Proses!',
            confirmButtonColor: '#198754'
        }).then((res) => {
            if(res.isConfirmed) sendData(detail);
        });
    } else {
        // Draft langsung simpan
        sendData(detail);
    }
  });

  function sendData(detail){
    const data = $('#formPenerimaan').serializeArray();
    data.push({name:'detail', value: JSON.stringify(detail)});

    $.ajax({
      url: '<?php echo url("penerimaan/barang") ?>',
      method: 'POST',
      data: $.param(data),
      dataType: 'json',
      success: function(res){
        Swal.fire('Success!',res.message,'success').then(()=>location.href='<?php echo url("penerimaan/barang") ?>');
      },
      error: function(xhr){
        let msg = 'Terjadi kesalahan';
        if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
        Swal.fire('Error!', msg ,'error');
      }
    });
  }

});
</script>