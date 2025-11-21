<?php
namespace Wasf\Console\Commands;

class RouteList extends Command
{
    public function signature(): string { return 'route:list'; }
    public function description(): string { return 'Show all registered routes in a formatted and colored table'; }

    public function handle(array $args): void
    {
        $routerFile = getcwd() . '/routes/web.php';
        $router     = new \Wasf\Routing\Router();

        // Load cache or route file
        $cache = getcwd() . '/bootstrap/cache/routes.php';
        if (file_exists($cache)) {
            $router->getCollection()->loadFromDump(require $cache);
        } else {
            require $routerFile;
        }

        $collection = $router->getCollection();
        $routes = $collection->getRoutes();
        $named  = array_flip($collection->getNamedRoutes());

        $rows = [];

        foreach ($routes as $method => $list) {
            foreach ($list as $r) {
                $uri  = $r['uri'];
                $key  = $method . ':' . $uri;
                $name = $named[$key] ?? '-';

                $action = $r['action']['uses'];
                if ($action instanceof \Closure) {
                    $action = 'Closure';
                }

                $mw = implode(', ', $r['middleware'] ?? []);

                // Apply color rules (c() instead of green/yellow/etc)
                $methodColored = match ($method) {
                    'GET'  => $this->c('GET',  '32'),   // green
                    'POST' => $this->c('POST', '33'),   // yellow
                    default => $this->c($method, '1'),  // bold
                };

                $rows[] = [
                    'method'     => $methodColored,
                    'uri'        => $this->c($uri, '36'),                    // cyan
                    'name'       => $name === '-' ? '-' : $this->c($name, '34'), // blue
                    'action'     => $this->c($action, '36'),                 // cyan
                    'middleware' => $mw ? $this->c($mw, '35') : '',         // magenta
                ];
            }
        }

        if (empty($rows)) {
            $this->warn("No registered routes.");
            return;
        }

        $this->printTable($rows);
    }

    protected function printTable(array $rows): void
    {
        $headers = ['Method', 'URI', 'Name', 'Action', 'Middleware'];
        $headersColored = [
            $this->c('Method', '1'),       // bold
            $this->c('URI', '1'),
            $this->c('Name', '1'),
            $this->c('Action', '1'),
            $this->c('Middleware', '1'),
        ];

        // Determine column widths
        $widths = array_fill_keys($headers, 0);
        foreach ($headers as $h) {
            $widths[$h] = strlen($h);
        }

        foreach ($rows as $row) {
            foreach ($headers as $h) {
                $key = strtolower($h);
                // Strip ANSI codes for width calc
                $plain = preg_replace('/\e\[[0-9;]*m/', '', $row[$key]);
                $widths[$h] = max($widths[$h], strlen($plain));
            }
        }

        // Draw top border
        echo "\n+";
        foreach ($widths as $w) echo str_repeat('-', $w + 2) . '+';
        echo "\n";

        // Draw header
        $this->drawRow($headersColored, $widths);

        // Separator
        echo '+' . implode('+', array_map(fn($w) => str_repeat('-', $w + 2), $widths)) . "+\n";

        // Draw content
        foreach ($rows as $row) {
            $this->drawRow([
                $row['method'],
                $row['uri'],
                $row['name'],
                $row['action'],
                $row['middleware'],
            ], $widths);
        }

        // Bottom border
        echo '+' . implode('+', array_map(fn($w) => str_repeat('-', $w + 2), $widths)) . "+\n\n";
    }

    protected function drawRow(array $cols, array $widths): void
    {
        $out = '|';
        foreach (array_values($widths) as $i => $w) {
            $text = $cols[$i];
            $plain = preg_replace('/\e\[[0-9;]*m/', '', $text);
            $pad = $w - strlen($plain);
            $out .= ' ' . $text . str_repeat(' ', $pad) . ' |';
        }
        echo $out . "\n";
    }
}
