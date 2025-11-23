<?php
// trace-list.php (Bootstrap 5 conversion)
// Full single-file replacement: keeps real data & logic from original
// Expects $exception to be an instance of Throwable/Exception.

if (!isset($exception) || !($exception instanceof \Throwable)) {
    echo '<section class="container">';
    echo '<div class="card mb-3"><div class="card-body"><strong>No exception object provided to trace-list.php</strong></div></div></section>';
    return;
}

// Helpers (kept unchanged)
function safeClassName($obj) {
    return is_object($obj) ? get_class($obj) : (string)$obj;
}

function shortPath($path) {
    if (!$path) return '';
    $cwd = getcwd();
    if ($cwd && strpos($path, $cwd) === 0) {
        return substr($path, strlen($cwd) + 1); // remove cwd + slash
    }
    return $path;
}

function frameTitle($frame) {
    $parts = [];
    if (!empty($frame['class'])) {
        $parts[] = $frame['class'];
    }
    if (!empty($frame['type'])) {
        $parts[] = $frame['type'];
    }
    if (!empty($frame['function'])) {
        $parts[] = $frame['function'].'(' . (isset($frame['args']) ? '' : '') . ')';
    }
    if (empty($parts) && !empty($frame['function'])) {
        return $frame['function'].'()';
    }
    return implode('', $parts) ?: ($frame['function'] ?? '');
}

function getCodeSnippetHtml($file, $line, $context = 6) {
    if (!is_readable($file)) {
        return '<div class="p-2 small font-monospace">[source not available]</div>';
    }

    $lines = @file($file, FILE_IGNORE_NEW_LINES);
    if (!$lines) {
        return '<div class="p-2 small font-monospace">[cannot read file]</div>';
    }

    $total  = count($lines);
    $start  = max(1, $line - $context);
    $end    = min($total, $line + $context);

    // Wrap the snippet like real <code> inside a Bootstrap card-like block
    $out  = '<pre class="small font-monospace lh-sm border rounded p-2 mb-0" ';
    $out .= 'style="white-space:pre;overflow-x:auto;"><code>';

    for ($i = $start; $i <= $end; $i++) {
        $content = htmlspecialchars($lines[$i - 1]);
        $lineNum = str_pad($i, 4, ' ', STR_PAD_LEFT);

        if ($i === $line) {
            // Highlighted error line (auto-adjusts with BS theme)
            $out .= 
            '<div class="d-flex rounded bg-danger bg-opacity-25">'
                . '<span class="text-danger-emphasis px-2" style="width:55px;">' . $lineNum . '</span>'
                . '<span class="px-2">' . $content . '</span>'
            . '</div>';
        } else {
            // Normal line
            $out .= 
            '<div class="d-flex">'
                . '<span class="text-secondary px-2" style="width:55px;">' . $lineNum . '</span>'
                . '<span class="px-2">' . $content . '</span>'
            . '</div>';
        }
    }

    $out .= '</code></pre>';

    return $out;
}

// Prepare data (kept unchanged)
$class = get_class($exception);
$file = $exception->getFile();
$line = $exception->getLine();
$message = $exception->getMessage();
$code = $exception->getCode();
$trace = $exception->getTrace();

$frameworkLabel = 'FRAMEWORK';
$frameworkVersion = defined('APP_VERSION') ? APP_VERSION : (defined('WASF_VERSION') ? WASF_VERSION : 'Unknown');
$phpVersion = phpversion();

// Group frames
$vendorFrames = [];
$appFrames = [];
foreach ($trace as $i => $frame) {
    $frameFile = $frame['file'] ?? null;
    $isVendor = false;
    if ($frameFile) {
        $normalized = str_replace('\\', '/', $frameFile);
        if (strpos($normalized, '/vendor/') !== false) {
            $isVendor = true;
        }
    }
    $entry = [
        'index' => $i,
        'file' => $frameFile,
        'short_file' => $frameFile ? shortPath($frameFile) : null,
        'line' => $frame['line'] ?? null,
        'call' => frameTitle($frame),
        'raw' => $frame,
    ];
    if ($isVendor) {
        $vendorFrames[] = $entry;
    } else {
        $appFrames[] = $entry;
    }
}

$vendorCount = count($vendorFrames);
$appCount = count($appFrames);
$totalFrames = $vendorCount + $appCount;

