<?php

namespace App\Http\Middleware;

class Guest implements MiddlewareInterface
{
    public function handle($next)
    {
        if (isset($_SESSION['user'])) {
            // sudah login → redirect ke dashboard
            header('Location: ' . url('barang'));
            exit;
        }

        return $next();
    }
}