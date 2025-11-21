<?php
namespace Wasf\Console\Commands;

class RouteCache extends Command
{
    public function signature(): string { return 'route:cache'; }
    public function description(): string { return 'Cache routes to file'; }

    public function handle(array $args): void
    {
        $routerFile = getcwd() . '/routes/web.php';

        if (!file_exists($routerFile)) {
            $this->error("routes/web.php not found.");
            return;
        }

        // Fresh router instance
        $router = new \Wasf\Routing\Router();
        require $routerFile;

        $collection = $router->getCollection();
        $routes = $collection->getRoutes();
        $named  = $collection->getNamedRoutes();

        // REMOVE closures before export
        foreach ($routes as $method => $list) {
            foreach ($list as $i => $r) {
                // if route handler is closure → remove it from cache
                if ($r['action']['uses'] instanceof \Closure) {
                    unset($routes[$method][$i]);
                    $this->warn("Skipped closure route: {$r['uri']}");
                }
            }
            // reindex array to avoid gaps
            $routes[$method] = array_values($routes[$method]);
        }

        // Make cache directory
        $cacheDir = getcwd() . '/bootstrap/cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $cacheFile = $cacheDir . '/routes.php';

        // Export only serializable data
        $cacheData = [
            'routes' => $routes,
            'named'  => $named,
        ];

        $export = '<?php return ' . var_export($cacheData, true) . ';';

        if (file_put_contents($cacheFile, $export) === false) {
            $this->error("Failed to write route cache file.");
            return;
        }

        $this->info("Routes cached to bootstrap/cache/routes.php");
    }
}
