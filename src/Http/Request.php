<?php
namespace Wasf\Http;
class Request
{
    public array $get = [];
    public array $post = [];
    public array $server = [];
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }
    public static function capture(): self
    {
        return new self();
    }
    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }
    public function path(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }
    public function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public function only(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            if ($this->has($key)) {
                $data[$key] = $this->input($key);
            }
        }
        return $data;
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function validate(array $rules)
    {
        $v = validator();
        $errors = $v->validate($this->all(), $rules);

        if ($v->fails()) {
            // simpan error per-field (ErrorBag)
            $_SESSION['errors'] = $errors;

            // simpan old input
            $_SESSION['_old_inputs'] = $this->all();

            // redirect back
            header("Location: " . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        $data = $this->all();
        unset($data['_token']); // 🔥 hapus CSRF token
        return $data;
    }
}
