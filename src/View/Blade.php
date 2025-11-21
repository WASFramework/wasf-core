<?php
namespace Wasf\View;

class Blade
{
    protected static array $sections = [];
    protected static ?string $currentSection = null;
    protected static ?string $parentView = null;
    protected static string $cachePath;

    protected static function getAppRoot(): string
    {
        $dir = getcwd();
        while ($dir !== dirname($dir)) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . 'app')) return $dir;
            $dir = dirname($dir);
        }
        return getcwd();
    }

    public static function cachePath(): string
    {
        if (!isset(self::$cachePath)) {
            $root = base_path();
            self::$cachePath = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'views';
            if (!is_dir(self::$cachePath)) mkdir(self::$cachePath, 0777, true);
        }
        return self::$cachePath;
    }

    protected static function viewFile(string $view): string
    {
        $root = base_path();
        $viewPath = str_replace('.', '/', $view);

        $candidates = [];

        // ------------------------------------------------------------------
        // 1. MVC VIEW (app/Views)
        // ------------------------------------------------------------------
        $candidates[] = "{$root}/app/Views/{$viewPath}.wasf.php";
        $candidates[] = "{$root}/app/Views/{$viewPath}.php";

        // ------------------------------------------------------------------
        // 2. HMVC VIEW (Modules/<Module>/Views)
        //     format: Blog/index
        // ------------------------------------------------------------------
        if (str_contains($view, '/')) {

            [$module, $file] = explode('/', $view, 2);

            $candidates[] = "{$root}/Modules/{$module}/Views/{$file}.wasf.php";
            $candidates[] = "{$root}/Modules/{$module}/Views/{$file}.php";
        }

        // ------------------------------------------------------------------
        // 3. FIND EXISTING VIEW
        // ------------------------------------------------------------------
        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // ------------------------------------------------------------------
        // 4. ERROR — INCLUDE DEBUG PATHS
        // ------------------------------------------------------------------
        $list = implode("\n", $candidates);
        throw new \RuntimeException("View '{$view}' not found.\nChecked:\n{$list}");
    }

    protected static function cacheFile(string $view): string
    {
        return self::cachePath() . DIRECTORY_SEPARATOR . md5($view) . '.php';
    }

    /* layout API */
    public static function extend(string $layout) { self::$parentView = $layout; }
    public static function startSection(string $name) { self::$currentSection = $name; ob_start(); }
    public static function endSection() {
        $c = ob_get_clean();
        if ($c === false) $c = '';
        self::$sections[self::$currentSection ?? 'content'] = $c;
        self::$currentSection = null;
    }
    public static function section(string $name, string $content) { self::$sections[$name] = $content; }
    public static function yield(string $name) {
        return array_key_exists($name, self::$sections)
            ? self::$sections[$name]
            : null;
    }
    public static function render(string $view, array $data = []): string
    {
        self::$sections = [];
        self::$currentSection = null;
        self::$parentView = null;

        $out = self::renderView($view, $data);

        while (self::$parentView !== null) {
            $parent = self::$parentView;
            self::$parentView = null;
            $out = self::renderView($parent, $data);
        }

        // 🔥 FIX: hapus errors setelah render
        unset($_SESSION['errors'], $_SESSION['_old_inputs']);

        return $out;
    }

    protected static function renderView(string $view, array $data): string
    {
        $file = self::viewFile($view);
        if (!file_exists($file)) throw new \RuntimeException("View {$view} not found at {$file}");

        $src = file_get_contents($file);
        $compiled = self::compile($src);

        $cache = self::cacheFile($view);
        file_put_contents($cache, $compiled);

        extract($data, EXTR_SKIP);
        ob_start();
        require $cache;
        return ob_get_clean();
    }

    /** safe preg_replace wrapper */
    protected static function safe_replace($pattern, $replacement, string $subject): string
    {
        $res = @preg_replace($pattern, $replacement, $subject);
        return $res === null ? $subject : $res;
    }

    /**
     * Find matching closing parenthesis position given starting '(' index.
     * Returns index of matching ')' or -1 if not found.
     */
    protected static function findMatchingParen(string $s, int $startPos): int
    {
        $len = strlen($s);
        $depth = 0;
        for ($i = $startPos; $i < $len; $i++) {
            $ch = $s[$i];
            if ($ch === '(') $depth++;
            elseif ($ch === ')') {
                $depth--;
                if ($depth === 0) return $i;
            }
        }
        return -1;
    }

    /**
     * Replace directives that have parentheses safely by scanning the string
     * $keyword like '@if', '@elseif', '@foreach', '@for', '@while'
     * $phpStart e.g. '<?php if ('
     * $phpEnd e.g. '): ?>'
     */
    protected static function replaceParenedDirectives(string $c, string $keyword, string $phpStart, string $phpEnd): string
    {
        $pos = 0;
        while (($idx = mb_strpos($c, $keyword, $pos)) !== false) {
            // find '(' after keyword
            $after = $idx + mb_strlen($keyword);
            // skip whitespace
            $l = strlen($c);
            $p = $after;
            while ($p < $l && preg_match('/\s/u', $c[$p])) $p++;
            if ($p >= $l || $c[$p] !== '(') {
                // not a directive call, skip
                $pos = $after;
                continue;
            }
            $openPos = $p;
            $closePos = self::findMatchingParen($c, $openPos);
            if ($closePos === -1) {
                // no match - skip to avoid corrupting
                $pos = $after;
                continue;
            }
            // extract content inside parentheses (without outer parens)
            $inner = substr($c, $openPos + 1, $closePos - $openPos - 1);
            // build replacement
            $before = substr($c, 0, $idx);
            $afterStr = substr($c, $closePos + 1);
            $replacement = $phpStart . $inner . $phpEnd;
            $c = $before . $replacement . $afterStr;
            // move pos after replacement to avoid infinite loop
            $pos = $idx + strlen($replacement);
        }
        return $c;
    }

    /**
     * Compile source to PHP
     */
    public static function compile(string $src): string
    {
        $c = $src;

        // @extends('layout')
        $c = self::safe_replace('/@extends\(\s*[\'"](.+?)[\'"]\s*\)/', "<?php \\Wasf\\View\\Blade::extend('$1'); ?>", $c);

        // @section('name', 'value')
        $c = self::safe_replace('/@section\(\s*[\'"](.+?)[\'"]\s*,\s*[\'"](.+?)[\'"]\s*\)/', "<?php \\Wasf\\View\\Blade::section('$1', '$2'); ?>", $c);

        // @section('name')
        $c = self::safe_replace('/@section\(\s*[\'"](.+?)[\'"]\s*\)/', "<?php \\Wasf\\View\\Blade::startSection('$1'); ?>", $c);

        // @endsection
        $c = self::safe_replace('/@endsection\b/', "<?php \\Wasf\\View\\Blade::endSection(); ?>", $c);

        // @yield('key', default)
        $c = preg_replace_callback(
            '/@yield\(\s*[\'"](.+?)[\'"]\s*,\s*((?:[^()]|\([^()]*\))+)\s*\)/',
            function ($m) {
                $section = $m[1];
                $default = $m[2]; // aman, tidak terpotong
                return "<?php echo \\Wasf\\View\\Blade::yield('{$section}') ?: {$default}; ?>";
            },
            $c
        );

        // @yield('name')
        $c = self::safe_replace('/@yield\(\s*[\'"](.+?)[\'"]\s*\)/', "<?php echo \\Wasf\\View\\Blade::yield('$1'); ?>", $c);

        // @include('view')
        $c = self::safe_replace('/@include\(\s*[\'"](.+?)[\'"]\s*\)/', "<?php echo \\Wasf\\View\\Blade::render('$1'); ?>", $c);

        // Raw echo {!! $var !!}
        $c = self::safe_replace('/\{\!\!\s*(.+?)\s*\!\!\}/s', '<?php echo $1; ?>', $c);

        // Escaped echo {{ $var }}
        $c = self::safe_replace(
            '/\{\{\s*(.+?)\s*\}\}/s',
            '<?php echo htmlspecialchars((string)($1 ?? ""), ENT_QUOTES, "UTF-8"); ?>',
            $c
        );

        // CONTROL STRUCTURES — use deterministic balanced-parenthesis replacement

        // @if(...)
        $c = self::replaceParenedDirectives($c, '@if', '<?php if (', '): ?>');

        // @elseif(...)
        $c = self::replaceParenedDirectives($c, '@elseif', '<?php elseif (', '): ?>');

        // @else
        $c = self::safe_replace('/@else\b/', '<?php else: ?>', $c);

        // @endif
        $c = self::safe_replace('/@endif\b/', '<?php endif; ?>', $c);

        // @foreach(...)
        $c = self::replaceParenedDirectives($c, '@foreach', '<?php foreach (', '): ?>');

        // @endforeach
        $c = self::safe_replace('/@endforeach\b/', '<?php endforeach; ?>', $c);

        // @for(...)
        $c = self::replaceParenedDirectives($c, '@for', '<?php for (', '): ?>');
        $c = self::safe_replace('/@endfor\b/', '<?php endfor; ?>', $c);

        // @while(...)
        $c = self::replaceParenedDirectives($c, '@while', '<?php while (', '): ?>');
        $c = self::safe_replace('/@endwhile\b/', '<?php endwhile; ?>', $c);

        // @flash('success')
        $c = self::safe_replace(
            '/@flash\(\s*[\'"](.+?)[\'"]\s*\)/',
            '<?php if ($__msg = \\Wasf\\Support\\Flash::get("$1")): ?><div class="alert alert-$1"><?php echo htmlspecialchars($__msg); ?></div><?php endif; ?>',
            $c
        );

        $c = self::safe_replace(
            '/@csrf\b/',
            '<?php echo \'<input type="hidden" name="_token" value="'.($_SESSION["_token"] ?? "").'">\'; ?>',
            $c
        );

        // @auth
        $c = self::safe_replace('/@auth\b/', '<?php if (auth()->check()): ?>', $c);

        // @endauth
        $c = self::safe_replace('/@endauth\b/', '<?php endif; ?>', $c);

        // @guest
        $c = self::safe_replace('/@guest\b/', '<?php if (!auth()->check()): ?>', $c);

        // @endguest
        $c = self::safe_replace('/@endguest\b/', '<?php endif; ?>', $c);

        $c = preg_replace_callback('/@error\([\'"](.*?)[\'"]\)/', function ($m) {
            $field = $m[1];
            return "<?php if(!empty(\$_SESSION['errors']['{$field}'])): \$message = \$_SESSION['errors']['{$field}'][0]; ?>";
        }, $c);

        $c = str_replace('@enderror', '<?php endif; ?>', $c);

        return $c;
    }
}
