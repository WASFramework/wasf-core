<?php

// =====================================
// 1. Basic Exception Info
// =====================================
$exceptionClass   = get_class($exception);
$exceptionMessage = $exception->getMessage();
$filePath         = $exception->getFile();
$fileLine         = $exception->getLine();
$phpVersion       = PHP_VERSION;

// =====================================
// 2. Detect WASF Version
// =====================================
$wasfVersion = 'dev';

$lockFile = base_path('composer.lock');
if (file_exists($lockFile)) {
    $lock = json_decode(file_get_contents($lockFile), true);

    if (isset($lock['packages'])) {
        foreach ($lock['packages'] as $pkg) {
            if ($pkg['name'] === 'wasframework/wasf-core') {
                $wasfVersion = $pkg['version'];
                break;
            }
        }
    }
}

if (!$wasfVersion) {
    $wasfVersion = 'dev';
}

// =====================================
// 3. Status Code
// =====================================
$statusCode = http_response_code() ?: 500;

// =====================================
// 4. Request URL
// =====================================
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$uri    = $_SERVER['REQUEST_URI'] ?? '/';

$requestFullUrl = "{$scheme}://{$host}{$uri}";
?>

<section class="container">

    <!-- Exception Summary Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <!-- Title -->
            <h1 class="fs-3 fw-bold text-light mb-1">
                <?= htmlspecialchars($exceptionClass) ?>
            </h1>

            <div class="text-muted small mb-3">
                <?= htmlspecialchars($filePath) ?>:<?= htmlspecialchars($fileLine) ?>
            </div>

            <p class="fs-5 text-secondary mb-4">
                <?= htmlspecialchars($exceptionMessage) ?>
            </p>

            <!-- Version Badges -->
            <div class="d-flex flex-wrap gap-2 mb-4">

                <!-- WASF Ver -->
                <span class="badge bg-outline-primary">
                    WASF <?= htmlspecialchars($wasfVersion) ?>
                </span>

                <!-- PHP Ver -->
                <span class="badge bg-outline-success">
                    PHP <?= htmlspecialchars($phpVersion) ?>
                </span>

                <!-- UNHANDLED -->
                <span class="badge bg-outline-danger d-flex align-items-center gap-1">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    UNHANDLED
                </span>

                <!-- STATUS CODE -->
                <span class="badge bg-danger">
                    CODE <?= $statusCode ?>
                </span>
            </div>

            <!-- Request Info -->
            <div class="card border shadow-sm p-2">
                <div class="d-flex align-items-center gap-3 w-100">

                    <!-- Status Code -->
                    <span class="badge bg-danger">
                        <?= $statusCode ?>
                    </span>

                    <!-- Method -->
                    <span class="badge bg-outline-primary">
                        <?= htmlspecialchars($requestMethod) ?>
                    </span>

                    <!-- URL -->
                    <div class="flex-grow-1 small text-truncate">
                        <?= htmlspecialchars($requestFullUrl) ?>
                    </div>

                    <!-- Copy Button -->
                    <button 
                        class="btn btn-sm btn-secondary d-flex align-items-center justify-content-center copy-btn"
                        data-copy="<?= htmlspecialchars($requestFullUrl ?? '') ?>"
                        style="width: 32px; height: 32px;"
                    >
                        <i class="bi bi-copy copy-icon"></i>
                    </button>
                </div>

            </div>

        </div>
    </div>

</section>
