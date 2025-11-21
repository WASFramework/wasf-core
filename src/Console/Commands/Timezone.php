<?php

namespace Wasf\Console\Commands;

use Wasf\Support\Config;

class Timezone extends Command
{
    public function signature(): string
    {
        return 'timezone';
    }

    public function description(): string
    {
        return 'Show current application timezone and current time.';
    }

    public function handle(array $args): void
    {
        $appTimezone = Config::get('app.timezone', 'UTC');
        $phpTimezone = date_default_timezone_get();
        $now = date('Y-m-d H:i:s');

        $this->info("WASF Timezone Status");
        $this->line("----------------------------------------");
        $this->line("Configured timezone   : <info>{$appTimezone}</info>");
        $this->line("PHP active timezone   : <info>{$phpTimezone}</info>");
        $this->line("Current time          : <info>{$now}</info>");

        if ($appTimezone !== $phpTimezone) {
            $this->warn("\nWarning: Timezone mismatch!");
        } else {
            $this->info("\nTimezone OK ✓");
        }
    }
}
