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

        <!-- Laravel Ignition / Tailwind built CSS (you should paste build CSS into this
        file) -->
        <link rel="stylesheet" href="/assets/debug/ignition-laravel.css">

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
                            class="w-[18px] h-[18px] flex items-center justify-center bg-rose-500 rounded-md">
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

            <div class="h-0 w-full relative">
                <div
                    class="absolute top-[-1px] left-0 right-0 bottom-0 border-t border-dashed border-neutral-300 dark:border-white/[9%]"></div>
            </div>

            <section
                class="w-full max-w-7xl mx-auto p-4 sm:p-14 border-x border-dashed border-neutral-300 dark:border-white/[9%] flex flex-col gap-8 py-0 sm:py-0">
                <div class="flex flex-col pt-8 sm:pt-16 overflow-x-auto">
                    <div class="flex flex-col gap-5 mb-8">
                        <h1 class="text-3xl font-semibold text-neutral-950 dark:text-white">Illuminate\Database\QueryException</h1>
                        <div
                            class="truncate font-mono text-xs text-neutral-500 dark:text-neutral-400 -mt-3 text-xs"
                            dir="ltr">
                            <span
                                data-tippy-content="vendor\laravel\framework\src\Illuminate\Database\Connection.php:824">
                                vendor\laravel\framework\src\Illuminate\Database\Connection.php<span class="text-neutral-500">:824</span>
                            </span>
                        </div>
                        <p class="text-xl font-light text-neutral-800 dark:text-neutral-300">
                            could not find driver (Connection: sqlite, SQL: select * from "sessions" where
                            "id" = XtDSbCY95BNzDc0G3f7G778q7r1LrlAEtEKSlzen limit 1)
                        </p>
                    </div>

                    <div class="flex items-start gap-2 mb-8 sm:mb-16">
                        <div
                            class="bg-white dark:bg-white/[3%] border border-neutral-200 dark:border-white/10 divide-x divide-neutral-200 dark:divide-white/10 rounded-md shadow-xs flex items-center gap-0.5">
                            <div class="flex items-center gap-1.5 h-6 px-[6px] font-mono text-[13px]">
                                <span class="text-neutral-400 dark:text-neutral-500">LARAVEL</span>
                                <span class="text-neutral-500 dark:text-neutral-300">12.38.1</span>
                            </div>
                            <div class="flex items-center gap-1.5 h-6 px-[6px] font-mono text-[13px]">
                                <span class="text-neutral-400 dark:text-neutral-500">PHP</span>
                                <span class="text-neutral-500 dark:text-neutral-300">8.2.20</span>
                            </div>
                        </div>
                        <div
                            class="inline-flex w-fit shrink-0 items-center justify-center gap-1 font-mono leading-3 uppercase transition-colors dark:border [&amp;_svg]:size-2.5 h-6 min-w-5 rounded-md px-1.5 text-xs/none bg-rose-200 text-rose-900 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-100 dark:[&amp;_svg]:!text-white">
                            <svg
                                width="10"
                                height="10"
                                viewbox="0 0 10 10"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-2.5 h-2.5">
                                <g clip-path="url(#clip0_14732_6105)">
                                    <path
                                        d="M9.87466 7.8287L5.92654 0.549947C5.82917 0.369362 5.68068 0.221523 5.49966 0.124947C5.25374 -0.00665839 4.9658 -0.0358401 4.69847 0.0437494C4.43115 0.123339 4.20606 0.305262 4.07216 0.549947L0.124664 7.8287C0.0383472 7.98887 -0.00481098 8.16875 -0.000569449 8.35066C0.00367208 8.53256 0.0551674 8.71024 0.148856 8.86622C0.242546 9.0222 0.375205 9.15112 0.533798 9.24031C0.692391 9.32951 0.871462 9.37591 1.05341 9.37495H8.94591C9.12031 9.37495 9.29203 9.33202 9.44591 9.24995C9.56783 9.18524 9.67572 9.09703 9.76338 8.99041C9.85104 8.8838 9.91672 8.76088 9.95663 8.62876C9.99655 8.49663 10.0099 8.35791 9.99595 8.22059C9.98199 8.08328 9.94036 7.95009 9.87466 7.8287ZM4.99966 8.12495C4.87605 8.12495 4.75521 8.08829 4.65243 8.01962C4.54965 7.95094 4.46954 7.85333 4.42224 7.73912C4.37493 7.62492 4.36256 7.49925 4.38667 7.37802C4.41079 7.25678 4.47031 7.14541 4.55772 7.05801C4.64513 6.9706 4.75649 6.91107 4.87773 6.88696C4.99897 6.86284 5.12464 6.87522 5.23884 6.92252C5.35304 6.96983 5.45066 7.04993 5.51933 7.15272C5.58801 7.2555 5.62466 7.37633 5.62466 7.49995C5.62466 7.66571 5.55882 7.82468 5.44161 7.94189C5.3244 8.0591 5.16542 8.12495 4.99966 8.12495ZM5.62466 5.93745C5.62466 6.02033 5.59174 6.09981 5.53313 6.15842C5.47453 6.21702 5.39504 6.24995 5.31216 6.24995H4.68716C4.60428 6.24995 4.5248 6.21702 4.46619 6.15842C4.40759 6.09981 4.37466 6.02033 4.37466 5.93745V3.43745C4.37466 3.35457 4.40759 3.27508 4.46619 3.21648C4.5248 3.15787 4.60428 3.12495 4.68716 3.12495H5.31216C5.39504 3.12495 5.47453 3.15787 5.53313 3.21648C5.59174 3.27508 5.62466 3.35457 5.62466 3.43745V5.93745Z"
                                        fill="currentColor"></path>
                                </g>
                                <defs>
                                    <clippath id="clip0_14732_6105">
                                        <rect width="10" height="10"></rect>
                                    </clippath>
                                </defs>
                            </svg>
                            UNHANDLED
                        </div>
                        <div
                            class="inline-flex w-fit shrink-0 items-center justify-center gap-1 font-mono leading-3 uppercase transition-colors dark:border [&amp;_svg]:size-2.5 h-6 min-w-5 rounded-md px-1.5 text-xs/none bg-rose-600 dark:border-rose-500 dark:bg-rose-600 text-white dark:text-white [&amp;_svg]:!text-white">
                            CODE 0
                        </div>
                    </div>

                    <div
                        x-data="{
                            copied: false,
                            async copyToClipboard() {
                                try {
                                    await window.copyToClipboard('http://laravel.test');
                                    this.copied = true;
                                    setTimeout(() =&gt; { this.copied = false }, 3000);
                                } catch (err) {
                                    console.error('Failed to copy the requestURL: ', err);
                                }
                            }
                        }"
                        class="bg-white dark:bg-[#1a1a1a] border border-neutral-200 dark:border-white/10 rounded-lg flex items-center justify-between h-10 px-2 shadow-xs relative z-50">
                        <div class="flex items-center gap-3 w-full">
                            <div
                                class="inline-flex w-fit shrink-0 items-center justify-center gap-1 font-mono leading-3 uppercase transition-colors dark:border [&amp;_svg]:size-2.5 h-6 min-w-5 rounded-md px-1.5 text-xs/none bg-rose-600 dark:border-rose-500 dark:bg-rose-600 text-white dark:text-white [&amp;_svg]:!text-white">
                                <svg
                                    width="10"
                                    height="10"
                                    viewbox="0 0 10 10"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-2.5 h-2.5">
                                    <g clip-path="url(#clip0_14732_6105)">
                                        <path
                                            d="M9.87466 7.8287L5.92654 0.549947C5.82917 0.369362 5.68068 0.221523 5.49966 0.124947C5.25374 -0.00665839 4.9658 -0.0358401 4.69847 0.0437494C4.43115 0.123339 4.20606 0.305262 4.07216 0.549947L0.124664 7.8287C0.0383472 7.98887 -0.00481098 8.16875 -0.000569449 8.35066C0.00367208 8.53256 0.0551674 8.71024 0.148856 8.86622C0.242546 9.0222 0.375205 9.15112 0.533798 9.24031C0.692391 9.32951 0.871462 9.37591 1.05341 9.37495H8.94591C9.12031 9.37495 9.29203 9.33202 9.44591 9.24995C9.56783 9.18524 9.67572 9.09703 9.76338 8.99041C9.85104 8.8838 9.91672 8.76088 9.95663 8.62876C9.99655 8.49663 10.0099 8.35791 9.99595 8.22059C9.98199 8.08328 9.94036 7.95009 9.87466 7.8287ZM4.99966 8.12495C4.87605 8.12495 4.75521 8.08829 4.65243 8.01962C4.54965 7.95094 4.46954 7.85333 4.42224 7.73912C4.37493 7.62492 4.36256 7.49925 4.38667 7.37802C4.41079 7.25678 4.47031 7.14541 4.55772 7.05801C4.64513 6.9706 4.75649 6.91107 4.87773 6.88696C4.99897 6.86284 5.12464 6.87522 5.23884 6.92252C5.35304 6.96983 5.45066 7.04993 5.51933 7.15272C5.58801 7.2555 5.62466 7.37633 5.62466 7.49995C5.62466 7.66571 5.55882 7.82468 5.44161 7.94189C5.3244 8.0591 5.16542 8.12495 4.99966 8.12495ZM5.62466 5.93745C5.62466 6.02033 5.59174 6.09981 5.53313 6.15842C5.47453 6.21702 5.39504 6.24995 5.31216 6.24995H4.68716C4.60428 6.24995 4.5248 6.21702 4.46619 6.15842C4.40759 6.09981 4.37466 6.02033 4.37466 5.93745V3.43745C4.37466 3.35457 4.40759 3.27508 4.46619 3.21648C4.5248 3.15787 4.60428 3.12495 4.68716 3.12495H5.31216C5.39504 3.12495 5.47453 3.15787 5.53313 3.21648C5.59174 3.27508 5.62466 3.35457 5.62466 3.43745V5.93745Z"
                                            fill="currentColor"></path>
                                    </g>
                                    <defs>
                                        <clippath id="clip0_14732_6105">
                                            <rect width="10" height="10"></rect>
                                        </clippath>
                                    </defs>
                                </svg>
                                500
                            </div>
                            <div
                                class="inline-flex w-fit shrink-0 items-center justify-center gap-1 font-mono leading-3 uppercase transition-colors dark:border [&amp;_svg]:size-2.5 h-6 min-w-5 rounded-md px-1.5 text-xs/none bg-black/8 text-neutral-900 dark:border-neutral-700 dark:bg-white/10 dark:text-neutral-100">
                                <svg
                                    width="12"
                                    height="12"
                                    viewbox="0 0 12 12"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-2.5 h-2.5">
                                    <path
                                        d="M5.99996 10.6876C7.10936 10.6876 8.00871 8.58896 8.00871 6.00012C8.00871 3.41129 7.10936 1.31262 5.99996 1.31262C4.89056 1.31262 3.99121 3.41129 3.99121 6.00012C3.99121 8.58896 4.89056 10.6876 5.99996 10.6876Z"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M1.3125 6.00012H10.6875"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M6 10.6876C8.58883 10.6876 10.6875 8.58896 10.6875 6.00012C10.6875 3.41129 8.58883 1.31262 6 1.31262C3.41117 1.31262 1.3125 3.41129 1.3125 6.00012C1.3125 8.58896 3.41117 10.6876 6 10.6876Z"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                                GET
                            </div>
                            <div
                                class="flex-1 text-sm font-light truncate text-neutral-950 dark:text-white">
                                <span data-tippy-content="http://laravel.test">
                                    http://laravel.test
                                </span>
                            </div>
                            <button
                                @click="copyToClipboard()"
                                class="rounded-md w-6 h-6 flex flex-shrink-0 items-center justify-center cursor-pointer border transition-colors duration-200 ease-in-out bg-white/5 border-neutral-200 hover:bg-neutral-100 dark:bg-white/5 dark:border-white/10 dark:hover:bg-white/10">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="12"
                                    height="12"
                                    viewbox="0 0 12 12"
                                    fill="none"
                                    class="w-3 h-3 text-neutral-400"
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
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <div class="h-0 w-full relative -mt-5 -z-10">
                <div class="absolute top-[-1px] left-0 right-0 bottom-0 border-t border-dashed border-neutral-300 dark:border-white/[9%]"></div>
            </div>

