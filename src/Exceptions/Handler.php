<?php

namespace Wasf\Exceptions;

use Throwable;
use Wasf\Debug\IgnitionRenderer;

class Handler
{
    public static function handleException(Throwable $e)
    {
        // Jika mode development → tampilkan tampilan error advanced
        if (env('APP_ENV') === 'development') {
            IgnitionRenderer::render($e);
            return;
        }

        // Default behavior (mode production)
        $code = 500;

        if ($e instanceof HttpException) {
            $code = $e->getStatusCode();
        }

        self::render($code, $e->getMessage());
    }

    public static function render($code, $message = null)
    {
        http_response_code($code);

        $view = base_path("resources/views/errors/{$code}.php");

        if (!file_exists($view)) {
            $view = base_path("resources/views/errors/500.php");
        }

        include $view;
        exit;
    }
}
