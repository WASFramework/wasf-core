<?php

namespace Wasf\Console\Commands;

class MakeModule extends Command
{
    public function signature(): string { return 'make:module'; }
    public function description(): string { return 'Generate HMVC module (Controllers, Models, Views, routes.php)'; }

    public function handle(array $args): void
    {
        if (!isset($args[0])) {
            $this->error("Module name required. Example: php wasf make:module Blog");
            return;
        }

        $module = ucfirst($args[0]);
        $module       = ucfirst($args[0]);
        $moduleLower  = strtolower($args[0]);

        $appPath = getcwd() . "/";

        $basePath = "{$appPath}/Modules/{$module}";
        $controllerPath = "{$basePath}/Controllers";
        $modelPath = "{$basePath}/Models";
        $viewPath = "{$basePath}/Views";

        foreach ([$basePath, $controllerPath, $modelPath, $viewPath] as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0777, true);
        }

        // Controller
        $controllerFile = "{$controllerPath}/{$module}Controller.php";
        if (!file_exists($controllerFile)) {
            file_put_contents($controllerFile, $this->controllerStub($module));
            $this->info("Created Controller: {$module}Controller.php");
        }

        // Model
        $modelFile = "{$modelPath}/{$module}.php";
        if (!file_exists($modelFile)) {
            file_put_contents($modelFile, $this->modelStub($module));
            $this->info("Created Model: {$module}.php");
        }

        // View
        if (!file_exists("{$viewPath}/index.wasf.php")) {
            file_put_contents("{$viewPath}/index.wasf.php", "<h1>{$module} Module Loaded</h1>");
            $this->info("Created View: index.wasf.php");
        }

        // Routes
        if (!file_exists("{$basePath}/routes.php")) {
            file_put_contents("{$basePath}/routes.php", $this->routeStub($module, $moduleLower));
            $this->info("Created routes.php");
        }

        $this->info("Module {$module} berhasil dibuat!");
    }

    private function controllerStub($module): string
    {
        return <<<PHP
<?php

namespace Modules\\{$module}\Controllers;

use Wasf\Http\Request;
use Wasf\Http\Response;
use Wasf\View\Blade;

class {$module}Controller
{
    public function index(Request \$request, Response \$response)
    {
        return Blade::render('{$module}/index');
    }
}
PHP;
    }

    private function modelStub($module): string
    {
        return <<<PHP
<?php

namespace Modules\\{$module}\Models;

use Wasf\ORM\Model;

class {$module} extends Model
{
    protected static string \$table = '{$module}';
}
PHP;
    }

private function routeStub($module, $moduleLower): string
{
    return <<<PHP
<?php

// HMVC Module: {$module}

use Wasf\Routing\Router;

\$router->get('/{$moduleLower}', '{$module}Controller@index')->name('{$moduleLower}.index');
PHP;
}
}
