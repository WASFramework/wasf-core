<?php
namespace Wasf\Console\Commands;

class ClearView extends Command
{
    public function signature(): string { return 'clear:view'; }
    public function description(): string { return 'Clear compiled blade views (storage/views)'; }

    public function handle(array $args): void
    {
        $dir = storage_path('views');

        if (!is_dir($dir)) {
            $this->warn("No compiled views directory: {$dir}");
            return;
        }

        $files = glob($dir . '/*.php');
        $count = 0;

        foreach ($files as $f) {
            @unlink($f);
            $count++;
        }

        $this->info("Cleared {$count} compiled view(s).");
    }
}
