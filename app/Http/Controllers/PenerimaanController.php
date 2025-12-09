<?php
namespace App\Http\Controllers;

use Exception;
use PDO;

class PenerimaanController extends Controller
{
    // --- HELPER KARTU STOK (Untuk catatan stok) ---
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

        if ($jenis_logic == 'PENERIMAAN') {
            $masuk      = $jumlah;
            $keluar     = 0;
            $stok_akhir = $stok_awal + $jumlah;
            $kode_db    = 'P';
        } else if ($jenis_logic == 'BATAL_TERIMA' || $jenis_logic == 'RETUR_VENDOR') {
            $masuk      = 0;
            $keluar     = $jumlah;
            $stok_akhir = $stok_awal - $jumlah;
            $kode_db    = ($jenis_logic == 'RETUR_VENDOR') ? 'R' : 'X'; // R = Retur, X = Void
        }

        $stmt = $pdo->prepare("
            INSERT INTO kartu_stok (jenis_transaksi, masuk, keluar, stok, created_at, id_transaksi, id_barang)
            VALUES (?, ?, ?, ?, NOW(), ?, ?)
        ");
        $stmt->execute([$kode_db, $masuk, $keluar, $stok_akhir, $id_transaksi, $id_barang]);
    }

    public function index()
    {
        $penerimaan = \DB::select("
            SELECT p.*, u.username, pg.id AS id_pengadaan_asli
            FROM penerimaan p
            LEFT JOIN user u ON u.id = p.id_user
            LEFT JOIN pengadaan pg ON pg.id = p.id_pengadaan
            ORDER BY p.id DESC
        ");

        return $this->view('penerimaanbarang.index', [
            'title'      => 'Penerimaan Barang',
            'penerimaan' => $penerimaan,
        ]);
    }

    public function create()
    {
        $pengadaan = \DB::select("
            SELECT p.*, v.nama_vendor
            FROM pengadaan p
            LEFT JOIN vendor v ON v.id = p.id_vendor
            WHERE p.status = 'P'
            ORDER BY p.id DESC
        ");

        return $this->view('penerimaanbarang.create', [
            'title'     => 'Input Penerimaan Barang',
            'pengadaan' => $pengadaan,
        ]);
    }

    public function getDetailPengadaan($id)
    {
        header('Content-Type: application/json');
        try {
            $detail = \DB::select("
                SELECT d.*, b.nama AS nama_barang, s.nama_satuan
                FROM detail_pengadaan d
                LEFT JOIN barang b ON b.id = d.id_barang
                LEFT JOIN satuan s ON s.id = b.id_satuan
                WHERE d.id_pengadaan = ?
            ", [$id]);

            echo json_encode(['status' => 'success', 'detail' => $detail]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function store()
    {
        header('Content-Type: application/json');

        $pdo = \DB::connect();
        $pdo->beginTransaction();

        try {
            $id_pengadaan = $_POST['id_pengadaan'] ?? null;
            $status       = $_POST['status'] ?? 'D';

            if (empty($id_pengadaan)) {
                throw new Exception("Pengadaan wajib dipilih!");
            }

            $user    = $_SESSION['user'] ?? null;
            $id_user = $user['id'] ?? null;
            if (! $id_user) {
                throw new Exception("User tidak terdeteksi!");
            }

            $stmt = $pdo->prepare("
                INSERT INTO penerimaan (id_pengadaan, id_user, status, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$id_pengadaan, $id_user, $status]);
            $id_penerimaan = $pdo->lastInsertId();

            $detail = json_decode($_POST['detail'] ?? '[]', true);
            if (empty($detail)) {
                throw new Exception("Tidak ada barang yang diterima!");
            }

            foreach ($detail as $d) {
                $stmtDet = $pdo->prepare("
                    INSERT INTO detail_penerimaan (id_penerimaan, id_barang, jumlah_terima, harga_satuan_terima, sub_total_terima)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtDet->execute([
                    $id_penerimaan,
                    (int) $d['id_barang'],
                    (int) $d['jumlah_terima'],
                    (int) $d['harga_satuan_terima'],
                    (int) $d['sub_total_terima'],
                ]);

                if ($status === 'S') {
                    $this->catatKartuStok($pdo, (int) $d['id_barang'], 'PENERIMAAN', (int) $d['jumlah_terima'], $id_penerimaan);
                }
            }

            if ($status === 'S') {
                $stmtUp = $pdo->prepare("UPDATE pengadaan SET status = 'S' WHERE id = ?");
                $stmtUp->execute([$id_pengadaan]);
            } else {
            }

            $pdo->commit();

            $msg = ($status == 'S') ? 'Penerimaan Selesai. Stok bertambah!' : 'Penerimaan disimpan sebagai Draft.';
            echo json_encode(['status' => 'success', 'message' => $msg]);

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
            $penerimaan = \DB::select("
                SELECT p.*, u.username, pg.id AS id_pengadaan
                FROM penerimaan p
                LEFT JOIN user u ON u.id = p.id_user
                LEFT JOIN pengadaan pg ON pg.id = p.id_pengadaan
                WHERE p.id = ?
            ", [$id]);

            if (empty($penerimaan)) {
                throw new Exception("Data tidak ditemukan");
            }

            $detail = \DB::select("
                SELECT d.*, b.nama AS nama_barang, s.nama_satuan
                FROM detail_penerimaan d
                LEFT JOIN barang b ON b.id = d.id_barang
                LEFT JOIN satuan s ON s.id = b.id_satuan
                WHERE d.id_penerimaan = ?
            ", [$id]);

            echo json_encode([
                'status'     => 'success',
                'penerimaan' => $penerimaan[0],
                'detail'     => $detail,
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function edit($id)
    {
        $penerimaan = \DB::select("SELECT * FROM penerimaan WHERE id = ?", [$id]);
        if (empty($penerimaan)) {
            die("Data tidak ditemukan");
        }

        $penerimaan = $penerimaan[0];

        if ($penerimaan->status == 'S') {
        }

        $detail = \DB::select("
            SELECT d.*, b.nama AS nama_barang, s.nama_satuan
            FROM detail_penerimaan d
            LEFT JOIN barang b ON b.id = d.id_barang
            LEFT JOIN satuan s ON s.id = b.id_satuan
            WHERE d.id_penerimaan = ?
        ", [$id]);

        return $this->view('penerimaanbarang.edit', [
            'title'      => 'Edit Penerimaan Barang',
            'penerimaan' => $penerimaan,
            'detail'     => $detail,
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

            $status_baru = $inputData['status'] ?? 'D';

            $stmtCek = $pdo->prepare("SELECT status, id_pengadaan FROM penerimaan WHERE id = ?");
            $stmtCek->execute([$id]);
            $dataLama = $stmtCek->fetch(PDO::FETCH_ASSOC);

            if (! $dataLama) {
                throw new Exception("Data tidak ditemukan");
            }

            if ($dataLama['status'] == 'S') {
                throw new Exception("Transaksi sudah Selesai tidak dapat diedit lagi!");
            }

            $stmtUp = $pdo->prepare("UPDATE penerimaan SET status = ? WHERE id = ?");
            $stmtUp->execute([$status_baru, $id]);

            $pdo->prepare("DELETE FROM detail_penerimaan WHERE id_penerimaan = ?")->execute([$id]);

            $detail = json_decode($inputData['detail'] ?? '[]', true);

            if (empty($detail)) {
                throw new Exception("Tidak ada barang yang diterima!");
            }

            foreach ($detail as $d) {
                $stmtDet = $pdo->prepare("
                    INSERT INTO detail_penerimaan (id_penerimaan, id_barang, jumlah_terima, harga_satuan_terima, sub_total_terima)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtDet->execute([
                    $id,
                    (int) $d['id_barang'],
                    (int) $d['jumlah_terima'],
                    (int) $d['harga_satuan_terima'],
                    (int) $d['sub_total_terima'],
                ]);

                if ($status_baru === 'S') {
                    $this->catatKartuStok($pdo, (int) $d['id_barang'], 'PENERIMAAN', (int) $d['jumlah_terima'], $id);
                }
            }

            if ($status_baru === 'S') {
                $pdo->prepare("UPDATE pengadaan SET status = 'S' WHERE id = ?")->execute([$dataLama['id_pengadaan']]);
            }

            $pdo->commit();

            $msg = ($status_baru == 'S') ? 'Penerimaan Selesai. Stok bertambah!' : 'Perubahan Draft disimpan.';
            echo json_encode(['status' => 'success', 'message' => $msg]);

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
            $stmtP = $pdo->prepare("SELECT id_pengadaan, status FROM penerimaan WHERE id = ?");
            $stmtP->execute([$id]);
            $penerimaan = $stmtP->fetch(PDO::FETCH_ASSOC);

            if (! $penerimaan) {
                throw new Exception("Data tidak ditemukan");
            }

            $id_pengadaan   = $penerimaan['id_pengadaan'];
            $status_current = $penerimaan['status'];

            if ($status_current === 'S') {
                $stmtDet = $pdo->prepare("SELECT id_barang, jumlah_terima FROM detail_penerimaan WHERE id_penerimaan = ?");
                $stmtDet->execute([$id]);
                $details = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

                foreach ($details as $d) {
                    $this->catatKartuStok($pdo, $d['id_barang'], 'BATAL_TERIMA', $d['jumlah_terima'], $id);
                }

                $pdo->prepare("UPDATE pengadaan SET status = 'P' WHERE id = ?")->execute([$id_pengadaan]);
            }

            $pdo->prepare("DELETE FROM detail_penerimaan WHERE id_penerimaan = ?")->execute([$id]);
            $pdo->prepare("DELETE FROM penerimaan WHERE id = ?")->execute([$id]);

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data penerimaan dihapus!']);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function retur($id)
    {
        // Ambil Data Penerimaan
        $penerimaan = \DB::select("
            SELECT p.*, pg.id AS id_pengadaan_asli, v.nama_vendor
            FROM penerimaan p
            LEFT JOIN pengadaan pg ON pg.id = p.id_pengadaan
            LEFT JOIN vendor v ON v.id = pg.id_vendor
            WHERE p.id = ? AND p.status = 'S'
        ", [$id]);

        if (empty($penerimaan)) {
            die("Data tidak ditemukan atau status belum Selesai.");
        }
        $penerimaan = $penerimaan[0];

        $detail = \DB::select("
            SELECT d.*, b.nama AS nama_barang, s.nama_satuan
            FROM detail_penerimaan d
            LEFT JOIN barang b ON b.id = d.id_barang
            LEFT JOIN satuan s ON s.id = b.id_satuan
            WHERE d.id_penerimaan = ?
        ", [$id]);

        return $this->view('penerimaanbarang.retur', [
            'title'      => 'Retur Barang ke Vendor',
            'penerimaan' => $penerimaan,
            'detail'     => $detail,
        ]);
    }

    public function storeRetur()
    {
        header('Content-Type: application/json');
        $pdo = \DB::connect();
        $pdo->beginTransaction();

        try {
            $id_penerimaan = $_POST['id_penerimaan'] ?? null;
            $user          = $_SESSION['user'] ?? null;
            $id_user       = $user['id'] ?? null;

            if (! $id_penerimaan || ! $id_user) {
                throw new Exception("Data transaksi tidak valid.");
            }

            $stmt = $pdo->prepare("
                INSERT INTO retur (id_penerimaan, id_user, created_at)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$id_penerimaan, $id_user]);
            $id_retur = $pdo->lastInsertId();

            $detail = json_decode($_POST['detail'] ?? '[]', true);
            if (empty($detail)) {
                throw new Exception("Tidak ada barang yang diretur!");
            }

            foreach ($detail as $d) {
                $qty_retur         = (int) $d['jumlah'];
                $alasan            = $d['alasan'] ?? '-';
                $id_det_penerimaan = (int) $d['id_detail_penerimaan'];
                $id_barang         = (int) $d['id_barang'];

                $stmtCek = $pdo->prepare("SELECT jumlah_terima FROM detail_penerimaan WHERE id = ?");
                $stmtCek->execute([$id_det_penerimaan]);
                $dt = $stmtCek->fetch(PDO::FETCH_ASSOC);

                if ($qty_retur > $dt['jumlah_terima']) {
                    throw new Exception("Jumlah retur melebihi jumlah yang diterima!");
                }

                $stok_gudang = $this->getSisaStok($pdo, $id_barang);
                if ($qty_retur > $stok_gudang) {
                    throw new Exception("Gagal Retur. Stok gudang saat ini kurang (Mungkin sudah terjual). Sisa: $stok_gudang");
                }

                $stmtDet = $pdo->prepare("
                    INSERT INTO detail_retur (id_retur, id_detail_penerimaan, jumlah, alasan)
                    VALUES (?, ?, ?, ?)
                ");
                $stmtDet->execute([$id_retur, $id_det_penerimaan, $qty_retur, $alasan]);

                $this->catatKartuStok($pdo, $id_barang, 'RETUR_VENDOR', $qty_retur, $id_retur);
            }

            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Retur berhasil disimpan. Stok barang berkurang!']);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}
