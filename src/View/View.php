<?php
namespace Wasf\View;
class View
{
    public static function make(string $view, array $data = []): string
    {
        $base = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR;
        $file = $base . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';
        if (!file_exists($file)) {
            throw new \RuntimeException("View {$view} not found at {$file}");
        }
        extract($data, EXTR_SKIP);
        ob_start();
        require $file;
        return ob_get_clean();
    }
}
