<?php

namespace App\Http\Controllers;

class Controller
{
    protected $layout = 'layouts/app';

protected function view($view, $data = [])
    {
        extract($data);

        $viewPath   = PROJECT_ROOT . '/resources/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            die("View <strong>{$view}</strong> tidak ditemukan!<br>Path: {$viewPath}");
        }

        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        $layoutPath = PROJECT_ROOT . '/resources/views/' . $this->layout . '.php';
        include $layoutPath;
    }

    protected function viewPlain($view, $data = [])
    {
        extract($data);

        $viewPath   = PROJECT_ROOT . '/resources/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            die("View <strong>{$view}</strong> tidak ditemukan!<br>Path: {$viewPath}");
        }

        include $viewPath;
    }
}