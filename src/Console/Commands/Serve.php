<?php
namespace Wasf\Console\Commands;

class Serve extends Command
{
    public function signature(): string
    {
        return 'serve';
    }

    public function description(): string
    {
        return 'Run PHP development server';
    }

    public function handle(array $args): void
{
    $host   = $args[0] ?? 'localhost:8000';
    $public = getcwd() . '/public';

    if (!is_dir($public)) {
        $this->error("Public directory not found: {$public}");
        return;
    }

    // Title warna cyan bold
    $this->line($this->c("Starting WASF Development Server", "1;36"));

    // Label warna kuning, value warna hijau
    $this->line(
        $this->c("Host: ", "33") . 
        $this->c("http://{$host}", "32")
    );

    $this->line(
        $this->c("Root: ", "33") .
        $this->c($public, "32")
    );

    $this->line(""); // kosong

    $cmd = "php -S {$host} -t {$public}";
    passthru($cmd);
}

}
