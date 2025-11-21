<?php
namespace Wasf\Console\Commands;

class RouteClear extends Command
{
    public function signature(): string { return 'route:clear'; }
    public function description(): string { return 'Clear route cache'; }

    public function handle(array $args): void
    {
        $cacheFile = getcwd() . '/bootstrap/cache/routes.php';

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            $this->info("Route cache cleared.");
        } else {
            $this->warn("No route cache file found.");
        }
    }
}
