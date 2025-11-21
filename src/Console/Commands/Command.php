<?php
namespace Wasf\Console\Commands;

abstract class Command
{
    /**
     * Color map — bebas diganti sesuai kebutuhan.
     */
    protected array $colors = [
        'info'  => '32',     // green
        'warn'  => '33',     // yellow
        'error' => '31',     // red
        'line'  => null,     // no color
    ];

    /**
     * Render ANSI color.
     */
    protected function color(string $text, ?string $code): string
    {
        if (!$code) return $text;
        return "\033[{$code}m{$text}\033[0m";
    }

    /* ---------------------------------
     | Required command definitions
     |----------------------------------*/
    abstract public function signature(): string;
    abstract public function description(): string;
    abstract public function handle(array $args): void;

    /* ---------------------------------
     | Output helpers using color map
     |----------------------------------*/

    protected function line(string $msg = ''): void
    {
        $color = $this->colors['line'] ?? null;
        echo $this->color($msg, $color) . PHP_EOL;
    }

    protected function info(string $msg): void
    {
        echo $this->color($msg, $this->colors['info']) . PHP_EOL;
    }

    protected function warn(string $msg): void
    {
        echo $this->color($msg, $this->colors['warn']) . PHP_EOL;
    }

    protected function error(string $msg): void
    {
        echo $this->color($msg, $this->colors['error']) . PHP_EOL;
    }

    /* ---------------------------------
     | Direct color helpers
     |----------------------------------*/
    protected function c(string $text, string $code): string
    {
        return $this->color($text, $code);
    }
}
