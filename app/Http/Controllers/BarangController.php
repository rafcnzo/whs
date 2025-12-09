<?php

namespace App\Http\Controllers;

use Exception;

class BarangController extends Controller
{
    public function index()
    {
        // Ambil semua barang + join satuan
        $barang = \DB::select("
            SELECT b.*, s.nama_satuan 
            FROM barang b 
            LEFT JOIN satuan s ON b.id_satuan = s.id 
            ORDER BY b.id DESC
        ");

        return $this->view('barang.index', [
            'title'  => 'Data Barang',
            'barang' => $barang,
            'satuan' => \DB::select("SELECT * FROM satuan WHERE status = 1") // buat dropdown
        ]);
    }

    // AJAX: Tambah Barang
    public function store()
    {
        header('Content-Type: application/json');

        try {
            $nama      = trim($_POST['nama'] ?? '');
            $jenis     = $_POST['jenis'] ?? '';
            $id_satuan = (int)($_POST['id_satuan'] ?? 0);
            $harga     = (int)str_replace(['.', ' '], '', $_POST['harga'] ?? 0);
            $status    = isset($_POST['status']) ? 1 : 0;

            if (empty($nama) || $id_satuan == 0) {
                throw new Exception("Nama barang dan satuan wajib diisi!");
            }

            \DB::insert("INSERT INTO barang (jenis, nama, id_satuan, harga, status) VALUES (?, ?, ?, ?, ?)", [
                $jenis, $nama, $id_satuan, $harga, $status
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Barang berhasil ditambah!']);
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
            $nama      = trim($inputData['nama'] ?? '');
            $jenis     = $inputData['jenis'] ?? '';
            $id_satuan = (int)($inputData['id_satuan'] ?? 0);
            
            $hargaRaw  = $inputData['harga'] ?? 0;
            $harga     = (int)str_replace(['.', ' '], '', $hargaRaw);
            
            $status    = isset($inputData['status']) ? 1 : 0;

            if (empty($nama) || $id_satuan == 0) {
                throw new Exception("Data tidak lengkap! Nama dan Satuan harus diisi.");
            }

            // Update Database
            \DB::update("UPDATE barang SET jenis=?, nama=?, id_satuan=?, harga=?, status=? WHERE id=?", [
                $jenis, $nama, $id_satuan, $harga, $status, $id
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Barang berhasil diupdate!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    // AJAX: Hapus
    public function destroy($id)
    {
        header('Content-Type: application/json');
        \DB::delete("DELETE FROM barang WHERE id = ?", [$id]);
        echo json_encode(['status' => 'success', 'message' => 'Barang dihapus!']);
        exit;
    }
}