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

if (isset($request) && is_object($request)) {
    $route = $request->getMatchedRoute();
    if ($route) {
        $routeController = $route['action'] ?? 'Closure';
        $routeMiddleware = isset($route['middleware'])
            ? implode(', ', (array) $route['middleware'])
            : '-';
        $routeParams = $route['params'] ?? [];
    }
}

?>

<section class="container">

    <!-- =============================== -->
    <!-- HEADERS -->
    <!-- =============================== -->
    <div class="mb-4">
        <h2 class="fs-5 fw-semibold mb-3">Headers</h2>

        <div class="card shadow-sm">
            <div class="list-group list-group-flush">

                <?php foreach ($headers as $name => $value): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center small">

                        <strong class="text-uppercase text-muted me-2" style="width: 180px;">
                            <?= htmlspecialchars($name) ?>
                        </strong>

                        <span class="flex-grow-1 border-bottom border-dotted mx-2 opacity-50"></span>

                        <span class="text-truncate" data-tippy-content="<?= htmlspecialchars($value) ?>">
                            <?= htmlspecialchars($value) ?>
                        </span>

                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>


    <!-- =============================== -->
    <!-- BODY -->
    <!-- =============================== -->
    <div class="mb-4">
        <h2 class="fs-5 fw-semibold mb-3">Body</h2>

        <?php if (!$bodyText): ?>
            <div class="card shadow-sm p-3 text-center text-uppercase small text-muted">
                // No request body
            </div>
        <?php else: ?>
            <div class="card shadow-sm p-3 small" style="white-space: pre-wrap;">
                <?= htmlspecialchars($bodyText) ?>
            </div>
        <?php endif; ?>

    </div>


    <!-- =============================== -->
    <!-- ROUTING -->
    <!-- =============================== -->
    <div class="mb-4">
        <h2 class="fs-5 fw-semibold mb-3">Routing</h2>

        <div class="card shadow-sm">
            <div class="list-group list-group-flush">

                <!-- CONTROLLER -->
                <div class="list-group-item d-flex justify-content-between align-items-center small">
                    <strong class="text-uppercase text-muted me-2" style="width: 180px;">Controller</strong>
                    <span class="flex-grow-1 border-bottom border-dotted mx-2 opacity-50"></span>
                    <span class="text-truncate" data-tippy-content="<?= htmlspecialchars($routeController) ?>">
                        <?= htmlspecialchars($routeController) ?>
                    </span>
                </div>

                <!-- MIDDLEWARE -->
                <div class="list-group-item d-flex justify-content-between align-items-center small">
                    <strong class="text-uppercase text-muted me-2" style="width: 180px;">Middleware</strong>
                    <span class="flex-grow-1 border-bottom border-dotted mx-2 opacity-50"></span>
                    <span class="text-truncate" data-tippy-content="<?= htmlspecialchars($routeMiddleware) ?>">
                        <?= htmlspecialchars($routeMiddleware) ?>
                    </span>
                </div>

            </div>
        </div>

    </div>


    <!-- =============================== -->
    <!-- ROUTE PARAMETERS -->
    <!-- =============================== -->
    <div class="mb-4">
        <h2 class="fs-5 fw-semibold mb-3">Routing Parameters</h2>

        <?php if (empty($routeParams)): ?>
            <div class="card shadow-sm p-3 text-center text-uppercase small text-muted">
                // No routing parameters
            </div>
        <?php else: ?>
            <div class="card shadow-sm">
                <div class="list-group list-group-flush">

                    <?php foreach ($routeParams as $key => $value): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center small">
                            <strong class="text-uppercase text-muted me-2" style="width: 180px;">
                                <?= htmlspecialchars($key) ?>
                            </strong>
                            <span class="flex-grow-1 border-bottom border-dotted mx-2 opacity-50"></span>
                            <span class="text-truncate" data-tippy-content="<?= htmlspecialchars($value) ?>">
                                <?= htmlspecialchars($value) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>

                </div>
            </div>
        <?php endif; ?>

    </div>

</section>
