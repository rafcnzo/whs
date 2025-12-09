<?php
namespace App\Http\Controllers;

use Exception;

class SatuanController extends Controller
{
    public function index()
    {
        $satuan = \DB::select("SELECT * FROM satuan ORDER BY id DESC");

        return $this->view('satuan.index', [
            'title'  => 'Data Satuan',
            'satuan' => $satuan,
        ]);
    }

    public function store()
    {
        header('Content-Type: application/json');

        try {
            $nama_satuan = trim($_POST['nama_satuan'] ?? '');
            $status      = isset($_POST['status']) ? 1 : 0;

            if (empty($nama_satuan)) {
                throw new Exception("Nama satuan wajib diisi!");
            }

            \DB::insert("INSERT INTO satuan (nama_satuan, status) VALUES (?, ?)", [
                $nama_satuan, $status,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Satuan berhasil ditambah!']);
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
                $rawInput = file_get_contents("php://input");
                
                parse_str($rawInput, $inputData); 
            } else {
                $inputData = $_POST;
            }

            $nama_satuan = trim($inputData['nama_satuan'] ?? '');
            
            $status      = isset($inputData['status']) ? 1 : 0;

            \DB::update("UPDATE satuan SET nama_satuan=?, status=? WHERE id=?", [
                $nama_satuan, $status, $id
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Satuan berhasil diupdate!']);
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
        \DB::delete("DELETE FROM satuan WHERE id = ?", [$id]);
        echo json_encode(['status' => 'success', 'message' => 'Satuan berhasil dihapus!']);
        exit;
    }
}
