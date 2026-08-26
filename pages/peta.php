<?php

session_start();

require_once "../config/database.php";


// =====================================================
// DATA WILAYAH
// =====================================================

$wilayahList = [

    'Sumatera',
    'Jawa',
    'Kalimantan',
    'Sulawesi',
    'Bali & Nusa Tenggara',
    'Maluku',
    'Papua'

];

$wilayahDipilih = $_GET['wilayah'] ?? 'Sumatera';
if (!in_array($wilayahDipilih, $wilayahList, true)) {
    $wilayahDipilih = '';
}
$lokasiPeta = $wilayahDipilih !== ''
    ? $wilayahDipilih . ', Indonesia'
    : 'Indonesia';


// =====================================================
// QUERY AKSI PER WILAYAH
// =====================================================

$queryWilayah = mysqli_query(
    $conn,
    "SELECT
        wilayah,
        COUNT(*) AS total
     FROM aksi_user
     WHERE status = 'disetujui'
     GROUP BY wilayah
     ORDER BY total DESC"
);


$dataWilayah = [];

while (
    $row =
        mysqli_fetch_assoc(
            $queryWilayah
        )
) {

    $dataWilayah[
        $row['wilayah']
    ] = (int) $row['total'];

}


// =====================================================
// TOTAL AKSI
// =====================================================

$totalAksi = array_sum(
    $dataWilayah
);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Peta Aksi Indonesia — Aksi Untuk Negeri
    </title>

    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    

    <link rel="stylesheet" href="../assets/css/pages.css">
</head>

<body class="map-page">
<?php $basePath = '../'; require __DIR__ . '/../includes/navbar.php'; ?>
<?php if (false): ?>


<header class="navbar">

    <div class="container nav-container">

        <a
            href="../index.php"
            class="logo"
        >

            <span class="logo-icon">
                🇮🇩
            </span>

            Aksi Untuk Negeri

        </a>


        <nav class="nav-menu">

            <a href="../index.php">
                Beranda
            </a>

            <a href="aksi.php">
                Pilih Aksi
            </a>

            <a href="progress.php">
                Progress
            </a>

            <a href="peta.php">
                Peta Aksi
            </a>

            <a href="about.php">
                Tentang
            </a>

            <a href="faq.php">
                FAQ
            </a>

            <a href="contact.php">
                Kontak
            </a>

            <a href="cerita.php">
                Cerita Mereka
            </a>

        </nav>


        <div class="nav-button">

            <?php if (
                isset($_SESSION['user_id'])
            ): ?>

                <a
                    href="../dashboard.php"
                    class="btn btn-primary"
                >
                    Dashboard
                </a>

            <?php else: ?>

                <a
                    href="../login.php"
                    class="btn btn-outline"
                >
                    Masuk
                </a>

            <?php endif; ?>

        </div>

    </div>

</header>
<?php endif; ?>


<main class="map-main" id="peta">

    <div class="container">


        <div class="map-heading">

            <h1>

                Aksi dari
                <span>
                    Seluruh Negeri.
                </span>

            </h1>

            <p>

                    <?= $wilayahDipilih !== ''
                        ? 'Menampilkan peta wilayah ' . htmlspecialchars($wilayahDipilih)
                        : 'Setiap titik kontribusi menunjukkan' ?>
                    <?php if ($wilayahDipilih === ''): ?>
                semangat masyarakat Indonesia untuk
                melakukan perubahan dari daerahnya
                masing-masing.
                    <?php endif; ?>

            </p>

        </div>

        <!-- SUMMARY -->

        <div class="map-summary">
            <div class="summary-card">
                <strong><?= count(array_filter($dataWilayah, fn($jumlah) => $jumlah > 0)) ?></strong>
                <span>Wilayah sudah berkontribusi</span>
            </div>

            <div class="summary-card">
                <strong><?= number_format($totalAksi, 0, ',', '.') ?></strong>
                <span>Total aksi disetujui</span>
            </div>
        </div>


        <div class="map-wrapper">


            <!-- MAP -->

            <div class="map-card">

                <iframe
                    class="google-map-embed"
                    src="https://www.google.com/maps?q=<?= urlencode($lokasiPeta) ?>&output=embed"
                    title="Peta Google <?= htmlspecialchars($lokasiPeta) ?>"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </div>


            <!-- REGIONS -->

            <div class="region-list">

                <?php
                $maxAksi = !empty($dataWilayah) ? max($dataWilayah) : 1;
                ?>

                <?php foreach ($wilayahList as $wilayah): ?>
                    <?php
                    $jumlah = $dataWilayah[$wilayah] ?? 0;
                    $persentase = $totalAksi > 0 ? ($jumlah / $totalAksi) * 100 : 0;
                    $barWidth = ($jumlah / $maxAksi) * 100;
                    $iconWilayah = match ($wilayah) {
                        'Sumatera' => '🌴',
                        'Jawa' => '🏙️',
                        'Kalimantan' => '🌳',
                        'Sulawesi' => '🌊',
                        'Bali & Nusa Tenggara' => '🏝️',
                        'Maluku' => '🌊',
                        'Papua' => '🏔️',
                        default => ''
                    };
                    ?>

                    <div class="region-item">
                        <a class="region-top" href="peta.php?<?= http_build_query(['wilayah' => $wilayah]) ?>#peta">
                            <span class="region-name">
                                <?= $iconWilayah ?>
                                <?= htmlspecialchars($wilayah) ?>
                            </span>

                            <span class="region-total">
                                <?= number_format($jumlah, 0, ',', '.') ?>
                            </span>
                        </a>

                        <div class="region-bar">
                            <div class="region-fill" style="width: <?= $barWidth ?>%;"></div>
                        </div>

                        <span class="region-percent">
                            <?= number_format($persentase, 1) ?>% dari seluruh aksi
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>

</main>


<?php if (false): ?>
<footer class="footer">

    <div class="container footer-container">

        <div class="footer-brand">

            <div class="logo">

                <span class="logo-icon">
                    🇮🇩
                </span>

                Aksi Untuk Negeri

            </div>


            <p>
                Platform kampanye sosial untuk
                mengubah semangat kemerdekaan
                menjadi aksi nyata.
            </p>

        </div>


        <div class="footer-links">

            <h4>
                Jelajahi
            </h4>

            <a href="../index.php#aksi">
                Pilih Aksi
            </a>

            <a href="../index.php#progress">
                Progress
            </a>

            <a href="../index.php#tantangan">
                17 Hari
            </a>

            <a href="../index.php#cerita">
                Cerita Mereka
            </a>

            <a href="about.php">
                Tentang
            </a>

            <a href="faq.php">
                FAQ
            </a>

            <a href="contact.php">
                Kontak
            </a>

        </div>


        <div class="footer-links">

            <h4>
                Bergabung
            </h4>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="../admin/index.php">
                        Dashboard Admin
                    </a>
                <?php endif; ?>

                <a href="../dashboard.php">
                    Dashboard
                </a>

                <a href="../logout.php">
                    Keluar
                </a>
            <?php else: ?>
                <a href="../login.php">
                    Masuk
                </a>

                <a href="../register.php">
                    Daftar
                </a>
            <?php endif; ?>

        </div>

    </div>


    <div class="footer-bottom">

        <div class="container">

            <p>
                © <?= date('Y') ?>
                Aksi Untuk Negeri.
                Dibuat untuk Indonesia 🇮🇩
            </p>

        </div>

    </div>

</footer>
<?php endif; require __DIR__ . '/../includes/footer.php'; ?>


<script src="../assets/js/icons.js"></script>
</body>

</html>
