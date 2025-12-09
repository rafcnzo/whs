<?php
namespace App\Http\Controllers;

use Exception;
use PDO;

class MarginPenjualanController extends Controller
{
    public function index()
    {
        $margin = \DB::select("
            SELECT m.*, u.username 
            FROM margin_penjualan m
            LEFT JOIN user u ON u.id = m.id_user
            ORDER BY m.id DESC
        ");

        return $this->view('marginpenjualan.index', [
            'title'  => 'Kelola Margin Penjualan',
            'margin' => $margin
        ]);
    }

    public function store()
    {
        header('Content-Type: application/json');
        try {
            $persen = $_POST['persen'] ?? 0;
            $status = $_POST['status'] ?? 1;
            
            $user    = $_SESSION['user'] ?? null;
            $id_user = $user['id'] ?? null;

            if(!$id_user) throw new Exception("User tidak terdeteksi!");

            \DB::insert("
                INSERT INTO margin_penjualan (persen, status, id_user, created_at)
                VALUES (?, ?, ?, NOW())
            ", [$persen, $status, $id_user]);

            echo json_encode(['status' => 'success', 'message' => 'Margin berhasil ditambahkan!']);
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

            $persen = $inputData['persen'] ?? 0;
            $status = $inputData['status'] ?? 1;
            $id_user= $_SESSION['user']['id'];

            \DB::update("
                UPDATE margin_penjualan 
                SET persen = ?, status = ?, id_user = ?, update_at = NOW()
                WHERE id = ?
            ", [$persen, $status, $id_user, $id]);

            echo json_encode(['status' => 'success', 'message' => 'Margin berhasil diupdate!']);
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
            \DB::delete("DELETE FROM margin_penjualan WHERE id = ?", [$id]);
            
            echo json_encode(['status' => 'success', 'message' => 'Margin berhasil dihapus!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}