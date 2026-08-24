<?php
$basePath = $basePath ?? '';
?>
<header class="navbar">
    <div class="container nav-container">
        <a href="<?= $basePath ?>index.php" class="logo">
            <span class="logo-icon"></span>
            Aksi Untuk Negeri
        </a>
        <nav class="nav-menu">
            <a href="<?= $basePath ?>index.php">Beranda</a>
            <a href="<?= $basePath ?>pages/aksi.php">Pilih Aksi</a>
            <a href="<?= $basePath ?>pages/progress.php">Progress</a>
            <a href="<?= $basePath ?>pages/peta.php">Peta Aksi</a>
            <a href="<?= $basePath ?>pages/event.php">Event</a>
            <a href="<?= $basePath ?>pages/leaderboard.php">Leaderboard</a>
        </nav>
        <div class="nav-button">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="<?= $basePath ?>admin/index.php" class="btn btn-primary">Dashboard Admin</a>
                <?php else: ?>
                    <a href="<?= $basePath ?>dashboard.php" class="btn btn-primary">Dashboard</a>
                <?php endif; ?>
                <a href="<?= $basePath ?>logout.php" class="btn btn-outline">Keluar</a>
            <?php else: ?>
                <a href="<?= $basePath ?>login.php" class="btn btn-outline">Masuk</a>
                <a href="<?= $basePath ?>register.php" class="btn btn-primary">Gabung</a>
            <?php endif; ?>
        </div>
    </div>
</header>
