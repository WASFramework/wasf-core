<?php
namespace Wasf\Console;

class Kernel
{
    protected array $commands = [];

    public function __construct()
    {
        $this->commands = CommandLoader::discover();
    }

    /**
     * Dispatch CLI command.
     *
     * @param string $cmd
     * @param array $args
     */
    public function handle(string $cmd, array $args = []): void
    {
        if ($cmd === '' || $cmd === null) {
            $cmd = 'list';
        }

        if (!isset($this->commands[$cmd])) {
            echo "\033[31mUnknown command: {$cmd}\033[0m\n\n";
            $this->runList();
            return;
        }

        $class = $this->commands[$cmd];

        /** @var \Wasf\Console\Commands\Command $command */
        $command = new $class();
        $command->handle($args);
    }

    /**
     * Return all discovered commands.
     *
     * @return array signature => class
     */
    public function allCommands(): array
    {
        return $this->commands;
    }

    public function runList(): void
    {
        if (isset($this->commands['list'])) {
            $class = $this->commands['list'];
            $cmd = new $class();
            $cmd->handle([]);
        } else {
            echo "No commands available.\n";
        }
    }
}
