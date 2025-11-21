<?php
namespace Wasf\Foundation;

class ExceptionHandler
{
    public function render(\Throwable $e)
    {
        $debug = getenv('APP_DEBUG') === 'true'
            || (defined('APP_DEBUG') && APP_DEBUG === true);

        http_response_code(500);

        if ($debug) {
            echo "<h1>Exception: " . htmlspecialchars($e->getMessage()) . "</h1>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            echo "<h1>Application Error</h1>";
            echo "<p>Sorry, something went wrong.</p>";
        }
    }

    public function handle(\Throwable $e)
    {
        // gunakan helper Laravel-style
        $log = storage_path('logs/error.log');

        @mkdir(dirname($log), 0777, true);

        $msg =
            date('Y-m-d H:i:s') . " " .
            $e->getMessage() . "\n" .
            $e->getTraceAsString() . "\n\n";

        file_put_contents($log, $msg, FILE_APPEND);

        $this->render($e);
    }
}
