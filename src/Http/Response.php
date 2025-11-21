<?php
namespace Wasf\Http;

class Response
{
    protected string $content = '';
    protected int $status = 200;
    protected array $headers = [];

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /** NEW — needed by public/index.php */
    public function getBody(): string
    {
        return $this->content;
    }

    /** NEW — shortcut for redirects */
    public function redirect(string $url, int $status = 302): self
    {
        $this->setStatus($status);
        $this->header('Location', $url);
        return $this;
    }

    public function with(string $key, string $message): self
    {
        if (!isset($_SESSION)) session_start();

        $_SESSION['flash'][$key] = $message;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $k => $v) {
            header("{$k}: {$v}");
        }

        echo $this->content;
    }
}
