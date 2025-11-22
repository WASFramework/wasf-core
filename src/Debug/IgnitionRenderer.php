<?php

namespace Wasf\Debug;

use Throwable;

class IgnitionRenderer
{
    public static function render(Throwable $exception)
    {
        http_response_code(500);

        // Ambil request WASF-mu
        $request = app()->request ?? null;

        // Ambil route jika ada
        $route = null;
        if ($request && method_exists($request, 'getMatchedRoute')) {
            $route = $request->getMatchedRoute();
        }

        // Variabel yang akan tersedia di semua partial (layout, header-server, trace-list, dst)
        $vars = [
            'exception' => $exception,
            'request'   => $request,
            'route'     => $route,
        ];

        extract($vars);

        // View utama ignition
        $view = __DIR__ . '/resources/ignition.php';

        include $view;
        exit;
    }
}
