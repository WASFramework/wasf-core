<?php

use Wasf\Exceptions\HttpException;

/**
 * env()
 */
if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

/**
 * config()
 */
if (!function_exists('config')) {
    function config(string $key, $default = null)
    {
        return \Wasf\Support\Config::get($key, $default);
    }
}

/**
 * base_path() — LARAVEL STYLE
 * Mendeteksi root project, bukan public/
 */
if (!function_exists('base_path')) {
    function base_path(string $path = '')
    {
        // Jika framework punya instance $app → gunakan itu
        global $app;
        if (isset($app) && method_exists($app, 'basePath')) {
            return $app->basePath($path);
        }

        // Jika tidak ada, deteksi root secara otomatis
        $dir = getcwd();
        while ($dir !== dirname($dir)) {
            if (is_dir($dir . '/app') && is_dir($dir . '/config')) {
                return $path ? $dir . DIRECTORY_SEPARATOR . $path : $dir;
            }
            $dir = dirname($dir);
        }

        return $path ? getcwd() . DIRECTORY_SEPARATOR . $path : getcwd();
    }
}

/**
 * app_path()
 */
if (!function_exists('app_path')) {
    function app_path(string $path = '')
    {
        $base = base_path('app');
        return $path ? $base . DIRECTORY_SEPARATOR . $path : $base;
    }
}

/**
 * storage_path()
 */
if (!function_exists('storage_path')) {
    function storage_path(string $path = '')
    {
        $base = base_path('storage');
        return $path ? $base . DIRECTORY_SEPARATOR . $path : $base;
    }
}

/**
 * public_path()
 */
if (!function_exists('public_path')) {
    function public_path(string $path = '')
    {
        $base = base_path('public');
        return $path ? $base . DIRECTORY_SEPARATOR . $path : $base;
    }
}

/**
 * config_path()
 */
if (!function_exists('config_path')) {
    function config_path(string $path = '')
    {
        $base = base_path('config');
        return $path ? $base . DIRECTORY_SEPARATOR . $path : $base;
    }
}


/**
 * view()
 */
if (!function_exists('view')) {
    function view(string $v, array $d = [])
    {
        return \Wasf\View\Blade::render($v, $d);
    }
}


/**
 * url()
 */
if (!function_exists('url')) {

    function url($path = null)
    {
        return new class($path) {

            private $path;

            public function __construct($path) { $this->path = $path; }

            private function base()
            {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    ? 'https://' : 'http://';

                return $scheme . $_SERVER['HTTP_HOST'];
            }

            public function current()
            {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                    ? 'https://' : 'http://';

                return $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            }

            public function __toString()
            {
                if ($this->path === null) return $this->base();
                return $this->base() . '/' . ltrim($this->path, '/');
            }
        };
    }
}


/**
 * route()
 */
if (!function_exists('route')) {
    function route(string $name, array $params = []): ?string
    {
        if (isset($GLOBALS['WASF_ROUTE_COLLECTION'])) {
            return $GLOBALS['WASF_ROUTE_COLLECTION']->generate($name, $params);
        }
        return null;
    }
}


/**
 * auth()
 */
if (!function_exists('auth')) {
    function auth()
    {
        return \Wasf\Auth\AuthManager::instance();
    }
}


/**
 * redirect()
 */
if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302)
    {
        $response = new \Wasf\Http\Response();
        return $response->setStatus($status)->header('Location', $url);
    }
}


/**
 * flash_put, flash_get
 */
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


/**
 * old()
 */
if (!function_exists('old')) {
    function old($key, $default = null) {
        return $_SESSION['_old_inputs'][$key] ?? $default;
    }
}


/**
 * model_from_table()
 */
if (!function_exists('model_from_table')) {
    function model_from_table(string $table)
    {
        $className = ucfirst(rtrim($table, 's'));
        $paths = glob(base_path("Modules/*/Models/{$className}.php"));

        if (!$paths) return null;

        foreach ($paths as $path) {
            $module = basename(dirname(dirname($path)));
            return "Modules\\{$module}\\Models\\{$className}";
        }

        return null;
    }
}


/**
 * validator()
 */
if (!function_exists('validator')) {
    function validator(): Wasf\Validation\Validator
    {
        return new Wasf\Validation\Validator();
    }
}


/**
 * dd()
 */
if (!function_exists('dd')) {
    function dd($v) {
        var_dump($v);
        die;
    }
}


/**
 * abort()
 */
if (!function_exists('abort')) {
    function abort($code, $message = '')
    {
        throw new HttpException($code, $message);
    }
}


/**
 * now(), carbon()
 */
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


/**
 * __() translator
 */
if (!function_exists('__')) {
    function __($key)
    {
        $locale = Config::get('app.locale', 'en');
        $fallback = Config::get('app.fallback_locale', 'en');

        $path = base_path("lang/{$locale}/messages.php");
        $fallbackPath = base_path("lang/{$fallback}/messages.php");

        if (file_exists($path)) {
            $lines = include $path;
            if (isset($lines[$key])) return $lines[$key];
        }

        if (file_exists($fallbackPath)) {
            $lines = include $fallbackPath;
            if (isset($lines[$key])) return $lines[$key];
        }

        return $key;
    }
}
