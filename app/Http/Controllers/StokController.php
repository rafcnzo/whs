<?php
namespace App\Http\Controllers;

use Exception;

class StokController extends Controller
{
    public function index()
    {
        $barang = \DB::select("
            SELECT b.id, b.nama, s.nama_satuan,
                COALESCE((
                    SELECT ks.stok 
                    FROM kartu_stok ks 
                    WHERE ks.id_barang = b.id 
                    ORDER BY ks.created_at DESC 
                    LIMIT 1
                ),0) AS stok_terakhir
            FROM barang b
            LEFT JOIN satuan s ON s.id = b.id_satuan
            WHERE b.status = 1
            ORDER BY b.nama ASC
        ");

        return $this->view('stok.index', [
            'title'  => 'Informasi Stok Barang',
            'barang' => $barang
        ]);
    }

    public function mutasi($id_barang)
    {
        header('Content-Type: application/json');
        try {
            $mutasi = \DB::select("
                SELECT ks.*, b.nama AS nama_barang
                FROM kartu_stok ks
                LEFT JOIN barang b ON b.id = ks.id_barang
                WHERE ks.id_barang = ?
                ORDER BY ks.created_at DESC
            ", [$id_barang]);

            foreach ($mutasi as &$m) {
                $m->label_transaksi = $this->mapJenis($m->jenis_transaksi);
            }

            echo json_encode($mutasi);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
        }
        exit;
    }

    private function mapJenis($kode)
    {
        $map = [
            'P' => 'Penerimaan',
            'R' => 'Retur Vendor',
            'X' => 'Batal Terima',
            'J' => 'Penjualan',
            'M' => 'Koreksi Masuk'
        ];
        return $map[$kode] ?? 'Unknown';
    }
}
