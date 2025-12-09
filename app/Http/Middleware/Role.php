<?php
namespace App\Http\Middleware;

class Role
{
    /**
     * Handle middleware request.
     *
     * @param callable $next Fungsi selanjutnya (Controller)
     * @param string|null $roleRequired Nama role yang dibutuhkan (misal: 'superadmin')
     */
    public function handle($next, $roleRequired = null)
    {
        if (! isset($_SESSION['user'])) {
            if ($this->isJsonRequest()) {
                $this->sendJson(401, 'Sesi Anda telah habis. Silakan login kembali.');
            }

            header("Location: " . url('login'));
            exit;
        }

        $userRoleId = $_SESSION['user']['id_role'];

        $db   = \DB::connect();
        $stmt = $db->prepare("SELECT nama_role FROM role WHERE id = ?");
        $stmt->execute([$userRoleId]);
        $userRoleName = $stmt->fetchColumn();

        if ($roleRequired && $userRoleName !== $roleRequired) {

            if ($this->isJsonRequest()) {
                $this->sendJson(403, 'Akses Ditolak! Anda tidak memiliki izin (' . $roleRequired . ') untuk aksi ini.');
            }

            header("Location: " . url('forbidden'));
            exit;
        }

        return $next();
    }

    private function isJsonRequest()
    {
        if (! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return true;
        }

        return false;
    }

    private function sendJson($statusCode, $message)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode([
            'status'  => 'error',
            'message' => $message,
        ]);
        exit;
    }
}
