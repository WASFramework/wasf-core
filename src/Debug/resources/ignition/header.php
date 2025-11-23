<?php
// wasf-core/src/Debug/resources/ignition.php
/** @var Throwable $exception */
$line = $exception->getLine();
$file = $exception->getFile();
$codeAll = @file($file) ?: [];
$start = max($line - 7, 1);
$end   = min($line + 7, count($codeAll));
$trace = $exception->getTrace();
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$requestUri = ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');
?>

<!doctype html>
<html lang="en" data-bs-theme="dark" id="html-root">
<head>
    <meta charset="utf-8"/>
    <title><?= htmlspecialchars($exception->getMessage()) ?></title>

    <!-- Bootstrap 5 -->
    <link href="/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/debug/ignition.css" rel="stylesheet">
    <link href="/assets/debug/prism.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Inter font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <meta name="viewport" content="width=device-width,initial-scale=1"/>

    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
    </style>
</head>

<body>
<div class="min-vh-100">

    <!-- Top Navigation -->
    <section class="container py-3 border-bottom border-secondary border-opacity-25">

        <div class="d-flex align-items-center justify-content-between">
            <!-- Left branding -->
            <div class="d-flex align-items-center gap-2">
                <div class="bg-danger text-white d-flex align-items-center justify-content-center rounded"
                     style="width: 20px; height: 20px;">
                    <i class="bi bi-exclamation"></i>
                </div>

                <div class="fw-semibold text-white">
                    Internal Server Error
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">

                <button 
                    class="btn btn-sm btn-outline-secondary copy-btn d-flex align-items-center gap-2"
                    data-copy="<?= htmlspecialchars($filePath) ?>:<?= htmlspecialchars($fileLine) ?>"
                >
                    <i class="bi bi-copy copy-icon"></i>
                    <span class="copy-label">Copy as Markdown</span>
                </button>
            </div>
        </div>

    </section>

    <!-- CONTENT BELOW -->