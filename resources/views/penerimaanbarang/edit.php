<div class="page-wrapper">
  <div class="content container-fluid">
    <div class="page-header">
      <div class="page-title">
        <h4><i class="fas fa-edit"></i> Edit Penerimaan Barang</h4>
        <h6>Lanjutkan proses penerimaan barang (Draft)</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-dark text-white">
            <h5><i class="fas fa-list"></i> Detail Barang Diterima</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered" id="detailTable">
                  <thead class="table-dark">
                    <tr>
                      <th>No</th>
                      <th>Barang</th>
                      <th>Satuan</th>
                      <th width="120">Jml Terima</th>
                      <th>Harga/Satuan</th>
                      <th>Subtotal</th>
                    </tr>
                  </thead>
                  <tbody id="detailBody">
                    <?php $no = 1;foreach ($detail as $d): ?>
                      <tr>
                        <td class="text-center"><?php echo $no++ ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($d->nama_barang) ?></strong>
                            <input type="hidden" class="id_barang" value="<?php echo $d->id_barang ?>">
                        </td>
                        <td><?php echo htmlspecialchars($d->nama_satuan) ?></td>
                        <td>
                            <input type="number" class="form-control jumlah_terima" min="0" value="<?php echo $d->jumlah_terima ?>">
                        </td>
                        <td>
                            <input type="number" class="form-control harga_terima bg-light" min="0" value="<?php echo $d->harga_satuan_terima ?>" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control sub_total_terima bg-light" value="<?php echo number_format($d->sub_total_terima) ?>" readonly>
                        </td>
                      </tr>
                    <?php endforeach; ?>
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
              <input type="hidden" name="id" id="penerimaanId" value="<?php echo $penerimaan->id ?>">

              <input type="hidden" name="status" id="inputStatus" value="<?php echo $penerimaan->status ?>">

              <div class="mb-3">
                <label>ID Pengadaan (PO)</label>
                <input type="text" class="form-control bg-light" value="<?php echo 'PGD-' . str_pad($penerimaan->id_pengadaan, 4, '0', STR_PAD_LEFT) ?>" readonly>
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
                  <a href="<?php echo url('penerimaan/barang') ?>" class="btn btn-light border">Batal</a>
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
      $('#detailBody tr').each(function(){
        const jumlah = parseInt($(this).find('.jumlah_terima').val()) || 0;
        const harga  = parseInt($(this).find('.harga_terima').val()) || 0;
        const sub    = jumlah * harga;
        $(this).find('.sub_total_terima').val(sub.toLocaleString('id-ID'));
      });
    }
    $(document).on('keyup change','.jumlah_terima', hitung);

    $('.btn-action').on('click', function(e){
      e.preventDefault();

      const status = $(this).data('status');
      $('#inputStatus').val(status);

      let detail = [];
      let valid = true;

      $('#detailBody tr').each(function(){
          const jml = $(this).find('.jumlah_terima').val();

          if(jml === '' || jml < 0) valid = false;

          detail.push({
              id_barang: $(this).find('.id_barang').val(),
              jumlah_terima: jml,
              harga_satuan_terima: $(this).find('.harga_terima').val(),
              sub_total_terima: $(this).find('.sub_total_terima').val().replace(/\D/g,'')
          });
      });

      if(!valid){
          Swal.fire('Warning', 'Jumlah terima tidak boleh kosong atau minus!', 'warning');
          return;
      }

      if(status === 'S'){
          Swal.fire({
              title: 'Selesaikan Penerimaan?',
              text: "Stok akan bertambah dan status Pengadaan akan ditutup.",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonText: 'Ya, Proses!',
              confirmButtonColor: '#198754'
          }).then((res) => {
              if(res.isConfirmed) updateData(detail);
          });
      } else {
          updateData(detail);
      }
    });

    function updateData(detail){
      const form = $('#formPenerimaan');
      const data = form.serializeArray();
      data.push({name:'detail', value: JSON.stringify(detail)});

      const id = $('#penerimaanId').val();

      $.ajax({
        url: '<?php echo url("penerimaan/barang") ?>/' + id,
        method: 'PUT',
        data: $.param(data),
        dataType: 'json',
        success: function(res){
          Swal.fire('Success!', res.message, 'success').then(()=>{
            window.location.href = '<?php echo url("penerimaan/barang") ?>';
          });
        },
        error: function(xhr){
          let msg = 'Terjadi kesalahan saat update';
          if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
          Swal.fire('Error!', msg, 'error');
        }
      });
    }

    hitung();

  });
</script>