<?php
namespace Wasf\Console;

class CommandLoader
{
    /**
     * Discover command classes in Commands folder.
     * Returns map: signature => fully-qualified class name
     */
    public static function discover(): array
    {
        $dir = __DIR__ . '/Commands';
        $commands = [];

        foreach (glob($dir . '/*.php') as $file) {
            $class = "Wasf\\Console\\Commands\\" . basename($file, '.php');

            // ensure class can be autoloaded
            if (!class_exists($class)) {
                // try require file fallback (if not autoloaded)
                @include_once $file;
                if (!class_exists($class)) continue;
            }

            // instantiate and read signature if it extends base Command
            try {
                $ref = new \ReflectionClass($class);
                if ($ref->isInstantiable() && $ref->isSubclassOf(\Wasf\Console\Commands\Command::class)) {
                    /** @var \Wasf\Console\Commands\Command $obj */
                    $obj = $ref->newInstance();
                    $sig = $obj->signature();
                    $commands[$sig] = $class;
                }
            } catch (\Throwable $e) {
                // ignore faulty command files
                continue;
            }
        }

        // ensure a 'list' command exists (alias)
        if (!isset($commands['list']) && class_exists(\Wasf\Console\Commands\ListCommand::class)) {
            $commands['list'] = \Wasf\Console\Commands\ListCommand::class;
        }

        return $commands;
    }
}
