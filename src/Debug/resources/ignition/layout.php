<?php include __DIR__ . '/header.php'; ?>

<!-- Garis pemisah -->
<div class="container-fluid p-0">
    <hr class="border border-secondary border-opacity-25 m-0">
</div>

<div class="container">

    <!-- Exception Summary -->
    <?php include __DIR__ . '/exception-summary.php'; ?>
    <hr class="border border-secondary border-opacity-25">

    <!-- Trace List -->
    <?php include __DIR__ . '/trace-list.php'; ?>
    <hr class="border border-secondary border-opacity-25">

    <!-- Database Queries -->
    <?php include __DIR__ . '/queries.php'; ?>
    <hr class="border border-secondary border-opacity-25">

    <!-- Server Header -->
    <?php include __DIR__ . '/header-server.php'; ?>
    <hr class="border border-secondary border-opacity-25">

</div>

<?php include __DIR__ . '/footer.php'; ?>
