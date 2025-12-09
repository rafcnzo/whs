<?php
namespace App\Http\Controllers;

use Exception;

class UserController extends Controller
{
    public function index()
    {
        $user = \DB::select("
            SELECT u.*, r.nama_role
            FROM user u
            LEFT JOIN role r ON u.id_role = r.id
            ORDER BY u.id DESC
        ");

        $role = \DB::select("SELECT * FROM role ORDER BY id DESC");

        return $this->view('user.index', [
            'title' => 'Manajemen User',
            'user'  => $user,
            'role'  => $role,
        ]);
    }

    public function storeUser()
    {
        header('Content-Type: application/json');

        try {
            $username = trim($_POST['username'] ?? '');
            $id_role  = intval($_POST['id_role'] ?? 0);
            $password = trim($inputData['password'] ?? '');

            if (empty($username)) {
                throw new Exception("Username wajib diisi!");
            }

            \DB::insert("INSERT INTO user (username, password, id_role) VALUES (?, ?, ?)", [
                $username, password_hash($password, PASSWORD_DEFAULT), $id_role,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'User berhasil ditambah!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function updateUser($id)
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

            $username = trim($inputData['username'] ?? '');
            $id_role  = intval($inputData['id_role'] ?? 0);
            $password = trim($inputData['password'] ?? '');

            // Validasi role
            $exists = \DB::select("SELECT COUNT(*) as cnt FROM role WHERE id=?", [$id_role]);
            if ($exists[0]->cnt == 0) {
                throw new Exception("Role tidak valid");
            }

            \DB::update("UPDATE user SET username=?, password=?, id_role=? WHERE id=?", [
                $username, $password, $id_role, $id,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'User berhasil diupdate!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function destroyUser($id)
    {
        header('Content-Type: application/json');
        \DB::delete("DELETE FROM user WHERE id = ?", [$id]);
        echo json_encode(['status' => 'success', 'message' => 'user berhasil dihapus!']);
        exit;
    }

    public function storeRole()
    {
        header('Content-Type: application/json');

        try {
            $nama_role = trim($_POST['nama_role'] ?? '');

            if (empty($nama_role)) {
                throw new Exception("Nama role wajib diisi!");
            }

            \DB::insert("INSERT INTO role (nama_role) VALUES (?)", [
                $nama_role,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Role berhasil ditambah!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function updateRole($id)
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

            $nama_role = trim($inputData['nama_role'] ?? '');

            \DB::update("UPDATE role SET nama_role=? WHERE id=?", [
                $nama_role, $id,
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Role berhasil diupdate!']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function destroyRole($id)
    {
        header('Content-Type: application/json');
        \DB::delete("DELETE FROM role WHERE id = ?", [$id]);
        echo json_encode(['status' => 'success', 'message' => 'Role berhasil dihapus!']);
        exit;
    }
}
