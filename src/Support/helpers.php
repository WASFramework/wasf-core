<?php

use Wasf\Exceptions\HttpException;

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return \Wasf\Support\Config::get($key, $default);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = '')
    {
        global $app;
        return $app->basePath($path);
    }
}

if (!function_exists('view')) {
    function view(string $v, array $d = []) {
        return \Wasf\View\Blade::render($v, $d);
    }
}

if (!function_exists('url')) {

    function url($path = null)
    {
        // Build object-like behavior
        return new class($path) {

            private $path;

            public function __construct($path)
            {
                $this->path = $path;
            }

            // Return base URL of the site
            private function base()
            {
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                return $scheme . $_SERVER['HTTP_HOST'];
            }

            // url()->current()
            public function current()
            {
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
                return $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }

            // url('path')
            public function __toString()
            {
                if ($this->path === null) {
                    return $this->base();
                }

                $path = ltrim($this->path, '/');
                return $this->base() . '/' . $path;
            }
        };
    }
}

if (!function_exists('route')) {
    function route(string $name, array $params = []): ?string
    {
        if (isset($GLOBALS['WASF_ROUTE_COLLECTION'])) {
            return $GLOBALS['WASF_ROUTE_COLLECTION']->generate($name, $params);
        }
        return null;
    }
}

if (!function_exists('auth')) {
    function auth()
    {
        return \Wasf\Auth\AuthManager::instance();
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302)
    {
        $response = new \Wasf\Http\Response();
        $response->setStatus($status)->header('Location', $url);
        return $response;
    }
}

if (!function_exists('flash_put')) {
    function flash_put(string $key, $value): void
    {
        $_SESSION['flash'][$key] = $value;
    }
}

if (!function_exists('flash_get')) {
    function flash_get(string $key)
    {
        if (!empty($_SESSION['flash'][$key])) {
            $v = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $v;
        }
        return null;
    }
}

if (!function_exists('flash')) {
    function flash(string $key, string $message = null)
    {
        if ($message === null) {
            return \Wasf\Support\Flash::get($key);
        }

        return \Wasf\Support\Flash::set($key, $message);
    }
}

if (!function_exists('upload_file')) {
    function upload_file(array $file, string $destFolder)
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validasi MIME sederhana
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed)) {
            return null;
        }

        // Buat folder jika belum ada
        if (!is_dir($destFolder)) {
            mkdir($destFolder, 0777, true);
        }

        // Nama unik
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('pf_') . '.' . $ext;

        $fullPath = $destFolder . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $fullPath)) {
            return $filename;
        }

        return null;
    }
}

if (!class_exists('\\Wasf\\Support\\Flash')) {
    class_alias(DummyFlash::class, '\\Wasf\\Support\\Flash');
}

if (!class_exists('DummyFlash')) {
    class DummyFlash {
        public static function put($k,$v) { $_SESSION['flash'][$k] = $v; }
        public static function get($k) {
            if (!empty($_SESSION['flash'][$k])) { $v = $_SESSION['flash'][$k]; unset($_SESSION['flash'][$k]); return $v; }
            return null;
        }
        public static function has($k){ return !empty($_SESSION['flash'][$k]); }
    }
}

// --- old() helper ---
if (!function_exists('old')) {
    function old($key, $default = null) {
        return $_SESSION['_old_inputs'][$key] ?? $default;
    }
}

if (!function_exists('model_from_table')) {
    function model_from_table(string $table)
    {
        // contoh: users -> User
        $className = ucfirst(rtrim($table, 's'));

        // cari di Modules folder
        $paths = glob(base_path('Modules/*/Models/' . $className . '.php'));

        if (!$paths) return null;

        // Extract namespace dari path
        foreach ($paths as $path) {
            $module = basename(dirname(dirname($path))); // Modules/Auth/Models
            return "Modules\\{$module}\\Models\\{$className}";
        }

        return null;
    }
}

if (!function_exists('validator')) {
    function validator(): Wasf\Validation\Validator
    {
        return new Wasf\Validation\Validator();
    }
}

if (!function_exists('dd')) {
    function dd($v) {
        var_dump($v);
        die;
    }
}

if (!function_exists('abort')) {
    function abort($code, $message = '')
    {
        throw new HttpException($code, $message);
    }
}

if (!function_exists('now')) {
    function now()
    {
        return Carbon::now();
    }
}

if (!function_exists('carbon')) {
    function carbon($time = null)
    {
        return Carbon::parse($time);
    }
}


if (!function_exists('__')) {
    function __($key)
    {
        $locale = Config::get('app.locale', 'en');
        $fallback = Config::get('app.fallback_locale', 'en');

        $path = base_path("lang/{$locale}/messages.php");
        $fallbackPath = base_path("lang/{$fallback}/messages.php");

        // load primary locale
        if (file_exists($path)) {
            $lines = include $path;
            if (isset($lines[$key])) {
                return $lines[$key];
            }
        }

        // fallback
        if (file_exists($fallbackPath)) {
            $lines = include $fallbackPath;
            if (isset($lines[$key])) {
                return $lines[$key];
            }
        }

        return $key; // return raw key if not found
    }
}