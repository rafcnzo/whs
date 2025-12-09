<?php

namespace App\Http\Middleware;

class Authenticate implements MiddlewareInterface
{
    public function handle($next)
    {
        if (!isset($_SESSION['user'])) {
            // belum login → redirect ke login
            header('Location: ' . url('login'));
            exit;
        }

        // lanjut ke controller
        return $next();
    }
}