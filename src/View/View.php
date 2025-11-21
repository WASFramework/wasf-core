<?php
namespace Wasf\View;

class View
{
    public static function make(string $view, array $data = []): string
    {
        // Jika file adalah Blade (.wasf.php)
        $root = dirname(__DIR__, 3);
        $path = $root . '/app/Views/' . str_replace('.', '/', $view);

        // Blade file exists?
        if (file_exists($path . '.wasf.php')) {
            return Blade::render($view, $data);
        }

        // PHP view file exists?
        if (file_exists($path . '.php')) {
            extract($data, EXTR_SKIP);
            ob_start();
            require $path . '.php';
            return ob_get_clean();
        }

        throw new \RuntimeException("View {$view} not found.");
    }
}
