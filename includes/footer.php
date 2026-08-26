<footer class="footer">
    <div class="container footer-container">
        <div class="footer-brand">
            <div class="logo">
            <span class="logo-icon">
                    <img src="<?= $basePath ?>assets/uploads/logo.png" alt="">
            </span>
                MERDEKA COMMUNITY
            </div>
            <p>Platform sosial untuk mengubah semangat kemerdekaan Republik Indonesia menjadi aksi nyata.</p>
        </div>
        <div class="footer-links">
            <h4>Jelajahi</h4>
            <a href="<?= $basePath ?>index.php#aksi">Pilih Aksi</a>
            <a href="<?= $basePath ?>pages/peta.php">Peta</a>
            <a href="<?= $basePath ?>index.php#progress">Progress</a>
            <a href="<?= $basePath ?>index.php#event">Event</a>
            <a href="<?= $basePath ?>index.php#cerita">Cerita Mereka</a>
        </div>
        <div class="footer-links">
            <h4>Bergabung</h4>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="<?= $basePath ?>admin/index.php">Dashboard Admin</a>
                <?php endif; ?>
                <a href="<?= $basePath ?>dashboard.php">Dashboard</a>
                <a href="<?= $basePath ?>logout.php">Keluar</a>
            <?php else: ?>
                <a href="<?= $basePath ?>login.php">Masuk</a>
                <a href="<?= $basePath ?>register.php">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>© <?= date('Y') ?> MERDEKA COMMUNITY. Dibuat untuk Indonesia </p>
        </div>
    </div>
</footer>
<script src="<?= $basePath ?>assets/js/icons.js"></script>
</body>
</html>
