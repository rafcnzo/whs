<?php
namespace App\Http\Controllers;

use Exception;

class PengadaanController extends Controller
{

    public function index()
    {
        $pengadaan = \DB::select("
            SELECT p.*, v.nama_vendor, u.username
            FROM pengadaan p
            LEFT JOIN vendor v ON v.id = p.id_vendor
            LEFT JOIN user u ON u.id = p.id_user
            ORDER BY p.id DESC
        ");

        return $this->view('pengadaanbarang.index', [
            'title'     => 'Pengadaan Barang',
            'pengadaan' => $pengadaan,
        ]);
    }

    public function create()
    {
        $pengadaan = \DB::select("
            SELECT p.*, v.nama_vendor, u.username
            FROM pengadaan p
            LEFT JOIN vendor v ON v.id = p.id_vendor
            LEFT JOIN user u ON u.id = p.id_user
            ORDER BY p.id DESC
        ");

        return $this->view('pengadaanbarang.create', [
            'title'     => 'Pengadaan Barang',
            'pengadaan' => $pengadaan,
            'vendor'    => \DB::select("SELECT * FROM vendor WHERE status = 1"),
            'barang'    => \DB::select("SELECT b.*, s.nama_satuan FROM barang b LEFT JOIN satuan s ON s.id = b.id_satuan WHERE b.status = 1"),
        ]);
    }

    public function store()
    {
        header('Content-Type: application/json');

        $pdo = \DB::connect();
        $pdo->beginTransaction();

        try {
            $id_vendor      = $_POST['id_vendor'] ?? null;
            $status         = $_POST['status'] ?? 'D'; 
            
            $subtotal_nilai = (int) ($_POST['subtotal_nilai'] ?? 0);
            $ppn            = (int) ($_POST['ppn'] ?? 0);
            $total_nilai    = (int) ($_POST['total_nilai'] ?? 0);

            if (empty($id_vendor)) {
                throw new Exception("Vendor wajib dipilih!");
            }

            $user    = $_SESSION['user'] ?? null;
            $id_user = $user['id'] ?? null;
            if (!$id_user) {
                throw new Exception("User tidak terdeteksi!");
            }
            $stmt = $pdo->prepare("
                INSERT INTO pengadaan (id_user, status, id_vendor, subtotal_nilai, ppn, total_nilai, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$id_user, $status, $id_vendor, $subtotal_nilai, $ppn, $total_nilai]);

            $id_pengadaan = $pdo->lastInsertId();

            $detail = json_decode($_POST['detail'] ?? '[]', true);
            
            if(empty($detail)) throw new Exception("Tidak ada barang yang dipilih");

            foreach ($detail as $d) {
                $stmtDet = $pdo->prepare("
                    INSERT INTO detail_pengadaan (id_pengadaan, id_barang, harga_satuan, jumlah, sub_total)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtDet->execute([
                    $id_pengadaan,
                    (int) $d['id_barang'],
                    (int) $d['harga_satuan'],
                    (int) $d['jumlah'],
                    (int) $d['sub_total'],
                ]);
            }

            $pdo->commit();

            $msg = ($status == 'P') ? 'Pengadaan berhasil dikirim untuk diproses!' : 'Pengadaan berhasil disimpan sebagai Draft!';
            echo json_encode(['status' => 'success', 'message' => $msg]);

        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function edit($id)
    {
        $pengadaan = \DB::select("SELECT * FROM pengadaan WHERE id = ?", [$id]);
        if (empty($pengadaan)) {
            die("Data pengadaan tidak ditemukan");
        }
        $pengadaan = $pengadaan[0];

        $detail = \DB::select("
                SELECT d.*,
                    b.nama AS nama_barang,
                    s.nama_satuan
                FROM detail_pengadaan d
                LEFT JOIN barang b ON b.id = d.id_barang
                LEFT JOIN satuan s ON s.id = b.id_satuan
                WHERE d.id_pengadaan = ?
            ", [$id]);

        $vendor = \DB::select("SELECT * FROM vendor WHERE status = 1");

        $barang = \DB::select("SELECT b.*, s.nama_satuan FROM barang b LEFT JOIN satuan s ON s.id = b.id_satuan WHERE b.status = 1");

        return $this->view('pengadaanbarang.edit', [
            'title'     => 'Edit Pengadaan Barang',
            'pengadaan' => $pengadaan,
            'detail'    => $detail,
            'vendor'    => $vendor,
            'barang'    => $barang,
        ]);
    }

    public function getdt($id)
    {
        header('Content-Type: application/json');
        try {
            $pengadaan = \DB::select("
                SELECT p.*, v.nama_vendor, u.username
                FROM pengadaan p
                LEFT JOIN vendor v ON v.id = p.id_vendor
                LEFT JOIN user u ON u.id = p.id_user
                WHERE p.id = ?
            ", [$id]);

            if (empty($pengadaan)) {
                throw new Exception("Data pengadaan tidak ditemukan");
            }

            $detail = \DB::select("
                SELECT d.*,
                    b.nama AS nama_barang,
                    s.nama_satuan
                FROM detail_pengadaan d
                LEFT JOIN barang b ON b.id = d.id_barang
                LEFT JOIN satuan s ON s.id = b.id_satuan
                WHERE d.id_pengadaan = ?
            ", [$id]);

            echo json_encode([
                'status'    => 'success',
                'pengadaan' => $pengadaan[0],
                'detail'    => $detail,
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function update($id)
    {
        header('Content-Type: application/json');

        try {
            $inputData = [];
            if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
                parse_str(file_get_contents("php://input"), $inputData);
            } else {
                $inputData = $_POST;
            }

            $id_vendor      = $inputData['id_vendor'] ?? null;
            $status         = $inputData['status'] ?? 'D';
            $subtotal_nilai = (int) ($inputData['subtotal_nilai'] ?? 0);
            $ppn            = (int) ($inputData['ppn'] ?? 0);
            $total_nilai    = (int) ($inputData['total_nilai'] ?? 0);

            if (empty($id_vendor)) {
                throw new Exception("Vendor wajib dipilih!");
            }

            \DB::update("
            UPDATE pengadaan
            SET status=?, id_vendor=?, subtotal_nilai=?, ppn=?, total_nilai=?
            WHERE id=?
        ", [$status, $id_vendor, $subtotal_nilai, $ppn, $total_nilai, $id]);

            \DB::delete("DELETE FROM detail_pengadaan WHERE id_pengadaan=?", [$id]);

            $detail = json_decode($inputData['detail'] ?? '[]', true);
            foreach ($detail as $d) {
                \DB::insert("
                INSERT INTO detail_pengadaan (id_pengadaan, id_barang, harga_satuan, jumlah, sub_total)
                VALUES (?, ?, ?, ?, ?)
            ", [
                    $id,
                    (int) $d['id_barang'],
                    (int) $d['harga_satuan'],
                    (int) $d['jumlah'],
                    (int) $d['sub_total'],
                ]);
            }

            echo json_encode(['status' => 'success', 'message' => 'Pengadaan berhasil diupdate!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function destroy($id)
    {
        header('Content-Type: application/json');

        try {
            \DB::delete("DELETE FROM detail_pengadaan WHERE id_pengadaan = ?", [$id]);

            \DB::delete("DELETE FROM pengadaan WHERE id = ?", [$id]);

            echo json_encode(['status' => 'success', 'message' => 'Data Pengadaan dan detailnya berhasil dihapus!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

}