<section
                    class="w-full max-w-7xl mx-auto p-4 sm:p-14 border-x border-dashed border-neutral-300 dark:border-white/[9%] flex flex-col gap-8 pt-14">
                    <div class="flex flex-col gap-2.5 bg-neutral-50 dark:bg-white/1 border border-neutral-200 dark:border-neutral-800 rounded-xl p-2.5 shadow-xs">
                        <div class="flex items-center gap-2.5 p-2">
                            <div
                                class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-white/5 rounded-md w-6 h-6 flex items-center justify-center p-1">
                                <svg
                                    width="10"
                                    height="10"
                                    viewBox="0 0 10 10"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-2.5 h-2.5 text-blue-500 dark:text-emerald-500">
                                    <g clip-path="url(#clip0_14732_6105)">
                                        <path
                                            d="M9.87466 7.8287L5.92654 0.549947C5.82917 0.369362 5.68068 0.221523 5.49966 0.124947C5.25374 -0.00665839 4.9658 -0.0358401 4.69847 0.0437494C4.43115 0.123339 4.20606 0.305262 4.07216 0.549947L0.124664 7.8287C0.0383472 7.98887 -0.00481098 8.16875 -0.000569449 8.35066C0.00367208 8.53256 0.0551674 8.71024 0.148856 8.86622C0.242546 9.0222 0.375205 9.15112 0.533798 9.24031C0.692391 9.32951 0.871462 9.37591 1.05341 9.37495H8.94591C9.12031 9.37495 9.29203 9.33202 9.44591 9.24995C9.56783 9.18524 9.67572 9.09703 9.76338 8.99041C9.85104 8.8838 9.91672 8.76088 9.95663 8.62876C9.99655 8.49663 10.0099 8.35791 9.99595 8.22059C9.98199 8.08328 9.94036 7.95009 9.87466 7.8287ZM4.99966 8.12495C4.87605 8.12495 4.75521 8.08829 4.65243 8.01962C4.54965 7.95094 4.46954 7.85333 4.42224 7.73912C4.37493 7.62492 4.36256 7.49925 4.38667 7.37802C4.41079 7.25678 4.47031 7.14541 4.55772 7.05801C4.64513 6.9706 4.75649 6.91107 4.87773 6.88696C4.99897 6.86284 5.12464 6.87522 5.23884 6.92252C5.35304 6.96983 5.45066 7.04993 5.51933 7.15272C5.58801 7.2555 5.62466 7.37633 5.62466 7.49995C5.62466 7.66571 5.55882 7.82468 5.44161 7.94189C5.3244 8.0591 5.16542 8.12495 4.99966 8.12495ZM5.62466 5.93745C5.62466 6.02033 5.59174 6.09981 5.53313 6.15842C5.47453 6.21702 5.39504 6.24995 5.31216 6.24995H4.68716C4.60428 6.24995 4.5248 6.21702 4.46619 6.15842C4.40759 6.09981 4.37466 6.02033 4.37466 5.93745V3.43745C4.37466 3.35457 4.40759 3.27508 4.46619 3.21648C4.5248 3.15787 4.60428 3.12495 4.68716 3.12495H5.31216C5.39504 3.12495 5.47453 3.15787 5.53313 3.21648C5.59174 3.27508 5.62466 3.35457 5.62466 3.43745V5.93745Z"
                                            fill="currentColor"></path>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_14732_6105">
                                            <rect width="10" height="10"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-neutral-900 dark:text-white">Exception trace</h3>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <div
                                x-data="{ expanded: false }"
                                class="group rounded-lg border border-neutral-200 dark:border-white/5 border-dashed border-neutral-300 bg-neutral-50 opacity-90 dark:border-white/10 dark:bg-white/1"
                                :class="{
                                    'bg-white dark:bg-white/5 shadow-xs': expanded,
                                    'border-dashed border-neutral-300 bg-neutral-50 opacity-90 dark:border-white/10 dark:bg-white/1': !expanded,
                                }">
                                <div
                                    class="flex h-11 cursor-pointer items-center gap-3 rounded-lg pr-2.5 pl-4 hover:bg-white/50 dark:hover:bg-white/2"
                                    @click="expanded = !expanded">
                                    <svg
                                        width="12"
                                        height="12"
                                        viewBox="0 0 12 12"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-3 h-3 text-neutral-400"
                                        x-show="!expanded">
                                        <path
                                            d="M2.75 2.75H5.614L5.316 2.114C5.069 1.587 4.54 1.25 3.958 1.25H2.25C1.422 1.25 0.75 1.922 0.75 2.75V4.75C0.75 3.645 1.645 2.75 2.75 2.75Z"></path>
                                        <path
                                            d="M0.75 4.75V2.75C0.75 1.922 1.422 1.25 2.25 1.25H3.958C4.54 1.25 5.069 1.587 5.316 2.114L5.614 2.75"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                        <path
                                            d="M2.75 2.75H9.25C10.355 2.75 11.25 3.645 11.25 4.75V8.25C11.25 9.355 10.355 10.25 9.25 10.25H2.75C1.645 10.25 0.75 9.355 0.75 8.25V4.75C0.75 3.645 1.645 2.75 2.75 2.75Z"
                                            stroke="currentColor"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                    <svg
                                        width="12"
                                        height="12"
                                        viewBox="0 0 12 12"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="w-3 h-3 text-blue-500 dark:text-emerald-500"
                                        x-show="expanded"
                                        style="display: none;">
                                        <g clip-path="url(#clip0_14732_6211)">
                                            <path
                                                d="M1.75 5.25V2.75C1.75 1.922 2.422 1.25 3.25 1.25H4.202C4.808 1.25 5.381 1.525 5.761 1.998L6.364 2.75H8.25C9.355 2.75 10.25 3.645 10.25 4.75V5.25"
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                            <path
                                                d="M2.46801 5.25H9.53101C10.44 5.25 11.14 6.052 11.017 6.953L10.735 9.021C10.6 10.012 9.75301 10.751 8.75301 10.751H3.24601C2.24601 10.751 1.39901 10.012 1.26401 9.021L0.982011 6.953C0.859011 6.052 1.55901 5.25 2.46801 5.25Z"
                                                stroke="currentColor"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"></path>
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_14732_6211">
                                                <rect width="12" height="12"></rect>
                                            </clipPath>
                                        </defs>
                                    </svg>

                                    <div
                                        class="flex-1 font-mono text-xs leading-3 text-neutral-900 dark:text-neutral-400">
                                        52 vendor frames
                                    </div>

                                    <button
                                        type="button"
                                        class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-md dark:border dark:border-white/8 group-hover:text-blue-500 group-hover:dark:text-emerald-500 text-neutral-500 dark:text-neutral-500 dark:bg-white/3"
                                        :class="{
                                'text-blue-500 dark:text-emerald-500 dark:bg-white/5': expanded,
                                'text-neutral-500 dark:text-neutral-500 dark:bg-white/3': !expanded,
                            }">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="8"
                                            height="12"
                                            viewBox="0 0 8 12"
                                            fill="none"
                                            x-show="expanded"
                                            style="display: none;">
                                            <g clip-path="url(#clip0_14550_6168)">
                                                <path
                                                    d="M6.75 11.0001L4 8.25012L1.25 11.0001"
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path
                                                    d="M6.75 1.50012L4 4.25012L1.25 1.50012"
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_14550_6168">
                                                    <rect
                                                        width="8"
                                                        height="11"
                                                        fill="white"
                                                        style="fill:white;fill-opacity:1;"
                                                        transform="translate(0 0.500122)"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 12 12"
                                            fill="none"
                                            x-show="!expanded">
                                            <g clip-path="url(#clip0_14550_6155)">
                                                <path
                                                    d="M8.75 8.25012L6 11.0001L3.25 8.25012"
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                                <path
                                                    d="M8.75 3.75012L6 1.00012L3.25 3.75012"
                                                    stroke="currentColor"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"></path>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_14550_6155">
                                                    <rect width="12" height="12" fill="white" style="fill:white;fill-opacity:1;"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </button>
                                </div>

                                <div
                                    class="flex flex-col rounded-b-lg divide-y divide-neutral-200 border-t border-neutral-200 dark:divide-white/5 dark:border-white/5"
                                    x-show="expanded"
                                    style="display: none;">
                                    <div class="flex flex-col divide-y divide-neutral-200 dark:divide-white/5">
                                        <div
                                            class="grid gap-3 p-4 bg-neutral-50 dark:bg-transparent overflow-x-auto rounded-lg">
                                            <div class="flex">
                                                <div
                                                    x-data="{ highlightedCode: null }"
                                                    x-init="
                                                                highlightedCode = window.highlight(
                                                                    'Illuminate\\Session\\DatabaseSessionHandler-\u003Eread(string)',
                                                                    'php',
                                                                    true,
                                                                    false,
                                                                    1,
                                                                    null
                                                                );
                                                            "
                                                    class="text-xs min-w-0"
                                                    data-tippy-content="Illuminate\Session\DatabaseSessionHandler-&gt;read(string)">
                                                    <div x-html="highlightedCode">
                                                        <pre class="shiki shiki-themes light-plus dark-plus bg-transparent! truncate" style="background-color:#FFFFFF;--shiki-dark-bg:#1E1E1E;color:#000000;--shiki-dark:#D4D4D4" tabindex="0"><code><span class="line"><span style="color:#000000;--shiki-dark:#D4D4D4">Illuminate\Session\DatabaseSessionHandler-&gt;</span><span style="color:#795E26;--shiki-dark:#DCDCAA">read</span><span style="color:#000000;--shiki-dark:#D4D4D4">(</span><span style="color:#0000FF;--shiki-dark:#569CD6">string</span><span style="color:#000000;--shiki-dark:#D4D4D4">)</span></span></code></pre>
                                                    </div>
                                                    <div x-show="!highlightedCode" style="display: none;">
                                                        <pre class="truncate"><code>Illuminate\Session\DatabaseSessionHandler-&gt;read(string)</code></pre>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="truncate font-mono text-xs text-neutral-500 dark:text-neutral-400 text-xs"
                                                dir="ltr">
                                                <span
                                                    data-tippy-content="vendor\laravel\framework\src\Illuminate\Session\DatabaseSessionHandler.php:96">
                                                    vendor\laravel\framework\src\Illuminate\Session\DatabaseSessionHandler.php<span class="text-neutral-500">:96</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                x-data="{
                        expanded: true,
                        hasCode: true
                    }"
                    class="group rounded-lg border border-neutral-200 dark:border-white/10 overflow-hidden shadow-xs dark:border-white/5"
                    :class="{ 'dark:border-white/5': expanded }">
                    <div
                        class="flex h-11 items-center gap-3 bg-white pr-2.5 pl-4 overflow-x-auto cursor-pointer hover:bg-white/50 dark:hover:bg-white/5 hover:[&amp;_svg]:stroke-emerald-500 dark:bg-white/5 rounded-t-lg"
                        :class="{
                            'cursor-pointer hover:bg-white/50 dark:hover:bg-white/5 hover:[&amp;_svg]:stroke-emerald-500': hasCode,
                            'dark:bg-white/5 rounded-t-lg': expanded,
                            'dark:bg-white/3 rounded-lg': !expanded
                        }"
                        @click="hasCode &amp;&amp; (expanded = !expanded)">

                        <div class="flex size-3 items-center justify-center flex-shrink-0">
                            <div
                                class="size-2 rounded-full bg-rose-500 dark:bg-neutral-400"
                                :class="{
                                    'bg-rose-500 dark:bg-neutral-400': expanded,
                                    'bg-rose-200 dark:bg-neutral-700': !expanded
                                }"></div>
                        </div>

                        <div class="flex flex-1 items-center justify-between gap-6 min-w-0">
                            <div
                                x-data="{ highlightedCode: null }"
                                x-init="
                                    highlightedCode = window.highlight(
                                        'public\\index.php',
                                        'php',
                                        true,
                                        false,
                                        1,
                                        null
                                    );
                                "
                                class="text-xs min-w-0"
                                data-tippy-content="public\index.php">
                                <div x-html="highlightedCode">
                                    <pre class="shiki shiki-themes light-plus dark-plus bg-transparent! truncate" style="background-color:#FFFFFF;--shiki-dark-bg:#1E1E1E;color:#000000;--shiki-dark:#D4D4D4" tabindex="0"><code><span class="line"><span style="color:#0000FF;--shiki-dark:#569CD6">public</span><span style="color:#000000;--shiki-dark:#D4D4D4">\index</span><span style="color:#000000;--shiki-dark:#D4D4D4">.</span><span style="color:#000000;--shiki-dark:#D4D4D4">php</span></span></code></pre>
                                </div>
                                <div x-show="!highlightedCode" style="display: none;">
                                    <pre class="truncate"><code>public\index.php</code></pre>
                                </div>
                            </div>
                            <div
                                class="truncate font-mono text-xs text-neutral-500 dark:text-neutral-400"
                                dir="rtl">
                                <span data-tippy-content="public\index.php:20" aria-describedby="tippy-108">
                                    public\index.php<span class="text-neutral-500">:20</span>
                                </span>
                            </div>
                        </div>

                        <div class="flex-shrink-0">
                            <button
                                type="button"
                                class="flex h-6 w-6 cursor-pointer items-center justify-center rounded-md dark:border dark:border-white/8 group-hover:text-blue-500 group-hover:dark:text-emerald-500 text-blue-500 dark:text-emerald-500 dark:bg-white/5"
                                :class="{
                        'text-blue-500 dark:text-emerald-500 dark:bg-white/5': expanded,
                        'text-neutral-500 dark:text-neutral-500 dark:bg-white/3': !expanded,
                    }">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="8"
                                height="12"
                                viewBox="0 0 8 12"
                                fill="none"
                                x-show="expanded">
                                <g clip-path="url(#clip0_14550_6168)">
                                    <path
                                        d="M6.75 11.0001L4 8.25012L1.25 11.0001"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M6.75 1.50012L4 4.25012L1.25 1.50012"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_14550_6168">
                                        <rect
                                            width="8"
                                            height="11"
                                            fill="white"
                                            style="fill:white;fill-opacity:1;"
                                            transform="translate(0 0.500122)"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="12"
                                height="12"
                                viewBox="0 0 12 12"
                                fill="none"
                                x-show="!expanded"
                                style="display: none;">
                                <g clip-path="url(#clip0_14550_6155)">
                                    <path
                                        d="M8.75 8.25012L6 11.0001L3.25 8.25012"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                    <path
                                        d="M8.75 3.75012L6 1.00012L3.25 3.75012"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </g>
                                <defs>
                                    <clipPath id="clip0_14550_6155">
                                        <rect width="12" height="12" fill="white" style="fill:white;fill-opacity:1;"></rect>
                                    </clipPath>
                                </defs>
                            </svg>
                        </button>
                    </div>
                </div>

                <div
                    class="text-sm rounded-b-lg bg-neutral-50 border-t border-neutral-100 dark:bg-neutral-900 dark:border-white/10"
                    x-show="expanded">
                    <div
                        x-data="{ highlightedCode: null }"
                        x-init="
                                highlightedCode = window.highlight(
                                    '\n\/\/ Bootstrap Laravel and handle the request...\n\/** @var Application $app *\/\n$app = require_once __DIR__.\u0027\/..\/bootstrap\/app.php\u0027;\n\n$app-\u003EhandleRequest(Request::capture());\n',
                                    'php',
                                    false,
                                    true,
                                    15,
                                    5
                                );
                            "
                        class="overflow-x-auto">
                        <div x-html="highlightedCode">
                            <pre class="shiki shiki-themes light-plus dark-plus bg-transparent! w-fit min-w-full" style="background-color:#FFFFFF;--shiki-dark-bg:#1E1E1E;color:#000000;--shiki-dark:#D4D4D4" tabindex="0"><code><span class="line inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4"><span class="mr-6 text-neutral-500! dark:text-neutral-600!">15</span></span>
                                <span class="line inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4"><span class="mr-6 text-neutral-500! dark:text-neutral-600!">16</span><span style="color:#008000;--shiki-dark:#6A9955">// Bootstrap Laravel and handle the request...</span></span>
                                <span class="line inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4"><span class="mr-6 text-neutral-500! dark:text-neutral-600!">17</span><span style="color:#008000;--shiki-dark:#6A9955">/** </span><span style="color:#0000FF;--shiki-dark:#569CD6">@var</span><span style="color:#267F99;--shiki-dark:#4EC9B0"> Application</span><span style="color:#008000;--shiki-dark:#6A9955"> $app */</span></span>
                                <span class="line inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4"><span class="mr-6 text-neutral-500! dark:text-neutral-600!">18</span><span style="color:#001080;--shiki-dark:#9CDCFE">$app</span><span style="color:#000000;--shiki-dark:#D4D4D4"> = </span><span style="color:#AF00DB;--shiki-dark:#C586C0">require_once</span><span style="color:#0000FF;--shiki-dark:#569CD6"> __DIR__</span><span style="color:#000000;--shiki-dark:#D4D4D4">.</span><span style="color:#A31515;--shiki-dark:#CE9178">'/../bootstrap/app.php'</span><span style="color:#000000;--shiki-dark:#D4D4D4">;</span></span>
                                <span class="line inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4"><span class="mr-6 text-neutral-500! dark:text-neutral-600!">19</span></span>
                                <span class="line inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 bg-rose-200! dark:bg-rose-900!"><span class="mr-6 text-neutral-500! dark:text-neutral-600! dark:text-white!">20</span><span style="color:#001080;--shiki-dark:#9CDCFE">$app</span><span style="color:#000000;--shiki-dark:#D4D4D4">-&gt;</span><span style="color:#795E26;--shiki-dark:#DCDCAA">handleRequest</span><span style="color:#000000;--shiki-dark:#D4D4D4">(</span><span style="color:#267F99;--shiki-dark:#4EC9B0">Request</span><span style="color:#000000;--shiki-dark:#D4D4D4">::</span><span style="color:#795E26;--shiki-dark:#DCDCAA">capture</span><span style="color:#000000;--shiki-dark:#D4D4D4">());</span></span>
                                <span class="line inline-block w-full px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4"><span class="mr-6 text-neutral-500! dark:text-neutral-600!">21</span></span></code></pre>
                        </div>
                        <div x-show="!highlightedCode" style="display: none;">
                            <pre><code><span class="block px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 "><span class="mr-6 text-neutral-500! dark:text-neutral-600! ">15</span></span><span class="block px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 "><span class="mr-6 text-neutral-500! dark:text-neutral-600! ">16</span>// Bootstrap Laravel and handle the request...</span><span class="block px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 "><span class="mr-6 text-neutral-500! dark:text-neutral-600! ">17</span>/** @var Application $app */</span><span class="block px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 "><span class="mr-6 text-neutral-500! dark:text-neutral-600! ">18</span>$app = require_once __DIR__.'/../bootstrap/app.php';</span><span class="block px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 "><span class="mr-6 text-neutral-500! dark:text-neutral-600! ">19</span></span><span class="block px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 bg-rose-200! dark:bg-rose-900!"><span class="mr-6 text-neutral-500! dark:text-neutral-600! dark:text-white!">20</span>$app-&gt;handleRequest(Request::capture());</span><span class="block px-4 py-1 h-7 even:bg-white odd:bg-white/2 even:dark:bg-white/2 odd:dark:bg-white/4 "><span class="mr-6 text-neutral-500! dark:text-neutral-600! ">21</span></span></code></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            </div>

            <div
                class="flex flex-col gap-2.5 bg-neutral-50 dark:bg-white/1 border border-neutral-200 dark:border-neutral-800 rounded-xl p-2.5 shadow-xs"
                x-data="{
                totalQueries: 0,
                currentPage: 1,
                perPage: 10,
                get totalPages() {
                    return Math.ceil(this.totalQueries / this.perPage);
                },
                get hasPrevious() {
                    return this.currentPage &gt; 1;
                },
                get hasNext() {
                    return this.currentPage &lt; this.totalPages;
                },
                goToPage(page) {
                    if (page &gt;= 1 &amp;&amp; page &lt;= this.totalPages) {
                        this.currentPage = page;
                    }
                },
                first() {
                    this.currentPage = 1;
                },
                last() {
                    this.currentPage = this.totalPages;
                },
                previous() {
                    if (this.hasPrevious) {
                        this.currentPage--;
                    }
                },
                next() {
                    if (this.hasNext) {
                        this.currentPage++;
                    }
                },
                get visiblePages() {
                    const total = this.totalPages;
                    const current = this.currentPage;
                    const pages = [];

                    if (total &lt;= 7) {
                        for (let i = 1; i &lt;= total; i++) {
                            pages.push({ type: 'page', value: i });
                        }
                    } else {
                        if (current &lt;= 4) {
                            for (let i = 1; i &lt;= 5; i++) {
                                pages.push({ type: 'page', value: i });
                            }
                            if (total &gt; 6) {
                                pages.push({ type: 'ellipsis', value: '...', id: 'end' });
                                pages.push({ type: 'page', value: total });
                            }
                        } else if (current &gt; total - 4) {
                            pages.push({ type: 'page', value: 1 });
                            if (total &gt; 6) {
                                pages.push({ type: 'ellipsis', value: '...', id: 'start' });
                            }
                            for (let i = Math.max(total - 4, 2); i &lt;= total; i++) {
                                pages.push({ type: 'page', value: i });
                            }
                        } else {
                            pages.push({ type: 'page', value: 1 });
                            pages.push({ type: 'ellipsis', value: '...', id: 'start' });
                            for (let i = current - 1; i &lt;= current + 1; i++) {
                                pages.push({ type: 'page', value: i });
                            }
                            pages.push({ type: 'ellipsis', value: '...', id: 'end' });
                            pages.push({ type: 'page', value: total });
                        }
                    }
                    return pages;
                }
            }">
        <div class="flex items-center justify-between p-2">
            <div class="flex items-center gap-2.5">
                <div
                    class="bg-white dark:bg-neutral-800 border border-neutral-200 dark:border-white/5 rounded-md w-6 h-6 flex items-center justify-center p-1">
                    <svg
                        width="12"
                        height="12"
                        viewBox="0 0 12 12"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-2.5 h-2.5 text-blue-500 dark:text-emerald-500">
                        <path
                            d="M9.75 2.56944C9.75 3.29815 8.07107 3.88889 6 3.88889C3.92893 3.88889 2.25 3.29815 2.25 2.56944M9.75 2.56944C9.75 1.84074 8.07107 1.25 6 1.25C3.92893 1.25 2.25 1.84074 2.25 2.56944M9.75 2.56944V9.43056C9.75 10.1593 8.07107 10.75 6 10.75C3.92893 10.75 2.25 10.1593 2.25 9.43056V2.56944M9.75 5.94434C9.75 6.67304 8.07107 7.26378 6 7.26378C3.92893 7.26378 2.25 6.67304 2.25 5.94434"
                            stroke="currentColor"
                            stroke-linecap="round"
                            stroke-linejoin="round"></path>
                    </svg>
                </div>
                <h3 class="text-base font-semibold">Queries</h3>
            </div>
            <div
                x-show="totalQueries &gt; 0"
                class="text-sm text-neutral-500 dark:text-neutral-400 flex items-center gap-2"
                style="display: none;">
                <span
                    x-text="`${((currentPage - 1) * perPage) + 1}-${Math.min(currentPage * perPage, totalQueries)} of ${totalQueries}`">1-0 of 0</span>
            </div>
        </div>

        <div class="flex flex-col gap-1">
            <div
                class="bg-white/[2%] border border-neutral-200 dark:border-neutral-800 rounded-md w-full p-5 uppercase text-sm text-center font-mono shadow-xs text-neutral-600 dark:text-neutral-400">
                <span class="text-neutral-400 dark:text-neutral-600">//
                </span>No queries executed
            </div>
        </div>

        <!-- Pagination Controls -->
        <div
            x-show="totalPages &gt; 1"
            class="flex items-center justify-center gap-1 py-4 font-mono"
            style="display: none;">
            <!-- First Button -->
            <button
                @click="first()"
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors text-neutral-600 cursor-not-allowed!"
                :disabled="!hasPrevious"
                :class="hasPrevious ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
                disabled="disabled">
                <svg
                    width="10"
                    height="10"
                    viewBox="0 0 10 10"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3 h-3">
                    <path
                        d="M4.75 1L0.75 5L4.75 9"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path
                        d="M9.25 1L5.25 5L9.25 9"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"></path>
                </svg>
            </button>

            <!-- Previous Button -->
            <button
                @click="previous()"
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors text-neutral-600 cursor-not-allowed!"
                :class="hasPrevious ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
                :disabled="!hasPrevious"
                disabled="disabled">
                <svg
                    width="6"
                    height="10"
                    viewBox="0 0 6 10"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3 h-3">
                    <path
                        d="M5.125 0.75L0.875 5L5.125 9.25"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"></path>
                </svg>
            </button>

            <!-- Page Numbers -->
            <template
                x-for="(page, index) in visiblePages"
                :key="`page-${page.type}-${page.value}-${page.id || index}`">
                <div>
                    <template x-if="page.type === 'ellipsis'">
                        <span class="flex items-center justify-center w-8 h-8 text-neutral-500">...</span>
                    </template>
                    <template x-if="page.type === 'page'">
                        <button
                            @click="goToPage(page.value)"
                            class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md text-sm font-medium transition-colors"
                            :class="currentPage === page.value ? 'bg-blue-600 text-white' : 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5'"
                            x-text="page.value"></button>
                    </template>
                </div>
            </template>

            <!-- Next Button -->
            <button
                @click="next()"
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors text-neutral-600 cursor-not-allowed!"
                :class="hasNext ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
                :disabled="!hasNext"
                disabled="disabled">
                <svg
                    width="6"
                    height="10"
                    viewBox="0 0 6 10"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3 h-3">
                    <path
                        d="M0.875 9.25L5.125 5L0.875 0.75"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"></path>
                </svg>
            </button>

            <!-- Last Button -->
            <button
                @click="last()"
                class="cursor-pointer flex items-center justify-center w-8 h-8 rounded-md transition-colors text-neutral-600 cursor-not-allowed!"
                :class="hasNext ? 'text-neutral-500 dark:text-neutral-300 hover:bg-neutral-200 hover:dark:text-white hover:dark:bg-white/5' : 'text-neutral-600 cursor-not-allowed!'"
                :disabled="!hasNext"
                disabled="disabled">
                <svg
                    width="10"
                    height="10"
                    viewBox="0 0 10 10"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    class="w-3 h-3">
                    <path
                        d="M5.25 9L9.25 5L5.25 1"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"></path>
                    <path
                        d="M0.75 9L4.75 5L0.75 1"
                        stroke="currentColor"
                        stroke-linecap="round"
                        stroke-linejoin="round"></path>
                </svg>
            </button>
        </div>
    </div>
</section>
        </div>

        <script src="/assets/debug/ignition-laravel.js"></script>

    </body>
</html>