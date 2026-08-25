<?php
$basePath = $basePath ?? '';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
$isHome = $currentPage === 'index.php';
$isAction = $currentPage === 'aksi.php';
$isProgress = $currentPage === 'progress.php';
$isEvent = $currentPage === 'event.php' || $currentPage === 'tantangan.php';
$isLeaderboard = $currentPage === 'leaderboard.php';
$isAbout = $currentPage === 'about.php' || $currentPage === 'cerita.php';
$isHelp = $currentPage === 'faq.php' || $currentPage === 'contact.php';
?>
<header class="navbar">
    <div class="container nav-container">
        <a href="<?= $basePath ?>index.php" class="logo">
            <span class="logo-icon"></span>
            Aksi Untuk Negeri
        </a>
        <nav class="nav-menu">
            <a href="<?= $basePath ?>index.php" class="<?= $isHome ? 'active' : '' ?>" <?= $isHome ? 'aria-current="page"' : '' ?>>Beranda</a>
            <a href="<?= $basePath ?>pages/aksi.php" class="<?= $isAction ? 'active' : '' ?>" <?= $isAction ? 'aria-current="page"' : '' ?>>Aksi</a>
            <a href="<?= $basePath ?>pages/progress.php" class="<?= $isProgress ? 'active' : '' ?>" <?= $isProgress ? 'aria-current="page"' : '' ?>>Progress</a>
            <a href="<?= $basePath ?>pages/event.php" class="<?= $isEvent ? 'active' : '' ?>" <?= $isEvent ? 'aria-current="page"' : '' ?>>Event</a>
            <a href="<?= $basePath ?>pages/leaderboard.php" class="<?= $isLeaderboard ? 'active' : '' ?>" <?= $isLeaderboard ? 'aria-current="page"' : '' ?>>Leaderboard</a>
            <details class="nav-dropdown" <?= $isAbout ? 'open' : '' ?>>
                <summary class="<?= $isAbout ? 'active' : '' ?>">Tentang <span aria-hidden="true">▾</span></summary>
                <div class="nav-dropdown-menu">
                    <a href="<?= $basePath ?>pages/about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">Tentang Kami</a>
                    <a href="<?= $basePath ?>pages/cerita.php" class="<?= $currentPage === 'cerita.php' ? 'active' : '' ?>">Cerita Mereka</a>
                </div>
            </details>
            <details class="nav-dropdown" <?= $isHelp ? 'open' : '' ?>>
                <summary class="<?= $isHelp ? 'active' : '' ?>">Bantuan <span aria-hidden="true">▾</span></summary>
                <div class="nav-dropdown-menu">
                    <a href="<?= $basePath ?>pages/faq.php" class="<?= $currentPage === 'faq.php' ? 'active' : '' ?>">FAQ</a>
                    <a href="<?= $basePath ?>pages/contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">Kontak</a>
                </div>
            </details>
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
