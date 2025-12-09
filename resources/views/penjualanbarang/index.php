<div class="page-wrapper">
    <div class="content container-fluid">

        <div class="page-header">
            <div class="page-title">
                <h4><i class="fas fa-cash-register"></i> Daftar Penjualan</h4>
                <h6>Kelola riwayat transaksi penjualan (POS)</h6>
            </div>
            <div class="page-btn">
                <a href="<?php echo url('penjualan/barang/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Transaksi Baru
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                <div class="col-md-4">
                    <label>Dari Tanggal</label>
                    <input type="date" id="filterStart" class="form-control"
                        value="<?php echo date('Y-m-01'); ?>">
                </div>
                <div class="col-md-4">
                    <label>Sampai Tanggal</label>
                    <input type="date" id="filterEnd" class="form-control"
                        value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4">
                    <button type="button" id="resetFilter" class="btn btn-success w-100">
                    <i class="fas fa-undo"></i> Reset Filter
                    </button>
                </div>
                </div>
            </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabelPenjualan" class="table table-hover table-striped" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>ID Penjualan</th>
                                <th>Tanggal</th>
                                <th>Kasir/User</th> <th>Subtotal</th>
                                <th>PPN</th>
                                <th>Total</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($penjualan)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="empty-state">
                                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Belum ada transaksi penjualan</h5>
                                            <p class="text-secondary">Klik tombol <strong>Transaksi Baru</strong> untuk memulai.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;foreach ($penjualan as $p): ?>
                                <tr>
                                    <td><?php echo $no++ ?></td>
                                    <td><?php echo 'PNJ-' . str_pad($p->id, 4, '0', STR_PAD_LEFT) ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($p->created_at)) ?></td>
                                    <td><?php echo htmlspecialchars($p->username ?? 'Unknown') ?></td>
                                    <td>Rp                                                                                     <?php echo number_format($p->subtotal_nilai) ?></td>
                                    <td>Rp                                                                                     <?php echo number_format($p->ppn) ?></td>
                                    <td class="fw-bold">Rp                                                                                                                     <?php echo number_format($p->total_nilai) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info detail-btn" data-id="<?php echo $p->id ?>" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $p->id ?>" title="Edit / Retur Sebagian">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger hapus-btn" data-id="<?php echo $p->id ?>" title="Batalkan Transaksi (Void)">
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

