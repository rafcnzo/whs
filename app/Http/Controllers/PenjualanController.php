<?php
namespace App\Http\Controllers;

use Exception;
use PDO;

class PenjualanController extends Controller
{
    private function getSisaStok($pdo, $id_barang)
    {
        $stmt = $pdo->prepare("SELECT stok FROM kartu_stok WHERE id_barang = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$id_barang]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? (int) $data['stok'] : 0;
    }

    private function catatKartuStok($pdo, $id_barang, $jenis_logic, $jumlah, $id_transaksi)
    {
        $stok_awal = $this->getSisaStok($pdo, $id_barang);

        $masuk      = 0;
        $keluar     = 0;
        $stok_akhir = 0;
        $kode_db    = '';

        if ($jenis_logic == 'PENJUALAN' || $jenis_logic == 'REVISI_KELUAR') {
            $masuk      = 0;
            $keluar     = $jumlah;
            $stok_akhir = $stok_awal - $jumlah;
            $kode_db    = 'J';
        } else {
            $masuk      = $jumlah;
            $keluar     = 0;
            $stok_akhir = $stok_awal + $jumlah;
            $kode_db    = 'M';
        }

        $stmt = $pdo->prepare("
            INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stok, created_at, id_transaksi, id_barang)
            VALUES (?, ?, ?, ?, NOW(), ?, ?)
        ");
        $stmt->execute([$kode_db, $masuk, $keluar, $stok_akhir, $id_transaksi, $id_barang]);
    }

    public function index()
    {
        $penjualan = \DB::select("
            SELECT p.*, u.username
            FROM penjualan p
            LEFT JOIN user u ON u.id = p.id_user
            ORDER BY p.id DESC
        ");

        return $this->view('penjualanbarang.index', [
            'title'     => 'Penjualan Barang',
            'penjualan' => $penjualan,
        ]);
    }

    public function create()
    {
        $margin = \DB::select("SELECT * FROM margin_penjualan WHERE status = 1");

        $barang = \DB::select("SELECT b.*, s.nama_satuan FROM barang b LEFT JOIN satuan s ON s.id = b.id_satuan WHERE b.status = 1");

        foreach ($barang as $b) {
            $pdo             = \DB::connect();
            $b->stok_terkini = $this->getSisaStok($pdo, $b->id);
        }

        return $this->view('penjualanbarang.create', [
            'title'  => 'Buat Penjualan Baru',
            'margin' => $margin,
            'barang' => $barang,
        ]);
    }

    public function store()
    {
        header('Content-Type: application/json');
        $pdo = \DB::connect();
        $pdo->beginTransaction();

        try {
            $subtotal_nilai = (int) ($_POST['subtotal_nilai'] ?? 0);
            $ppn            = (int) ($_POST['ppn'] ?? 0);
            $total_nilai    = (int) ($_POST['total_nilai'] ?? 0);
            $id_margin      = ! empty($_POST['id_margin_penjualan']) ? $_POST['id_margin_penjualan'] : null;

            $user    = $_SESSION['user'] ?? null;
            $id_user = $user['id'] ?? null;
            if (! $id_user) {
                throw new Exception("Sesi habis, silakan login ulang!");
            }

            $stmt = $pdo->prepare("
                INSERT INTO penjualan (id_user, subtotal_nilai, ppn, total_nilai, id_margin_penjualan, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$id_user, $subtotal_nilai, $ppn, $total_nilai, $id_margin]);
            $id_penjualan = $pdo->lastInsertId();

            $detail = json_decode($_POST['detail'] ?? '[]', true);
            if (empty($detail)) {
                throw new Exception("Keranjang belanja kosong!");
            }

            foreach ($detail as $d) {
                $id_barang = (int) $d['id_barang'];
                $jumlah    = (int) $d['jumlah'];
                $harga     = (int) $d['harga_satuan'];
                $sub_total = (int) $d['sub_total'];

                // Validasi Stok
                $stok_saat_ini = $this->getSisaStok($pdo, $id_barang);
                if ($stok_saat_ini < $jumlah) {
                    throw new Exception("Stok barang ID $id_barang tidak cukup! Sisa: $stok_saat_ini");
                }

                $stmtDet = $pdo->prepare("
                    INSERT INTO detail_penjualan (id_penjualan, id_barang, harga_satuan, jumlah, subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtDet->execute([$id_penjualan, $id_barang, $harga, $jumlah, $sub_total]);

                $this->catatKartuStok($pdo, $id_barang, 'PENJUALAN', $jumlah, $id_penjualan);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Transaksi Berhasil!']);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function getdt($id)
    {
        header('Content-Type: application/json');
        try {
            $penjualan = \DB::select("
                SELECT p.*, u.username, mp.persen AS margin_persen
                FROM penjualan p
                LEFT JOIN user u ON u.id = p.id_user
                LEFT JOIN margin_penjualan mp ON mp.id = p.id_margin_penjualan
                WHERE p.id = ?
            ", [$id]);

            if (empty($penjualan)) {
                throw new Exception("Data penjualan tidak ditemukan");
            }

            $detail = \DB::select("
                SELECT d.*,
                    b.nama AS nama_barang,
                    s.nama_satuan
                FROM detail_penjualan d
                LEFT JOIN barang b ON b.id = d.id_barang
                LEFT JOIN satuan s ON s.id = b.id_satuan
                WHERE d.id_penjualan = ?
            ", [$id]);

            echo json_encode([
                'status'    => 'success',
                'penjualan' => $penjualan[0],
                'detail'    => $detail,
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function edit($id)
    {
        $penjualan = \DB::select("SELECT p.*, mp.persen AS margin_persen
                              FROM penjualan p
                              LEFT JOIN margin_penjualan mp ON mp.id = p.id_margin_penjualan
                              WHERE p.id = ?", [$id]);

        if (empty($penjualan)) {
            die("Data penjualan tidak ditemukan!");
        }
        $penjualan = $penjualan[0];

        $detail = \DB::select("
            SELECT d.*,
                b.nama AS nama_barang,
                s.nama_satuan,
                b.harga AS harga_modal
            FROM detail_penjualan d
            LEFT JOIN barang b ON b.id = d.id_barang
            LEFT JOIN satuan s ON s.id = b.id_satuan
            WHERE d.id_penjualan = ?
        ", [$id]);

        $detailMap = [];
        foreach ($detail as $d) {
            $detailMap[$d->id_barang] = [
                'jumlah'   => $d->jumlah,
                'subtotal' => $d->subtotal,
                'harga'    => $d->harga_modal,
            ];
        }

        $barang = \DB::select("
            SELECT b.*, s.nama_satuan
            FROM barang b
            LEFT JOIN satuan s ON s.id = b.id_satuan
            WHERE b.status = 1
            ORDER BY b.nama ASC
        ");

        $pdo = \DB::connect(); 

        foreach ($barang as $b) {
            $stokSekarang = $this->getSisaStok($pdo, $b->id);
            $qtyLama               = $detailMap[$b->id]['jumlah'] ?? 0;
            $b->stok_tersedia_edit = $stokSekarang + $qtyLama; 
            $b->stok_terkini       = $stokSekarang;           
        }

        $margin = \DB::select("SELECT * FROM margin_penjualan WHERE status = 1 ORDER BY persen ASC");

        return $this->view('penjualanbarang.edit', [
            'title'     => 'Edit Penjualan #' . $penjualan->id,
            'penjualan' => $penjualan,
            'detail'    => $detail,
            'detailMap' => $detailMap, 
            'barang'    => $barang,
            'margin'    => $margin,
        ]);
    }

    public function update($id)
    {
        header('Content-Type: application/json');
        $pdo = \DB::connect();
        $pdo->beginTransaction();

        try {
            $inputData = [];
            if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                parse_str(file_get_contents("php://input"), $inputData);
            } else {
                $inputData = $_POST;
            }

            $clean = function($val) {
                return (int) preg_replace('/[^0-9]/', '', $val ?? '0');
            };

            $subtotal_nilai = $clean($inputData['subtotal_nilai'] ?? 0);
            $ppn            = $clean($inputData['ppn'] ?? 0);
            $total_nilai    = $clean($inputData['total_nilai'] ?? 0);
            $id_margin      = !empty($inputData['id_margin_penjualan']) ? $inputData['id_margin_penjualan'] : null;

            $stmtOld = $pdo->prepare("SELECT id_barang, jumlah FROM detail_penjualan WHERE id_penjualan = ?");
            $stmtOld->execute([$id]);
            $oldDetails = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

            foreach ($oldDetails as $od) {
                $this->catatKartuStok($pdo, $od['id_barang'], 'REVISI_MASUK', $od['jumlah'], $id);
            }

            $pdo->prepare("DELETE FROM detail_penjualan WHERE id_penjualan=?")->execute([$id]);

            $stmtUp = $pdo->prepare("
                UPDATE penjualan SET subtotal_nilai=?, ppn=?, total_nilai=?, id_margin_penjualan=?
                WHERE id=?
            ");
            $stmtUp->execute([$subtotal_nilai, $ppn, $total_nilai, $id_margin, $id]);

            $detail = json_decode($inputData['detail'] ?? '[]', true);
            foreach ($detail as $d) {
                $id_barang = (int) $d['id_barang'];
                $jumlah    = (int) $d['jumlah'];
                $harga     = (int) $d['harga_satuan'];
                $sub_total = (int) $d['sub_total'];

                $stok_saat_ini = $this->getSisaStok($pdo, $id_barang);
                if ($stok_saat_ini < $jumlah) {
                    throw new Exception("Stok barang ID $id_barang tidak cukup! Sisa: $stok_saat_ini");
                }

                $pdo->prepare("
                    INSERT INTO detail_penjualan (id_penjualan, id_barang, harga_satuan, jumlah, subtotal)
                    VALUES (?, ?, ?, ?, ?)
                ")->execute([$id, $id_barang, $harga, $jumlah, $sub_total]);

                $this->catatKartuStok($pdo, $id_barang, 'REVISI_KELUAR', $jumlah, $id);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Penjualan berhasil diupdate!']);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function destroy($id)
    {
        header('Content-Type: application/json');
        $pdo = \DB::connect();
        $pdo->beginTransaction();

        try {
            $stmtOld = $pdo->prepare("SELECT id_barang, jumlah FROM detail_penjualan WHERE id_penjualan = ?");
            $stmtOld->execute([$id]);
            $oldDetails = $stmtOld->fetchAll(PDO::FETCH_ASSOC);

            foreach ($oldDetails as $od) {
                $this->catatKartuStok($pdo, $od['id_barang'], 'BATAL_JUAL', $od['jumlah'], $id);
            }

            $pdo->prepare("DELETE FROM detail_penjualan WHERE id_penjualan = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM penjualan WHERE id = ?")->execute([$id]);

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Penjualan dihapus, stok telah dikembalikan!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}
