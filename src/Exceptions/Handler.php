<?php

namespace Wasf\Exceptions;

class Handler
{
    public static function handleException(\Throwable $e)
    {
        $code = 500;
        $message = $e->getMessage();

        if ($e instanceof HttpException) {
            $code = $e->getStatusCode();
        }

        self::render($code, $message);
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