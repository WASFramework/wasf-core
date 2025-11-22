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
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <title><?= htmlspecialchars($exception->getMessage()) ?></title>

        <!-- Inter (CDN) -->
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap"
            rel="stylesheet">

        <link rel="stylesheet" href="/assets/debug/ignition.css">

        <!-- Prism (code highlight) -->
        <link rel="stylesheet" href="/assets/debug/prism-tomorrow.css">
        <script src="/assets/debug/prism.min.js"></script>

        <meta name="viewport" content="width=device-width,initial-scale=1"/>
        <style>
            body,
            html {
                font-family: Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,"Helvetica Neue",Arial;
                height: 100%;
                margin: 0;
            }
        </style>
    </head>
    <body class="font-sans antialiased overflow-x-hidden bg-neutral-50 dark:bg-neutral-900 dark:text-white scheme-light-dark">
        <div class="min-h-dvh">
            <section
                class="w-full max-w-7xl mx-auto p-4 sm:p-14 border-x border-dashed border-neutral-300 dark:border-white/[9%] px-6 py-0 sm:py-0">
                <div
                    class="flex items-center justify-between"
                    x-data="{
                            copied: false,
                            async copyToClipboard() {
                                try {
                                    await window.copyToClipboard(markdown);
                                    this.copied = true;
                                    setTimeout(() =&gt; { this.copied = false }, 3000);
                                } catch (err) {
                                    console.error('Failed to copy the markdown: ', err);
                                }
                            }
                        }">
                    <div class="flex items-center gap-2 h-[56px]">
                        <div
                            class="w-[18px] h-[18px] flex items-center justify-center dark:bg-blue-900! rounded-md">
                            <svg
                                width="2"
                                height="10"
                                class="text-white"
                                viewbox="0 0 2 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M1.00006 6.3188C1.41416 6.3188 1.75006 5.98295 1.75006 5.56885V1.43115C1.75006 1.01705 1.41416 0.681152 1.00006 0.681152C0.585961 0.681152 0.250061 1.01705 0.250061 1.43115V5.56885C0.250061 5.98295 0.585961 6.3188 1.00006 6.3188Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M1.00006 9.41699C1.55235 9.41699 2.00007 8.96929 2.00007 8.41699C2.00007 7.86469 1.55235 7.41699 1.00006 7.41699C0.447781 7.41699 6.10352e-05 7.86469 6.10352e-05 8.41699C6.10352e-05 8.96929 0.447781 9.41699 1.00006 9.41699Z"
                                    fill="currentColor "></path>
                            </svg>
                        </div>
                        <div class="font-medium text-sm text-neutral-900 dark:text-white">
                            Internal Server Error
                        </div>
                    </div>

                    <button
                        class="text-sm rounded-md border px-3 h-8 flex items-center gap-2 transition-colors duration-200 ease-in-out cursor-pointer shadow-xs text-neutral-600 dark:text-neutral-400 bg-white/5 border-neutral-200 hover:bg-neutral-100 dark:bg-white/5 dark:border-white/10 dark:hover:bg-white/10"
                        @click="copyToClipboard()">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="12"
                            height="12"
                            viewbox="0 0 12 12"
                            fill="none"
                            class="w-3 h-3"
                            x-show="!copied">
                            <g clip-path="url(#clip0_14732_6079)">
                                <path
                                    d="M4.25 4.25012V1.25012H10.75V7.75012H7.75M7.75 4.25012H1.25V10.7501H7.75V4.25012Z"
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </g>
                            <defs>
                                <clippath id="clip0_14732_6079">
                                    <rect width="12" height="12"></rect>
                                </clippath>
                            </defs>
                        </svg>
                        <svg
                            fill="none"
                            stroke="currentColor"
                            viewbox="0 0 24 24"
                            class="w-3 h-3 text-emerald-500"
                            x-show="copied"
                            style="display: none;">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-text="copied ? 'Copied to clipboard' : 'Copy as Markdown'">Copy as Markdown</span>
                    </button>
                </div>
            </section>