<div class="modal fade" id="modalDetail" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Struk Penjualan</h5>
        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="infoPenjualan" class="mb-3">
          </div>

        <table class="table table-bordered table-sm">
          <thead class="table-dark">
            <tr>
              <th>Barang</th>
              <th>Satuan</th>
              <th class="text-end">Harga</th>
              <th class="text-center">Qty</th>
              <th class="text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody id="detailBody">
            </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){

    $(document).on('click','.hapus-btn',function(){
        const id = $(this).data('id');
        Swal.fire({
            title: 'Batalkan Transaksi?',
            text: "Stok barang akan dikembalikan ke gudang otomatis!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tidak'
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url: '<?php echo url("penjualan/barang") ?>/' + id,
                    method: 'DELETE',
                    success: (res)=> {
                        const data = typeof res === 'string' ? JSON.parse(res) : res;
                        Swal.fire('Void!', data.message, 'success').then(()=>location.reload());
                    },
                    error: (xhr)=> {
                        const res = xhr.responseJSON || {};
                        Swal.fire('Error!', res.message || 'Gagal menghapus data', 'error');
                    }
                });
            }
        });
    });

    $(document).on('click','.edit-btn',function(){
        const id = $(this).data('id');
        window.location.href = '<?php echo url("penjualan/barang/edit") ?>/' + id;
    });

    function formatKode(prefix, id){
        return prefix + '-' + String(id).padStart(4,'0');
    }

    $(document).on('click', '.detail-btn', function () {
        const id = $(this).data('id');

        $('#infoPenjualan').html('<div class="text-center"><div class="spinner-border spinner-border-sm"></div> Loading...</div>');
        $('#detailBody').empty();
        $('#modalDetail').modal('show');

        $.get('<?php echo url("penjualan/barang/dt") ?>/' + id, function (res) {
            const p = res.penjualan;
            const kodePenjualan = formatKode('PNJ', p.id);

            const subtotal     = parseInt(p.subtotal_nilai) || 0;
            const persenMargin = parseFloat(p.margin_persen) || 0;

            let hpp         = subtotal;
            let keuntungan  = 0;

            if (persenMargin > 0) {
                hpp = Math.round(subtotal / (1 + persenMargin / 100));
                keuntungan = subtotal - hpp;
            }
            let infoHtml = `
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr><td width="130"><strong>ID Transaksi</strong></td><td>: <strong>${kodePenjualan}</strong></td></tr>
                            <tr><td>Tanggal</td><td>: ${new Date(p.created_at).toLocaleString('id-ID')}</td></tr>
                            <tr><td>Kasir</td><td>: ${p.username || '-'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td>HPP (Harga Pokok)</td>
                                <td class="text-end">Rp ${hpp.toLocaleString('id-ID')}</td>
                            </tr>

                            ${persenMargin > 0 ? `
                            <tr class="table-success">
                                <td><strong>Keuntungan (+${persenMargin}%)</strong></td>
                                <td class="text-end fw-bold text-success">Rp ${keuntungan.toLocaleString('id-ID')}</td>
                            </tr>
                            ` : ''}

                            <tr class="border-top pt-2">
                                <td><strong>Subtotal</strong></td>
                                <td class="text-end fw-bold">Rp ${subtotal.toLocaleString('id-ID')}</td>
                            </tr>
                            <tr>
                                <td>PPN 10%</td>
                                <td class="text-end">Rp ${parseInt(p.ppn || 0).toLocaleString('id-ID')}</td>
                            </tr>
                            <tr class="fw-bold fs-4 border-top pt-3 text-primary">
                                <td>Total Bayar</td>
                                <td class="text-end">Rp ${parseInt(p.total_nilai || 0).toLocaleString('id-ID')}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            `;

            $('#infoPenjualan').html(infoHtml);

            $('#detailBody').empty();
            if (res.detail && res.detail.length > 0) {
                res.detail.forEach(function (d) {
                    $('#detailBody').append(`
                        <tr>
                            <td>${d.nama_barang}</td>
                            <td>${d.nama_satuan || '-'}</td>
                            <td class="text-end">Rp ${parseInt(d.harga_satuan).toLocaleString('id-ID')}</td>
                            <td class="text-center">${d.jumlah}</td>
                            <td class="text-end fw-bold">Rp ${parseInt(d.subtotal).toLocaleString('id-ID')}</td>
                        </tr>
                    `);
                });
            } else {
                $('#detailBody').append('<tr><td colspan="5" class="text-center text-muted">Tidak ada detail barang</td></tr>');
            }

        }, 'json').fail(function () {
            Swal.fire('Error!', 'Gagal mengambil data penjualan', 'error');
            $('#modalDetail').modal('hide');
        });
    });

    function applyFilter(){
        const start = $('#filterStart').val();
        const end   = $('#filterEnd').val();

        $('#tabelPenjualan tbody tr').each(function(){
            const tglText = $(this).find('td:eq(2)').text();

            let show = true;

            if(start || end){
                const parts = tglText.split(' ');
                const dateParts = parts[0].split('/');
                const jsDate = new Date(dateParts[2], dateParts[1]-1, dateParts[0]);

                if(start){
                    const startDate = new Date(start);
                    startDate.setHours(0,0,0,0);
                    if(jsDate < startDate) show = false;
                }
                if(end){
                    const endDate = new Date(end);
                    endDate.setHours(23,59,59,999);
                    if(jsDate > endDate) show = false;
                }
            }

            $(this).toggle(show);
        });
    }

    $('#filterStart,#filterEnd').on('change', applyFilter);

    $('#resetFilter').on('click',function(){
        $('#filterStart').val('<?php echo date('Y-m-01'); ?>');
        $('#filterEnd').val('<?php echo date('Y-m-d'); ?>');
        applyFilter();
    });

    applyFilter();

});
</script>