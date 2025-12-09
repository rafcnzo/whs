<div class="page-wrapper">
  <div class="content container-fluid">
    <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-cash-register"></i> Transaksi Penjualan Baru</h4>
                <h6>Kasir / Point of Sales (POS)</h6>
            </div>
    </div>
    <div class="row">
      <div class="col-md-7">
        <div class="card">
          <div class="card-header bg-dark text-white">
            <h5><i class="fas fa-box"></i> Pilih Barang</h5>
          </div>
          <div class="card-body">
          <div class="mb-3">
              <input type="text" id="searchBarang" class="form-control" placeholder="Ketik nama barang untuk mencari...">
          </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="barangTable">
                <thead class="table-dark">
                    <tr>
                    <th width="50">Pilih</th>
                    <th>Nama Barang</th>
                    <th>Stok</th> <th>Harga</th>
                    <th width="100">Qty</th>
                    <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $b):
                            $stok     = $b->stok_terkini;
                            $disabled = $stok <= 0 ? 'disabled' : '';
                            $bgRow    = $stok <= 0 ? 'table-danger' : '';
                        ?>
	                    <tr class="<?php echo $bgRow ?>">
	                    <td class="text-center">
	                        <input type="checkbox" class="pilih-barang form-check-input"
	                            data-id="<?php echo $b->id ?>"
	                            data-stok="<?php echo $stok ?>"
	                            <?php echo $disabled ?>>
	                    </td>
	                    <td>
	                        <?php echo htmlspecialchars($b->nama) ?>
	                        <small class="d-block text-muted"><?php echo htmlspecialchars($b->nama_satuan) ?></small>
	                    </td>
	                    <td>
	                        <?php if ($stok > 0): ?>
	                            <span class="badge bg-success"><?php echo $stok ?></span>
	                        <?php else: ?>
                            <span class="badge bg-danger">Habis</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="harga" data-harga="<?php echo $b->harga ?>">
                            Rp                               <?php echo number_format($b->harga) ?>
                        </span>
                    </td>
                    <td>
                        <input type="number" class="form-control qty" min="1" max="<?php echo $stok ?>" value="1" disabled>
                    </td>
                    <td>
                        <input type="text" class="form-control sub_total" readonly>
                    </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                </table>
            </div>
            <hr>
            <div class="d-flex justify-content-end">
              <h5>Total Keranjang: <span id="subtotalDisplay" class="text-primary">Rp 0</span></h5>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="card border-primary">
          <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-receipt"></i> Detail Transaksi</h5>
          </div>
          <div class="card-body">
            <form id="formPenjualan">

              <div class="mb-3">
                <label class="fw-bold">Margin Penjualan</label>
                <select name="id_margin_penjualan" id="marginSelect" class="form-control"> <option value="" data-persen="0">-- Harga Normal (0%) --</option>
                    <?php foreach ($margin as $m): ?>
                        <option value="<?php echo $m->id ?>" data-persen="<?php echo $m->persen ?>">
                            Margin                                   <?php echo $m->persen ?>%
                        </option>
                    <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label class="text-muted small">Kasir Bertugas</label>
                <input type="text" class="form-control bg-light" value="<?php echo $_SESSION['user']['username'] ?? 'Admin' ?>" readonly>
              </div>

              <div class="mb-3">
                <label>Subtotal</label>
                <input type="text" name="subtotal_nilai" id="subtotalInput" class="form-control fw-bold" readonly>
              </div>
              <div class="mb-3">
                <label>PPN (10%)</label>
                <input type="text" name="ppn" id="ppnInput" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label>Total Bayar</label>
                <input type="text" name="total_nilai" id="totalInput" class="form-control fw-bold fs-4 text-center" readonly>
              </div>

              <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-success btn-lg">
                      <i class="fas fa-paper-plane"></i> Proses Transaksi
                  </button>
                  <a href="<?php echo url('penjualan/barang') ?>" class="btn btn-secondary">Batal</a>
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

  function hitung(){
    let subtotalKeseluruhan = 0;

    let persenMargin = parseFloat($('#marginSelect').find(':selected').data('persen')) || 0;

    $('#barangTable tbody tr').each(function(){
      const cb = $(this).find('.pilih-barang');
      const row = $(this);

      if(cb.is(':checked')){
        const hargaModal = parseInt(row.find('.harga').data('harga'));

        let qty = parseInt(row.find('.qty').val()) || 0;
        const maxStok = parseInt(cb.data('stok'));
        if(qty > maxStok) { qty = maxStok; }
        if(qty < 1) { qty = 1; }

        let hargaJualSatuan = hargaModal + (hargaModal * (persenMargin / 100));
        hargaJualSatuan = Math.round(hargaJualSatuan);

        const subRow = hargaJualSatuan * qty;
        row.find('.sub_total').val(subRow.toLocaleString('id-ID'));

        subtotalKeseluruhan += subRow;
      } else {
        row.find('.sub_total').val('');
      }
    });

    $('#subtotalDisplay').text('Rp ' + subtotalKeseluruhan.toLocaleString('id-ID'));
    $('#subtotalInput').val(subtotalKeseluruhan);

    const ppn = Math.round(subtotalKeseluruhan * 0.1);
    $('#ppnInput').val(ppn);

    const totalBayar = subtotalKeseluruhan + ppn;
    $('#totalInput').val(totalBayar);
  }

  $('#marginSelect').on('change', function(){
      hitung();

      const persen = $(this).find(':selected').data('persen');
      if(persen > 0){
          Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'info',
              title: `Harga disesuaikan dengan margin ${persen}%`,
              showConfirmButton: false,
              timer: 1500
          });
      }
  });

  $(document).on('change','.pilih-barang',function(){
    const row = $(this).closest('tr');
    const inputQty = row.find('.qty');

    if($(this).is(':checked')){
        inputQty.prop('disabled', false).focus();
    } else {
        inputQty.prop('disabled', true).val(1);
    }
    hitung();
  });

  $(document).on('keyup change','.qty', function(){
      const row = $(this).closest('tr');
      const maxStok = parseInt(row.find('.pilih-barang').data('stok'));
      let val = parseInt($(this).val());

      if(val > maxStok){
          $(this).val(maxStok);
          Swal.fire({toast:true, position:'top-end', icon:'warning', title:'Stok Maksimal!', timer:1000, showConfirmButton:false});
      }
      hitung();
  });

  $('#formPenjualan').on('submit',function(e){
    e.preventDefault();

    let detail = [];
    let isValid = true;

    let persenMargin = parseFloat($('#marginSelect').find(':selected').data('persen')) || 0;

    $('#barangTable tbody tr').each(function(){
      const cb = $(this).find('.pilih-barang');
      if(cb.is(':checked')){
        const row = $(this);
        const hargaModal = parseInt(row.find('.harga').data('harga'));

        const subTotalClean = row.find('.sub_total').val().replace(/\D/g,'');

        detail.push({
          id_barang: cb.data('id'),
          harga_satuan: hargaModal,
          jumlah: row.find('.qty').val(),
          sub_total: subTotalClean
        });
      }
    });

    if(detail.length === 0){
        Swal.fire('Warning', 'Pilih minimal satu barang!', 'warning');
        return;
    }

    const data = $(this).serializeArray();
    data.push({name:'detail', value: JSON.stringify(detail)});

    const btn = $(this).find('button[type="submit"]');
    const originalText = btn.html();
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

    $.ajax({
      url: '<?php echo url("penjualan/barang") ?>',
      method: 'POST',
      data: $.param(data),
      dataType: 'json',
      success: function(res){
        Swal.fire('Berhasil!', res.message, 'success').then(()=>{
            window.location.href = '<?php echo url("penjualan/barang") ?>';
        });
      },
      error: function(xhr){
        let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan';
        Swal.fire('Gagal!', msg, 'error');
        btn.prop('disabled', false).html(originalText);
      }
    });
  });

  $('#searchBarang').on('keyup', function() {
      var value = $(this).val().toLowerCase();
      $('#barangTable tbody tr').filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
  });

});
</script>