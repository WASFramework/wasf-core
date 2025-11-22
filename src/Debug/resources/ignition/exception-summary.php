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

// fallback jika tidak ketemu
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


<section
    class="w-full max-w-7xl mx-auto p-4 sm:p-14 border-x border-dashed border-neutral-300 dark:border-white/[9%] flex flex-col gap-8 py-0 sm:py-0">
    <div class="flex flex-col pt-8 sm:pt-16 overflow-x-auto">
        <div class="flex flex-col gap-5 mb-8">
            <h1 class="text-3xl font-semibold text-neutral-950 dark:text-white"><?= htmlspecialchars($exceptionClass) ?></h1>
            <div
                class="truncate font-mono text-xs text-neutral-500 dark:text-neutral-400 -mt-3 text-xs"
                dir="ltr">
                <span data-tippy-content="<?= htmlspecialchars($filePath . ':' . $fileLine) ?>">
                    <?= htmlspecialchars($filePath) ?>:<?= $fileLine ?>
                </span>
            </div>

            <p class="text-xl font-light text-neutral-800 dark:text-neutral-300">
                <?= htmlspecialchars($exceptionMessage) ?>
            </p>
        </div>

        <!-- VERSION + TAGS -->
        <div class="flex items-start gap-2 mb-8 sm:mb-16">
            <div
                class="bg-white dark:bg-white/[3%] border border-neutral-200 dark:border-white/10 divide-x divide-neutral-200 dark:divide-white/10 rounded-md shadow-xs flex items-center gap-0.5">

                <div class="flex items-center gap-1.5 h-6 px-[6px] font-mono text-[13px]">
                    <span class="text-neutral-400 dark:text-neutral-500">WASF</span>
                    <span class="text-neutral-500 dark:text-neutral-300"><?= $wasfVersion ?></span>
                </div>

                <div class="flex items-center gap-1.5 h-6 px-[6px] font-mono text-[13px]">
                    <span class="text-neutral-400 dark:text-neutral-500">PHP</span>
                    <span class="text-neutral-500 dark:text-neutral-300"><?= $phpVersion ?></span>
                </div>
            </div>

            <div
                class="inline-flex w-fit shrink-0 items-center justify-center gap-1 font-mono leading-3 uppercase transition-colors dark:border [&_svg]:size-2.5 h-6 min-w-5 rounded-md px-1.5 text-xs/none bg-blue-200 text-white dark:border-blue-900 dark:bg-blue-950 dark:text-blue-100 [&_svg]:!text-white">
                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" class="w-2.5 h-2.5">
                    <path
                        d="M9.874 7.828L5.926 0.55C5.83 0.369 5.68 0.222 5.5 0.125C5.254 -0.007 4.966 -0.036 4.698 0.044C4.431 0.123 4.206 0.305 4.072 0.55L0.125 7.828C0.038 7.989 -0.005 8.169 0 8.351C0.004 8.533 0.055 8.71 0.149 8.866C0.243 9.022 0.375 9.151 0.534 9.24C0.692 9.33 0.871 9.376 1.053 9.375H8.946C9.12 9.375 9.292 9.332 9.446 9.25C9.568 9.185 9.676 9.097 9.764 8.99C9.851 8.884 9.917 8.761 9.957 8.629C9.997 8.497 10.01 8.358 9.996 8.221C9.982 8.083 9.94 7.95 9.874 7.828Z"
                        fill="currentColor"></path>
                </svg>
                UNHANDLED
            </div>

            <div
                class="inline-flex w-fit shrink-0 items-center justify-center gap-1 font-mono leading-3 uppercase h-6 min-w-5 rounded-md px-1.5 text-xs bg-blue-600 text-white dark:bg-blue-600 [&_svg]:!text-white">
                CODE <?= $statusCode ?>
            </div>
        </div>

        <!-- REQUEST INFO -->
        <div
            x-data="{
                copied: false,
                async copyToClipboard() {
                    try {
                        await navigator.clipboard.writeText('<?= htmlspecialchars($requestFullUrl) ?>');
                        this.copied = true;
                        setTimeout(() => { this.copied = false }, 2000);
                    } catch (err) { console.error(err); }
                }
            }"
            class="bg-white dark:bg-[#1a1a1a] border border-neutral-200 dark:border-white/10 rounded-lg flex items-center justify-between h-10 px-2 shadow-xs relative z-50">

            <div class="flex items-center gap-3 w-full">
                <!-- STATUS -->
                <div
                    class="inline-flex items-center gap-1 font-mono uppercase h-6 rounded-md px-1.5 text-xs bg-blue-600 text-white">
                    <?= $statusCode ?>
                </div>

                <!-- METHOD -->
                <div
                    class="inline-flex items-center gap-1 font-mono uppercase h-6 rounded-md px-1.5 text-xs bg-black/8 text-neutral-900 dark:bg-white/10 dark:text-neutral-100">
                    <?= htmlspecialchars($requestMethod) ?>
                </div>

                <!-- URL -->
                <div class="flex-1 text-sm font-light truncate text-neutral-950 dark:text-white">
                    <?= htmlspecialchars($requestFullUrl) ?>
                </div>

                <!-- COPY BUTTON -->
                <button @click="copyToClipboard()"
                        class="rounded-md w-6 h-6 flex items-center justify-center cursor-pointer border bg-white/5 border-neutral-200 dark:bg-white/5 dark:border-white/10 hover:bg-neutral-100 dark:hover:bg-white/10">
                    <span x-show="!copied">📋</span>
                    <span x-show="copied" style="display:none;">✔</span>
                </button>
            </div>
        </div>

    </div>
</section>
