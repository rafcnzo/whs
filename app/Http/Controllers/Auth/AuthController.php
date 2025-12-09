<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Exception;

class AuthController extends Controller
{
    public function login()
    {
        return $this->viewPlain('auth.login', [
            'title' => 'Login',
        ]);
    }

    public function register()
    {
        return $this->viewPlain('auth.register',
            ['title' => 'Daftar',
            ]);
    }

    public function prosesLogin()
    {
        header('Content-Type: application/json');

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        try {
            if (empty($username) || empty($password)) {
                throw new Exception("Username dan password wajib diisi!");
            }

            $users = \DB::select("SELECT * FROM user WHERE username = ? LIMIT 1", [$username]);

            if (empty($users)) {
                throw new Exception("Username atau password salah!");
            }

            $user = $users[0];

            if (! password_verify($password, $user->password)) {
                throw new Exception("Username atau password salah!");
            }

            $_SESSION['user'] = [
                'id'        => $user->id,
                'username'  => $user->username,
                'id_role'   => $user->id_role,   // ← WAJIB ada
                'logged_in' => true,
            ];


            echo json_encode([
                'status'   => 'success',
                'message'  => 'Login berhasil! Mengarahkan...',
                'redirect' => url('barang'),
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
        exit;
    }

    public function prosesRegister()
    {
        header('Content-Type: application/json');

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        try {
            if (empty($username) || empty($password)) {
                throw new Exception("Semua field wajib diisi!");
            }

            if ($password !== $confirm) {
                throw new Exception("Konfirmasi password tidak cocok!");
            }

            if (strlen($password) < 8) {
                throw new Exception("Password minimal 8 karakter!"); // ← INI YANG SALAH TADI
            }

            $cek = \DB::select("SELECT id FROM user WHERE username = ?", [$username]);
            if ($cek) {
                throw new Exception("Username sudah terdaftar!");
            }

            $hash = password_hash($password, PASSWORD_BCRYPT);

            \DB::insert("INSERT INTO user (username, password, id_role) VALUES (?, ?, '1')", [
                $username,
                $hash,
            ]);

            echo json_encode([
                'status'   => 'success',
                'message'  => 'Registrasi berhasil! Silakan login.',
                'redirect' => url('/'),
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ]);
        }
        exit;
    }

    public function logout()
    {
        session_destroy();
        header('Location: ' . url('login'));
        exit;
    }

    public function forbidden()
    {
        return $this->view('layouts.noaccess');
    }
}
