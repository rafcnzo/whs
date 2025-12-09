<div class="page-wrapper">
  <div class="content container-fluid">
    <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-truck-loading"></i> Halaman Buat Pengadaan Barang</h4>
                <h6>Penambahan Pengaadaan Barang</h6>
            </div>

    </div>
    <div class="row">
      <!-- KIRI: Tabel Barang -->
      <div class="col-md-7">
        <div class="card">
          <div class="card-header bg-dark text-white">
            <h5><i class="fas fa-box"></i> Pilih Barang</h5>
          </div>
          <div class="card-body">
          <div class="mb-3">
              <input type="text" id="searchBarang" class="form-control" placeholder="Cari nama barang...">
          </div>
            <table class="table table-hover table-striped" id="barangTable">
              <thead class="table-dark">
                <tr>
                  <th>Pilih</th>
                  <th>Nama Barang</th>
                  <th>Satuan</th>
                  <th>Harga</th>
                  <th>Qty</th>
                  <th>Sub Total</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($barang as $b): ?>
                <tr>
                  <td><input type="checkbox" class="pilih-barang" data-id="<?php echo $b->id ?>"></td>
                  <td><?php echo htmlspecialchars($b->nama) ?></td>
                  <td><?php echo htmlspecialchars($b->nama_satuan) ?></td>
                  <td><span class="harga" data-harga="<?php echo $b->harga ?>">Rp<?php echo number_format($b->harga) ?></span></td>
                  <td><input type="number" class="form-control qty" min="1" value="1" disabled></td>
                  <td><input type="text" class="form-control sub_total" readonly></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <hr>
            <div class="d-flex justify-content-end">
              <h5>Total Subtotal: <span id="subtotalDisplay">Rp 0</span></h5>
            </div>
          </div>
        </div>
      </div>

      <!-- KANAN: Form Pengadaan -->
      <div class="col-md-5">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5><i class="fas fa-truck-loading"></i> Form Pengadaan</h5>
          </div>
          <div class="card-body">
            <form id="formPengadaan">

              <input type="hidden" name="status" id="inputStatus" value="D">

              <div class="mb-3">
                <label>Vendor</label>
                <select name="id_vendor" class="form-control" required>
                  <option value="">Pilih Vendor</option>
                  <?php foreach ($vendor as $v): ?>
                    <option value="<?php echo $v->id ?>"><?php echo htmlspecialchars($v->nama_vendor) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-3">
                <label>Subtotal</label>
                <input type="text" name="subtotal_nilai" id="subtotalInput" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label>PPN (10%)</label>
                <input type="text" name="ppn" id="ppnInput" class="form-control" readonly>
              </div>
              <div class="mb-3">
                <label>Total</label>
                <input type="text" name="total_nilai" id="totalInput" class="form-control fw-bold fs-4" readonly>
              </div>

              <div class="d-grid gap-2">
                  <button type="button" class="btn btn-secondary btn-action" data-status="D">
                      <i class="fas fa-save"></i> Simpan sebagai Draft
                  </button>
                  <button type="button" class="btn btn-primary btn-action" data-status="P">
                      <i class="fas fa-paper-plane"></i> Kirim (Proses)
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
  function hitung(){
    let subtotal = 0;
    $('#barangTable tbody tr').each(function(){
      const cb = $(this).find('.pilih-barang');
      const harga = parseInt($(this).find('.harga').data('harga'));
      const qty = parseInt($(this).find('.qty').val()) || 0;
      if(cb.is(':checked')){
        const sub = harga * qty;
        $(this).find('.sub_total').val(sub.toLocaleString('id-ID'));
        subtotal += sub;
      } else {
        $(this).find('.sub_total').val('');
      }
    });
    $('#subtotalDisplay').text('Rp ' + subtotal.toLocaleString('id-ID'));
    $('#subtotalInput').val(subtotal);
    const ppn = subtotal * 0.1;
    $('#ppnInput').val(ppn);
    $('#totalInput').val(subtotal+ppn);
  }

  $(document).on('change','.pilih-barang',function(){
    const row = $(this).closest('tr');
    row.find('.qty').prop('disabled', !$(this).is(':checked'));
    hitung();
  });

  $(document).on('keyup change','.qty',hitung);

  function kirimData(detail) {
    const form = $('#formPengadaan');
    const data = form.serializeArray();
    data.push({name:'detail', value: JSON.stringify(detail)});

    $.ajax({
      url: '<?php echo url("pengadaan/barang") ?>',
      method: 'POST',
      data: $.param(data),
      dataType: 'json',
      success: function(res){
        Swal.fire('Success!', res.message, 'success').then(()=>{
          window.location.href = '<?php echo url("pengadaan/barang") ?>';
        });
      },
      error: function(xhr){
         let msg = 'Terjadi kesalahan';
         if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
         Swal.fire('Error!', msg, 'error');
      }
    });
  }

  $('.btn-action').on('click', function(e){
    e.preventDefault();

    const status = $(this).data('status');
    $('#inputStatus').val(status);

    let detail = [];
    $('#barangTable tbody tr').each(function(){
      if($(this).find('.pilih-barang').is(':checked')){
        detail.push({
          id_barang: $(this).find('.pilih-barang').data('id'),
          harga_satuan: $(this).find('.harga').data('harga'),
          jumlah: $(this).find('.qty').val(),
          sub_total: $(this).find('.sub_total').val().replace(/\D/g,'')
        });
      }
    });

    if(detail.length === 0) {
        Swal.fire('Warning', 'Pilih minimal satu barang!', 'warning');
        return;
    }

    if(status === 'P') {
        Swal.fire({
            title: 'Kirim Pengadaan?',
            text: "Data yang dikirim akan diproses ke penerimaan.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                kirimData(detail);
            }
        });
    } else {
        kirimData(detail);
    }
  });
  $('#searchBarang').on('keyup', function() {
      var value = $(this).val().toLowerCase();
      $('#barangTable tbody tr').filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
  });
});
</script>
