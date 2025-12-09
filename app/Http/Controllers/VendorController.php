<?php
namespace App\Http\Controllers;

use Exception;

class VendorController extends Controller
{
    public function index()
    {
        $vendor = \DB::select("SELECT * FROM vendor ORDER BY id DESC");

        return $this->view('vendor.index', [
            'title'  => 'Data Vendor',
            'vendor' => $vendor,
        ]);
    }

    public function store()
    {
        header('Content-Type: application/json');

        try {
            $nama_vendor = trim($_POST['nama_vendor'] ?? '');
            $badan_hukum = trim($_POST['badan_hukum'] ?? '');
            $status      = isset($_POST['status']) ? 1 : 0;

            if (empty($nama_vendor)) {
                throw new Exception("Nama vendor wajib diisi!");
            }

            \DB::insert("INSERT INTO vendor (nama_vendor, badan_hukum, status) VALUES (?, ?, ?)", [
                $nama_vendor, $badan_hukum, $status,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Vendor berhasil ditambah!']);
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

            $nama_vendor = trim($inputData['nama_vendor'] ?? '');
            $badan_hukum      = isset($inputData['badan_hukum']) ? 1 : 0;
            $status      = isset($inputData['status']) ? 1 : 0;

            \DB::update("UPDATE vendor SET nama_vendor=?, badan_hukum=?, status=? WHERE id=?", [
                $nama_vendor, $badan_hukum, $status, $id
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Vendor berhasil diupdate!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function destroy($id)
    {
        header('Content-Type: application/json');
        \DB::delete("DELETE FROM vendor WHERE id = ?", [$id]);
        echo json_encode(['status' => 'success', 'message' => 'Vendor berhasil dihapus!']);
        exit;
    }
}
