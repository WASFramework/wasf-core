<?php
namespace Wasf\Console\Commands;

use Wasf\Console\CommandLoader;
use Wasf\Console\Kernel;

class ListCommand extends Command
{
    public function signature(): string { return 'list'; }
    public function description(): string { return 'Show available commands'; }

    public function handle(array $args): void
    {
        $this->info("\nWASF CLI Commands:\n");

        // Use kernel discovery to preserve order
        $kernel = new Kernel();
        $commands = $kernel->allCommands();

        foreach ($commands as $sig => $class) {
            try {
                $c = new $class();
                $desc = $c->description();
            } catch (\Throwable $e) {
                $desc = '';
            }
            echo "  \033[32m{$sig}\033[0m  -  {$desc}\n";
        }

        $this->line("");
        $this->line("Usage: php wasf <command> [arguments]");
        $this->line("");
    }
}
