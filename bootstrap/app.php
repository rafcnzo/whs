<?php
session_start();
require_once __DIR__ . '/../helpers/helpers.php';
require_once __DIR__ . '/../app/Database/DB.php';
require_once __DIR__ . '/../app/Http/Controllers/Controller.php';
require_once __DIR__ . '/../app/Http/Controllers/Auth/AuthController.php';
require_once __DIR__ . '/../app/Http/Controllers/BarangController.php';
require_once __DIR__ . '/../app/Http/Controllers/SatuanController.php';
require_once __DIR__ . '/../app/Http/Controllers/VendorController.php';
require_once __DIR__ . '/../app/Http/Controllers/MarginPenjualanController.php';
require_once __DIR__ . '/../app/Http/Controllers/UserController.php';
require_once __DIR__ . '/../app/Http/Controllers/StokController.php';
require_once __DIR__ . '/../app/Http/Controllers/PengadaanController.php';
require_once __DIR__ . '/../app/Http/Controllers/PenerimaanController.php';
require_once __DIR__ . '/../app/Http/Controllers/PenjualanController.php';

require_once __DIR__ . '/../app/Http/Middleware/MiddlewareInterface.php';
require_once __DIR__ . '/../app/Http/Middleware/Authenticate.php';
require_once __DIR__ . '/../app/Http/Middleware/Guest.php';
require_once __DIR__ . '/../app/Http/Middleware/Role.php';

class Route
{
    public static $routes = [
        'GET'    => [],
        'POST'   => [],
        'PUT'    => [],
        'DELETE' => [],
    ];

    public static $current = null;

    public static function get($uri, $action)
    {
        $uri                       = trim($uri, '/');
        $uri                       = $uri === '' ? '/' : $uri;
        self::$routes['GET'][$uri] = ['action' => $action, 'middleware' => []];
        self::$current             = ['method' => 'GET', 'uri' => $uri];
        return new class {
            public function middleware($mw)
            {
                if (is_array($mw)) {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'] = array_merge(
                        Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'], $mw
                    );
                } else {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'][] = $mw;
                }
                return $this;
            }
        };

    }

    public static function post($uri, $action)
    {
        $uri                        = trim($uri, '/');
        $uri                        = $uri === '' ? '/' : $uri;
        self::$routes['POST'][$uri] = ['action' => $action, 'middleware' => []];
        self::$current              = ['method' => 'POST', 'uri' => $uri];

        return new class {
            public function middleware($mw)
            {
                if (is_array($mw)) {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'] = array_merge(
                        Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'], $mw
                    );
                } else {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'][] = $mw;
                }
                return $this;
            }
        };
    }

    public static function put($uri, $action)
    {
        $uri                       = trim($uri, '/');
        $uri                       = $uri === '' ? '/' : $uri;
        self::$routes['PUT'][$uri] = ['action' => $action, 'middleware' => []];
        self::$current             = ['method' => 'PUT', 'uri' => $uri];

        return new class {
            public function middleware($mw)
            {
                if (is_array($mw)) {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'] = array_merge(
                        Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'], $mw
                    );
                } else {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'][] = $mw;
                }
                return $this;
            }
        };
    }

    public static function delete($uri, $action)
    {
        $uri                          = trim($uri, '/');
        $uri                          = $uri === '' ? '/' : $uri;
        self::$routes['DELETE'][$uri] = ['action' => $action, 'middleware' => []];
        self::$current                = ['method' => 'DELETE', 'uri' => $uri];

        return new class {
            public function middleware($mw)
            {
                if (is_array($mw)) {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'] = array_merge(
                        Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'], $mw
                    );
                } else {
                    Route::$routes[Route::$current['method']][Route::$current['uri']]['middleware'][] = $mw;
                }
                return $this;
            }
        };
    }

    public static function dispatch()
    {
        $uri    = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $uri    = $uri === '' ? '/' : $uri;
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset(self::$routes[$method][$uri])) {
            $config = self::$routes[$method][$uri];
            self::runMiddlewareAndController($config);
            return;
        }

        foreach (self::$routes[$method] as $routePath => $config) {
            $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $routePath);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                self::runMiddlewareAndController($config, $matches);
                return;
            }
        }

        http_response_code(404);
        echo "<h1>404 - Halaman Tidak Ditemukan</h1>";
        exit;
    }

    private static function runMiddlewareAndController($config, $params = [])
    {
        $next = function () use ($config, $params) {
            [$controller, $action] = $config['action'];
            $instance              = new $controller();

            return call_user_func_array([$instance, $action], $params);
        };

        if (! empty($config['middleware'])) {
            foreach (array_reverse($config['middleware']) as $mwName) {

                // Handle middleware with parameter
                if (strpos($mwName, ':') !== false) {
                    [$mwNameOnly, $mwParam] = explode(':', $mwName, 2);
                } else {
                    $mwNameOnly = $mwName;
                    $mwParam    = null;
                }

                $mwClass = "App\\Http\\Middleware\\" . $mwNameOnly;

                if (class_exists($mwClass)) {
                    $mwInstance = new $mwClass();
                    $previous   = $next;

                    $next = function () use ($mwInstance, $previous, $mwParam) {
                        return $mwInstance->handle($previous, $mwParam);
                    };
                }
            }
        }

        $next();
    }
}

$user = $_SESSION['user'] ?? null;

if ($user) {

    $db = db();

    $pengadaanPending = $db->query("
        SELECT COUNT(*)
        FROM pengadaan
        WHERE status = 'P'
    ")->fetchColumn();

    $stokMasuk = $db->query("
        SELECT COUNT(*)
        FROM penerimaan
        WHERE status = 'S'
        AND DATE(created_at) = CURDATE()
    ")->fetchColumn();

    $_SESSION['notifications'] = [
        'pengadaan_pending' => $pengadaanPending,
        'stok_masuk'        => $stokMasuk,
    ];
}
require_once __DIR__ . '/../routes/web.php';

Route::dispatch();
