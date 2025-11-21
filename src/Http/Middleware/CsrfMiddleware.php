<?php

namespace Wasf\Http\Middleware;

use Wasf\Support\Flash;

class CsrfMiddleware
{
    public function handle($passable, \Closure $next)
    {
        // --- PROCESS REQUEST FIRST ---
        $request = new \Wasf\Http\Request();

        // Hanya cek token jika request POST/PUT/PATCH/DELETE
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {

            $sessionToken = $_SESSION['_token'] ?? null;
            $inputToken   = $request->input('_token');

            if (!$sessionToken || !$inputToken || !hash_equals($sessionToken, $inputToken)) {
                http_response_code(419);
                die('CSRF Token Mismatch');
            }
        }

        // Lanjutkan request
        $response = $next($passable);

        // Jika response berupa string HTML → inject token secara otomatis
        if (is_string($response)) {
            $token = $_SESSION['_token'] ?? bin2hex(random_bytes(32));
            $_SESSION['_token'] = $token;

            // Inject ke semua <form ... method="post">
            $response = preg_replace_callback(
                '/<form\b[^>]*>/i',
                function ($matches) use ($token) {
                    $formTag = $matches[0];

                    // Skip jika method GET (default Form)
                    if (preg_match('/method=["\']?(get)["\']?/i', $formTag)) {
                        return $formTag;
                    }

                    return $formTag . "\n" .
                        '<input type="hidden" name="_token" value="' . htmlspecialchars($token, ENT_QUOTES) . '">';
                },
                $response
            );
        }

        return $response;
    }
}
