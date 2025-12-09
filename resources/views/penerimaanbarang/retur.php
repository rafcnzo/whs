<div class="page-wrapper">
  <div class="content container-fluid">
    <div class="page-header">
      <div class="page-title">
        <h4><i class="fas fa-undo"></i> Form Retur Barang</h4>
        <h6>Mengembalikan barang diterima ke vendor (Potong Stok)</h6>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12 mb-3">
        <div class="card bg-light border-danger">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>No. Penerimaan:</strong> <?php echo 'PNR-' . str_pad($penerimaan->id, 4, '0', STR_PAD_LEFT) ?>
                    </div>
                    <div class="col-md-4">
                        <strong>Vendor:</strong> <?php echo htmlspecialchars($penerimaan->nama_vendor) ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <strong>Tanggal Terima:</strong> <?php echo date('d M Y', strtotime($penerimaan->created_at)) ?>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="card">
          <div class="card-header bg-danger text-white">
            <h5><i class="fas fa-boxes"></i> Pilih Barang yang Diretur</h5>
          </div>
          <div class="card-body">
            <form id="formRetur">
                <input type="hidden" name="id_penerimaan" value="<?php echo $penerimaan->id ?>">
                
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="returTable">
                      <thead class="table-dark">
                        <tr>
                          <th width="50" class="text-center">Pilih</th>
                          <th>Barang</th>
                          <th width="120">Jml Diterima</th>
                          <th width="150">Jml Retur</th>
                          <th>Alasan Retur</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($detail as $d): ?>
                          <tr>
                            <td class="text-center">
                                <input type="checkbox" class="check-item form-check-input" 
                                    data-id_detail="<?php echo $d->id ?>" 
                                    data-id_barang="<?php echo $d->id_barang ?>">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($d->nama_barang) ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($d->nama_satuan) ?></small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6"><?php echo $d->jumlah_terima ?></span>
                            </td>
                            <td>
                                <input type="number" class="form-control qty-retur" 
                                    min="1" max="<?php echo $d->jumlah_terima ?>" value="1" disabled>
                            </td>
                            <td>
                                <input type="text" class="form-control alasan" placeholder="Contoh: Rusak/Salah Kirim" disabled required>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    <a href="<?php echo url('penerimaan/barang') ?>" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-paper-plane"></i> Proses Retur
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
    // Enable/Disable Input saat checkbox dipilih
    $('.check-item').on('change', function(){
        const row = $(this).closest('tr');
        const inputs = row.find('.qty-retur, .alasan');
        
        if($(this).is(':checked')){
            inputs.prop('disabled', false);
            row.addClass('table-warning');
        } else {
            inputs.prop('disabled', true);
            row.removeClass('table-warning');
        }
    });

    $('.qty-retur').on('keyup change', function(){
        const max = parseInt($(this).attr('max'));
        const val = parseInt($(this).val());
        if(val > max){
            $(this).val(max);
            Swal.fire({toast:true, position:'top-end', icon:'warning', title:'Jumlah retur tidak boleh melebihi jumlah diterima', showConfirmButton:false, timer:1500});
        }
    });

    $('#formRetur').on('submit', function(e){
        e.preventDefault();

        let detail = [];
        $('#returTable tbody tr').each(function(){
            const cb = $(this).find('.check-item');
            if(cb.is(':checked')){
                detail.push({
                    id_detail_penerimaan: cb.data('id_detail'),
                    id_barang: cb.data('id_barang'),
                    jumlah: $(this).find('.qty-retur').val(),
                    alasan: $(this).find('.alasan').val()
                });
            }
        });

        if(detail.length === 0){
            Swal.fire('Warning', 'Pilih minimal satu barang yang ingin diretur!', 'warning');
            return;
        }

        const data = $(this).serializeArray();
        data.push({name:'detail', value: JSON.stringify(detail)});

        const btn = $(this).find('button[type="submit"]');
        const originalText = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');

        $.ajax({
            url: '<?php echo url("penerimaan/barang/retur") ?>', 
            method: 'POST',
            data: $.param(data),
            dataType: 'json',
            success: function(res){
                Swal.fire('Success!', res.message, 'success').then(()=>{
                    window.location.href = '<?php echo url("penerimaan/barang") ?>';
                });
            },
            error: function(xhr){
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Terjadi kesalahan';
                Swal.fire('Error!', msg, 'error');
                btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>