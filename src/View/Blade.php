<?php

namespace Wasf\View;

class Blade
{
    /* ----------------------------------------------------
     |  Properties
     |-----------------------------------------------------*/
    protected static array   $sections       = [];
    protected static ?string $currentSection = null;
    protected static ?string $parentView     = null;
    protected static string  $cachePath;


    /* ----------------------------------------------------
     |  Path Helpers
     |-----------------------------------------------------*/
    protected static function getAppRoot(): string
    {
        $dir = getcwd();

        while ($dir !== dirname($dir)) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . 'app')) {
                return $dir;
            }
            $dir = dirname($dir);
        }

        return getcwd();
    }

    public static function cachePath(): string
    {
        if (!isset(self::$cachePath)) {
            $root = base_path();
            $path = $root . '/storage/views';

            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }

            self::$cachePath = $path;
        }

        return self::$cachePath;
    }

    protected static function viewFile(string $view): string
    {
        $root = base_path();
        $viewPath = str_replace('.', '/', $view);

        $candidates = [
            "{$root}/app/Views/{$viewPath}.wasf.php",
            "{$root}/app/Views/{$viewPath}.php",
        ];

        // HMVC
        if (str_contains($view, '/')) {
            [$module, $file] = explode('/', $view, 2);

            $candidates[] = "{$root}/Modules/{$module}/Views/{$file}.wasf.php";
            $candidates[] = "{$root}/Modules/{$module}/Views/{$file}.php";
        }

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \RuntimeException(
            "View '{$view}' not found.\nChecked:\n" . implode("\n", $candidates)
        );
    }

    protected static function cacheFile(string $view): string
    {
        return self::cachePath() . '/' . md5($view) . '.php';
    }


    /* ----------------------------------------------------
     |  Section / Layout API
     |-----------------------------------------------------*/
    public static function extend(string $layout)
    {
        self::$parentView = $layout;
    }

    public static function startSection(string $name)
    {
        self::$currentSection = $name;
        ob_start();
    }

    public static function endSection()
    {
        $content = ob_get_clean() ?: '';
        self::$sections[self::$currentSection ?? 'content'] = $content;
        self::$currentSection = null;
    }

    public static function section(string $name, string $content)
    {
        self::$sections[$name] = $content;
    }

    public static function yield(string $name)
    {
        return self::$sections[$name] ?? null;
    }


    /* ----------------------------------------------------
     |  Rendering
     |-----------------------------------------------------*/
    public static function render(string $view, array $data = []): string
    {
        self::$sections       = [];
        self::$currentSection = null;
        self::$parentView     = null;

        $output = self::renderView($view, $data);

        // Handle extends (parent layouts)
        while (self::$parentView !== null) {
            $parent = self::$parentView;
            self::$parentView = null;
            $output = self::renderView($parent, $data);
        }

        // auto clean flash errors
        unset($_SESSION['errors'], $_SESSION['_old_inputs']);

        return $output;
    }

    protected static function renderView(string $view, array $data): string
    {
        $file = self::viewFile($view);
        if (!file_exists($file)) {
            throw new \RuntimeException("View {$view} not found at {$file}");
        }

        $compiled = self::compile(file_get_contents($file));
        $cache = self::cacheFile($view);

        file_put_contents($cache, $compiled);

        extract($data, EXTR_SKIP);

        ob_start();
        require $cache;
        return ob_get_clean();
    }


    /* ----------------------------------------------------
     |  Compiler Utilities
     |-----------------------------------------------------*/
    protected static function safe_replace($pattern, $replacement, string $subject): string
    {
        $result = @preg_replace($pattern, $replacement, $subject);
        return $result === null ? $subject : $result;
    }

    protected static function findMatchingParen(string $str, int $start): int
    {
        $depth = 0;
        $len = strlen($str);

        for ($i = $start; $i < $len; $i++) {
            $c = $str[$i];

            if ($c === '(') $depth++;
            elseif ($c === ')') {
                $depth--;
                if ($depth === 0) return $i;
            }
        }

        return -1;
    }

    protected static function replaceParenedDirectives(
        string $content,
        string $keyword,
        string $phpStart,
        string $phpEnd
    ): string {
        $pos = 0;

        while (($idx = mb_strpos($content, $keyword, $pos)) !== false) {
            $after = $idx + mb_strlen($keyword);
            $len = strlen($content);

            // skip whitespace
            $p = $after;
            while ($p < $len && preg_match('/\s/u', $content[$p])) {
                $p++;
            }

            if ($p >= $len || $content[$p] !== '(') {
                $pos = $after;
                continue;
            }

            $open = $p;
            $close = self::findMatchingParen($content, $open);

            if ($close === -1) {
                $pos = $after;
                continue;
            }

            $inner = substr($content, $open + 1, $close - $open - 1);

            $before = substr($content, 0, $idx);
            $afterStr = substr($content, $close + 1);
            $replace = $phpStart . $inner . $phpEnd;

            $content = $before . $replace . $afterStr;
            $pos = $idx + strlen($replace);
        }

        return $content;
    }


    /* ----------------------------------------------------
     |  Blade Compiler
     |-----------------------------------------------------*/
    public static function compile(string $src): string
    {
        $c = $src;

        /* ---------------- Layouts ---------------- */
        $c = self::safe_replace(
            '/@extends\(\s*[\'"](.+?)[\'"]\s*\)/',
            "<?php \\Wasf\\View\\Blade::extend('$1'); ?>",
            $c
        );

        $c = self::safe_replace(
            '/@section\(\s*[\'"](.+?)[\'"]\s*,\s*[\'"](.+?)[\'"]\s*\)/',
            "<?php \\Wasf\\View\\Blade::section('$1', '$2'); ?>",
            $c
        );

        $c = self::safe_replace(
            '/@section\(\s*[\'"](.+?)[\'"]\s*\)/',
            "<?php \\Wasf\\View\\Blade::startSection('$1'); ?>",
            $c
        );

        $c = self::safe_replace('/@endsection\b/', "<?php \\Wasf\\View\\Blade::endSection(); ?>", $c);

        /* ---------------- Output ---------------- */
        $c = preg_replace_callback(
            '/@yield\(\s*[\'"](.+?)[\'"]\s*,\s*((?:[^()]|\([^()]*\))+)\s*\)/',
            fn($m) => "<?php echo \\Wasf\\View\\Blade::yield('{$m[1]}') ?: {$m[2]}; ?>",
            $c
        );

        $c = self::safe_replace(
            '/@yield\(\s*[\'"](.+?)[\'"]\s*\)/',
            "<?php echo \\Wasf\\View\\Blade::yield('$1'); ?>",
            $c
        );

        $c = self::safe_replace(
            '/@include\(\s*[\'"](.+?)[\'"]\s*\)/',
            "<?php echo \\Wasf\\View\\Blade::render('$1'); ?>",
            $c
        );

        // raw
        $c = self::safe_replace('/\{\!\!\s*(.+?)\s*\!\!\}/s', '<?php echo $1; ?>', $c);

        // escaped
        $c = self::safe_replace(
            '/\{\{\s*(.+?)\s*\}\}/s',
            '<?php echo htmlspecialchars((string)($1 ?? ""), ENT_QUOTES, "UTF-8"); ?>',
            $c
        );

        /* ---------------- Control Structures ---------------- */
        $c = self::replaceParenedDirectives($c, '@if',      '<?php if (',      '): ?>');
        $c = self::replaceParenedDirectives($c, '@elseif',  '<?php elseif (',  '): ?>');
        $c = self::safe_replace('/@else\b/',  '<?php else: ?>', $c);
        $c = self::safe_replace('/@endif\b/', '<?php endif; ?>', $c);

        $c = self::replaceParenedDirectives($c, '@foreach', '<?php foreach (', '): ?>');
        $c = self::safe_replace('/@endforeach\b/', '<?php endforeach; ?>', $c);

        $c = self::replaceParenedDirectives($c, '@for',     '<?php for (',     '): ?>');
        $c = self::safe_replace('/@endfor\b/', '<?php endfor; ?>', $c);

        $c = self::replaceParenedDirectives($c, '@while',   '<?php while (',   '): ?>');
        $c = self::safe_replace('/@endwhile\b/', '<?php endwhile; ?>', $c);

        /* ---------------- Auth / CSRF / Errors ---------------- */
        $c = self::safe_replace(
            '/@flash\(\s*[\'"](.+?)[\'"]\s*\)/',
            '<?php if($__msg = \\Wasf\\Support\\Flash::get("$1")): ?>'
                . '<?php echo \\Wasf\\View\\Blade::renderView("components.flash", ["type"=>"$1","message"=>$__msg]); ?>'
            . '<?php endif; ?>',
            $c
        );

        $c = self::safe_replace('/@csrf\b/', '<?php echo \'<input type="hidden" name="_token" value="'.($_SESSION["_token"] ?? "").'">\'; ?>', $c);

        $c = self::safe_replace('/@auth\b/', '<?php if (auth()->check()): ?>', $c);
        $c = self::safe_replace('/@endauth\b/', '<?php endif; ?>', $c);

        $c = self::safe_replace('/@guest\b/', '<?php if (!auth()->check()): ?>', $c);
        $c = self::safe_replace('/@endguest\b/', '<?php endif; ?>', $c);

        $c = preg_replace_callback(
            '/@error\([\'"](.*?)[\'"]\)/',
            fn($m) => "<?php if(!empty(\$_SESSION['errors']['{$m[1]}'])): \$message=\$_SESSION['errors']['{$m[1]}'][0]; ?>",
            $c
        );

        $c = str_replace('@enderror', '<?php endif; ?>', $c);

        return $c;
    }
}
