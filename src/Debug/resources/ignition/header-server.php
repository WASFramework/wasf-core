<?php

// ===============================
//  HEADERS (dari $_SERVER)
// ===============================

$headers = [];
foreach ($_SERVER as $key => $value) {
    if (str_starts_with($key, 'HTTP_')) {
        $name = strtolower(str_replace('_', '-', substr($key, 5)));
        $headers[$name] = $value;
    }
}

// Fallback jika tidak ada header
if (!$headers) {
    $headers = ['no-headers' => '—'];
}

// ===============================
//  REQUEST BODY
// ===============================

$rawBody = file_get_contents('php://input');
$bodyText = trim($rawBody) !== '' ? $rawBody : null;

// ===============================
//  ROUTING (WASF)
// ===============================

// Default fallback
$routeController = 'Unknown';
$routeMiddleware = '-';
$routeParams = [];

// WASF Router
$routeController = 'Unknown';
$routeMiddleware = '-';
$routeParams = [];

if (isset($route) && is_array($route)) {
    $routeController = $route['action'] ?? 'Closure';
    $routeMiddleware = isset($route['middleware'])
        ? implode(', ', (array) $route['middleware'])
        : '-';

    $routeParams = $route['params'] ?? [];
}

// Jika renderer mengirim $request, gunakan.
// Jika tidak ada, skip aman.
if (isset($request) && is_object($request)) {
    $route = $request->getMatchedRoute();
    if ($route) {
        $routeController = $route['action'] ?? 'Closure';
        $routeMiddleware = isset($route['middleware'])
            ? implode(', ', (array)$route['middleware'])
            : '-';
        $routeParams = $route['params'] ?? [];
    }
}

?>
<section class="w-full max-w-7xl mx-auto p-4 sm:p-14 border-x border-dashed border-neutral-300 dark:border-white/[9%] flex flex-col gap-12">

    <!-- =============================== -->
    <!-- HEADERS -->
    <!-- =============================== -->
    <div class="flex flex-col gap-3">
        <h2 class="text-lg font-semibold text-neutral-900 dark:text-white">Headers</h2>
        <div class="flex flex-col">

            <?php foreach ($headers as $name => $value): ?>
            <div class="flex max-w-full items-baseline gap-2 h-10 text-sm font-mono">
                <div class="uppercase text-neutral-500 dark:text-neutral-400 shrink-0"><?= htmlspecialchars($name) ?></div>
                <div class="min-w-6 grow h-3 border-b-2 border-dotted border-neutral-300 dark:border-white/20"></div>
                <div class="truncate text-neutral-900 dark:text-white">
                    <span data-tippy-content="<?= htmlspecialchars($value) ?>">
                        <?= htmlspecialchars($value) ?>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>

        </div>
    </div>


    <!-- =============================== -->
    <!-- BODY -->
    <!-- =============================== -->
    <div class="flex flex-col gap-3">
        <h2 class="text-lg font-semibold">Body</h2>

        <?php if (!$bodyText): ?>
            <div class="bg-white/[2%] border border-neutral-200 dark:border-neutral-800 rounded-md w-full p-5 uppercase text-sm text-center font-mono shadow-xs text-neutral-600 dark:text-neutral-400">
                <span class="text-neutral-400 dark:text-neutral-600">// </span>No request body
            </div>
        <?php else: ?>
            <div
                class="bg-white/[2%] border border-neutral-200 dark:border-neutral-800 rounded-md w-full p-5 shadow-xs font-mono text-sm overflow-auto whitespace-pre-wrap text-neutral-800 dark:text-neutral-200">
                <?= htmlspecialchars($bodyText) ?>
            </div>
        <?php endif; ?>

    </div>


    <!-- =============================== -->
    <!-- ROUTING -->
    <!-- =============================== -->
    <div class="flex flex-col gap-3">
        <h2 class="text-lg font-semibold">Routing</h2>
        <div class="flex flex-col">

            <!-- CONTROLLER -->
            <div class="flex max-w-full items-baseline gap-2 h-10 text-sm font-mono">
                <div class="uppercase text-neutral-500 dark:text-neutral-400 shrink-0">controller</div>
                <div class="min-w-6 grow h-3 border-b-2 border-dotted border-neutral-300 dark:border-white/20"></div>
                <div class="truncate text-neutral-900 dark:text-white">
                    <span data-tippy-content="<?= htmlspecialchars($routeController) ?>">
                        <?= htmlspecialchars($routeController) ?>
                    </span>
                </div>
            </div>

            <!-- MIDDLEWARE -->
            <div class="flex max-w-full items-baseline gap-2 h-10 text-sm font-mono">
                <div class="uppercase text-neutral-500 dark:text-neutral-400 shrink-0">middleware</div>
                <div class="min-w-6 grow h-3 border-b-2 border-dotted border-neutral-300 dark:border-white/20"></div>
                <div class="truncate text-neutral-900 dark:text-white">
                    <span data-tippy-content="<?= htmlspecialchars($routeMiddleware) ?>">
                        <?= htmlspecialchars($routeMiddleware) ?>
                    </span>
                </div>
            </div>

        </div>
    </div>


    <!-- =============================== -->
    <!-- ROUTE PARAMETERS -->
    <!-- =============================== -->
    <div class="flex flex-col gap-3">
        <h2 class="text-lg font-semibold">Routing parameters</h2>

        <?php if (empty($routeParams)): ?>
            <div class="bg-white/[2%] border border-neutral-200 dark:border-neutral-800 rounded-md w-full p-5 uppercase text-sm text-center font-mono shadow-xs text-neutral-600 dark:text-neutral-400">
                <span class="text-neutral-400 dark:text-neutral-600">// </span>No routing parameters
            </div>
        <?php else: ?>
            <div class="flex flex-col">
                <?php foreach ($routeParams as $key => $value): ?>
                    <div class="flex max-w-full items-baseline gap-2 h-10 text-sm font-mono">
                        <div class="uppercase text-neutral-500 dark:text-neutral-400 shrink-0"><?= htmlspecialchars($key) ?></div>
                        <div class="min-w-6 grow h-3 border-b-2 border-dotted border-neutral-300 dark:border-white/20"></div>
                        <div class="truncate text-neutral-900 dark:text-white">
                            <span data-tippy-content="<?= htmlspecialchars($value) ?>">
                                <?= htmlspecialchars($value) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</section>