$requestUrl = (isset($_SERVER['HTTP_HOST']) ? ($_SERVER['REQUEST_SCHEME'] ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')) . '://' . $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] ?? '') : 'cli');

function renderTraceItem($entry, $highlightLine = null)
{
    // Batasi teks agar tidak berantakan
    $call = htmlspecialchars(
        mb_strimwidth($entry['call'] ?: ($entry['short_file'] ?: 'n/a'), 0, 120, "…")
    );

    $shortFile = $entry['short_file']
        ? htmlspecialchars(mb_strimwidth($entry['short_file'], 0, 100, "…"))
        : 'n/a';

    $fileAttr = $entry['file'] ? htmlspecialchars($entry['file']) : null;
    $line = $entry['line'] ?? null;

    ob_start();
    ?>
    <div class="d-flex gap-2 mb-1">

        <!-- Bullet / indicator -->
        <div class="flex-shrink-0 d-flex align-items-center">
            <div class="rounded-circle" style="width:10px; height:10px; background:#ff6b6b;"></div>
        </div>

        <!-- Content 2 baris -->
        <div class="flex-grow-1">

            <!-- Baris 1: Call -->
            <div class="small text-truncate" title="<?= $call ?>">
                <code><?= $call ?></code>
            </div>

            <!-- Baris 2: File Path -->
            <div class="small text-muted text-break d-block"
                title="<?= $fileAttr ? $fileAttr . ':' . ($line ?: '') : '' ?>">
                <?= $shortFile ?>
                <?= $line ? '<span class="text-muted">:' . $line . '</span>' : '' ?>
            </div>

        </div>

    </div>
    <?php
    return ob_get_clean();
}

?>
<section class="container">
    <div class="card mb-3">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                <i class="bi bi-exclamation-triangle-fill text-success"></i>
            </div>
            <h5 class="card-title mb-0">Exception trace</h5>
        </div>
    </div>

    <div class="mb-3">
        <!-- Vendor frames (Bootstrap collapse using Alpine for state parity) -->
        <div class="card mb-2">
            <div class="card-header d-flex align-items-center justify-content-between p-2">
                <div class="badge bg-outline-danger d-flex align-items-center gap-1"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#vendorFrames"
                        aria-expanded="false"
                        aria-controls="vendorFrames">
                    <?= $appCount ?> Stack Trace
                </div>

                <button class="btn btn-sm btn-secondary d-flex align-items-center gap-1"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#vendorFrames"
                        aria-expanded="false"
                        aria-controls="vendorFrames">
                    <i class="bi bi-chevron-expand toggle-icon"></i>
                </button>
            </div>
            <div id="vendorFrames" class="collapse">
                <div class="card-body p-2">
                    <?php foreach ($appFrames as $af): ?>
                        <div class="mb-2 p-2 border rounded">
                            <?= renderTraceItem($af) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- App frames (expanded by default) -->
        <div class="card mb-2">
            <div class="card-header d-flex align-items-center justify-content-between p-2">

                <h6 class="mb-0 ms-2">Application frames</h6>

                <!-- Toggle Button (with icon) -->
                <button class="btn btn-sm btn-secondary d-flex align-items-center gap-1"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#appFrames"
                        aria-expanded="true"
                        aria-controls="appFrames">
                    <!-- Icon (auto rotates using Bootstrap collapse classes) -->
                    <i class="bi bi-chevron-expand toggle-icon"></i>
                </button>
            </div>

            <div id="appFrames" class="collapse show">
                <div class="card-body p-2">
                    
                    <div class="mb-2 p-2 border rounded mb-3">
                        <div class="small text-muted mb-2">File</div>
                        <div class="small">
                            <?= htmlspecialchars(shortPath($file) ?: 'n/a') ?><?= $line ? ':' . $line : '' ?>
                        </div>
                    </div>

                    <div class="mt-3">
                        <?php
                        echo getCodeSnippetHtml($file, $line, 8);
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Queries / pagination block -->
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between p-2">
            <div class="d-flex align-items-center gap-3">
                <div class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1">
                    <i class="bi bi-database text-primary"></i>
                </div>
                <h6 class="mb-0">Queries</h6>
            </div>
            <div class="small text-muted" id="queriesInfo" style="display:none;">1-0 of 0</div>
        </div>
        <div class="card-body">
            <div class="p-3 text-center text-uppercase small text-muted border rounded">
                // No queries executed
            </div>

            <!-- Pagination (hidden until queries exist) -->
            <nav aria-label="Query pagination" style="display:none;" class="d-flex justify-content-center mt-3">
                <ul class="pagination mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">&laquo;</a></li>
                    <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1">&lsaquo;</a></li>
                    <!-- pages generated by Alpine in original, kept client-side in JS if needed -->
                    <li class="page-item disabled"><a class="page-link" href="#">&rsaquo;</a></li>
                    <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                </ul>
            </nav>
        </div>
    </div>

</section>
