<?php

session_start();

require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| Ambil kategori dari URL
|--------------------------------------------------------------------------
*/

$kategoriId = isset($_GET['kategori'])
    ? (int) $_GET['kategori']
    : 0;

$kataKunci = trim($_GET['q'] ?? '');
$tingkatDipilih = $_GET['tingkat'] ?? '';
$tingkatValid = ['Mudah', 'Sedang', 'Sulit'];
$wilayahValid = ['Sumatera', 'Jawa', 'Kalimantan', 'Sulawesi', 'Bali & Nusa Tenggara', 'Maluku', 'Papua'];
$wilayahDipilih = $_GET['wilayah'] ?? '';

if (!in_array($wilayahDipilih, $wilayahValid, true)) {
    $wilayahDipilih = '';
}

if (!in_array($tingkatDipilih, $tingkatValid, true)) {
    $tingkatDipilih = '';
}


/*
|--------------------------------------------------------------------------
| Ambil semua kategori
|--------------------------------------------------------------------------
*/

$queryKategori = mysqli_query(
    $conn,
    "SELECT * FROM kategori ORDER BY id ASC"
);


/*
|--------------------------------------------------------------------------
| Ambil kategori yang dipilih
|--------------------------------------------------------------------------
*/

$kategoriDipilih = null;

if ($kategoriId > 0) {

    $stmtKategori = mysqli_prepare(
        $conn,
        "SELECT * FROM kategori WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $stmtKategori,
        "i",
        $kategoriId
    );

    mysqli_stmt_execute($stmtKategori);

    $resultKategori =
        mysqli_stmt_get_result($stmtKategori);

    $kategoriDipilih =
        mysqli_fetch_assoc($resultKategori);
}


/*
|--------------------------------------------------------------------------
| Ambil daftar aksi
|--------------------------------------------------------------------------
*/

$conditions = [];
$params = [];
$types = '';

if ($kategoriId > 0) {
    $conditions[] = 'aksi.kategori_id = ?';
    $params[] = $kategoriId;
    $types .= 'i';
}

if ($kataKunci !== '') {
    $conditions[] = '(aksi.nama_aksi LIKE ? OR aksi.deskripsi LIKE ?)';
    $searchValue = '%' . $kataKunci . '%';
    $params[] = $searchValue;
    $params[] = $searchValue;
    $types .= 'ss';
}

if ($tingkatDipilih !== '') {
    $conditions[] = 'aksi.tingkat_kesulitan = ?';
    $params[] = $tingkatDipilih;
    $types .= 's';
}

$querySql = "SELECT
                aksi.*,
                kategori.nama_kategori,
                kategori.icon,
                kategori.sdg,
                (
                    SELECT COUNT(*)
                    FROM aksi_user
                    WHERE aksi_user.aksi_id = aksi.id
                    AND aksi_user.status = 'disetujui'
                ) AS total_peserta
             FROM aksi
             INNER JOIN kategori
                ON aksi.kategori_id = kategori.id";

if ($conditions) {
    $querySql .= ' WHERE ' . implode(' AND ', $conditions);
}

$querySql .= ' ORDER BY aksi.id ASC';

$stmtAksi = mysqli_prepare($conn, $querySql);

if ($params) {
    $bindParams = [$stmtAksi, $types];

    foreach ($params as $index => $param) {
        $bindParams[] = &$params[$index];
    }

    call_user_func_array('mysqli_stmt_bind_param', $bindParams);
}

mysqli_stmt_execute($stmtAksi);
$queryAksi = mysqli_stmt_get_result($stmtAksi);


/*
|--------------------------------------------------------------------------
| Hitung total aksi
|--------------------------------------------------------------------------
*/

$totalAksi = mysqli_num_rows($queryAksi);

$queryWilayah = mysqli_query(
    $conn,
    "SELECT wilayah, COUNT(*) AS total
     FROM aksi_user
     WHERE status = 'disetujui' AND wilayah IS NOT NULL AND wilayah != ''
     GROUP BY wilayah
     ORDER BY total DESC"
);

$dataWilayah = [];
while ($row = mysqli_fetch_assoc($queryWilayah)) {
    $dataWilayah[$row['wilayah']] = (int) $row['total'];
}

$aksiWilayah = [];
if ($wilayahDipilih !== '') {
    $stmtWilayah = mysqli_prepare(
        $conn,
        "SELECT aksi.id, aksi.nama_aksi, kategori.icon AS kategori_icon,
                COUNT(aksi_user.id) AS total_peserta
         FROM aksi_user
         INNER JOIN aksi ON aksi.id = aksi_user.aksi_id
         INNER JOIN kategori ON kategori.id = aksi.kategori_id
         WHERE aksi_user.status = 'disetujui' AND aksi_user.wilayah = ?
         GROUP BY aksi.id, aksi.nama_aksi, kategori.icon
         ORDER BY total_peserta DESC, aksi.nama_aksi ASC"
    );
    mysqli_stmt_bind_param($stmtWilayah, 's', $wilayahDipilih);
    mysqli_stmt_execute($stmtWilayah);
    $resultWilayah = mysqli_stmt_get_result($stmtWilayah);

    while ($row = mysqli_fetch_assoc($resultWilayah)) {
        $aksiWilayah[] = $row;
    }
}

$aksiIcons = [
    1 => '&#128218;',
    2 => '&#129330;',
    3 => '&#128187;',
    4 => '&#10084;&#65039;',
    5 => '&#128167;',
    6 => '&#127793;',
    7 => '&#127795;',
    8 => '&#9851;&#65039;',
    9 => '&#128188;',
    10 => '&#127758;',
    11 => '&#129309;',
    12 => '&#127919;',
    13 => '&#127758;',
    14 => '&#128737;&#65039;',
    15 => '&#128200;',
    16 => '&#127963;&#65039;',
    17 => '&#128161;',
];

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="Pilih aksi nyata untuk Indonesia melalui Semangat Kemerdekaan."
    >

    <title>
        Aksi Indonesia — Aksi Untuk Negeri
    </title>

    <link rel="icon" type="image/png" href="../assets/uploads/logo.png">

    <link
        rel="stylesheet"
        href="../assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>"
    >

    

    <link rel="stylesheet" href="../assets/css/pages.css">
</head>


<body class="aksi-page">
<?php $basePath = '../'; require __DIR__ . '/../includes/navbar.php'; ?>
<?php if (false): ?>


<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="container nav-container">

                                <span
                                    class="aksi-difficulty-badge <?= $difficultyClass ?>"
                                >
                                    <?= htmlspecialchars($aksi['tingkat_kesulitan']) ?>
                                </span>
                Aksi Untuk Negeri
            </span>

        </a>


        <nav class="nav-menu">

            <a href="../index.php#beranda">
                Beranda
            </a>

            <a href="aksi.php">
                Pilih Aksi
            </a>

            <a href="progress.php">
                Progress
            </a>

            <a href="tantangan.php">
                17 Hari
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

            <?php if (isset($_SESSION['user_id'])): ?>
                <a
                    href="../dashboard.php"
                    class="btn btn-outline"
                >
                    Dashboard
                </a>

                <a
                    href="../logout.php"
                    class="btn btn-primary"
                >
                    Keluar
                </a>
            <?php else: ?>
                <a
                    href="../login.php"
                    class="btn btn-outline"
                >
                    Masuk
                </a>

                <a
                    href="../register.php"
                    class="btn btn-primary"
                >
                    Gabung
                </a>
            <?php endif; ?>

        </div>

    </div>

</header>
<?php endif; ?>


<!-- =====================================================
     HERO
===================================================== -->

<section class="aksi-hero">

    <div class="container">

        <div class="aksi-hero-content">

            <?php if ($kategoriDipilih): ?>

                <h1>

                    Aksi untuk
                    <span>
                        <?= htmlspecialchars(
                            $kategoriDipilih['nama_kategori']
                        ) ?>
                    </span>

                </h1>


                <p>

                    <?= htmlspecialchars(
                        $kategoriDipilih['deskripsi']
                    ) ?>

                    Pilih aksi yang dapat kamu lakukan
                    dan jadilah bagian dari perubahan
                    untuk Indonesia.

                </p>

            <?php else: ?>

                <h1>

                    Temukan Aksi,
                    <span>Berikan Dampak</span>

                </h1>


                <p>

                    Temukan aksi berdasarkan bidang dan lokasi,
                    lalu ikut bergerak bersama komunitas di seluruh Indonesia.

                </p>

            <?php endif; ?>

        </div>

        <form method="get" action="aksi.php#kategori-filter" class="aksi-search">
            <?php if ($kategoriId > 0): ?>
                <input type="hidden" name="kategori" value="<?= $kategoriId ?>">
            <?php endif; ?>
            <input
                type="search"
                name="q"
                value="<?= htmlspecialchars($kataKunci) ?>"
                placeholder="Cari nama atau deskripsi aksi..."
                aria-label="Cari aksi"
            >
            <div class="aksi-select-wrap">
                <select
                    name="tingkat"
                    class="aksi-level-select"
                    aria-label="Filter tingkat kesulitan"
                >
                    <option value="">Semua tingkat</option>
                    <?php foreach ($tingkatValid as $tingkat): ?>
                        <option value="<?= $tingkat ?>" <?= $tingkatDipilih === $tingkat ? 'selected' : '' ?>>
                            <?= $tingkat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit">Cari Aksi</button>
        </form>

    </div>

</section>


<!-- =====================================================
     DAFTAR AKSI
===================================================== -->

<section class="aksi-list-section">

    <div class="container">

        <div class="aksi-list-header">

            <div>

                <h2>
                    Temukan Aksimu
                </h2>


                <p>
                    Pilih aksi yang ingin kamu lakukan
                    untuk memberikan dampak.
                </p>

            </div>


            <div class="aksi-count">

                <strong>
                    <?= $totalAksi ?>
                </strong>

                aksi tersedia

            </div>

        </div>
                <div id="kategori-filter" class="kategori-filter-list">

            <a
                href="aksi.php#kategori-filter"
                class="kategori-filter-item
                <?= $kategoriId === 0
                    ? 'active'
                    : '' ?>"
            >
                Semua Aksi
            </a>

            <?php
            mysqli_data_seek($queryKategori, 0);
            ?>

            <?php while ($kategori = mysqli_fetch_assoc($queryKategori)): ?>
                <a
                    href="aksi.php?kategori=<?= $kategori['id'] ?>#kategori-filter"
                    class="kategori-filter-item
                    <?= $kategoriId === (int) $kategori['id']
                        ? 'active'
                        : '' ?>"
                >
                    <span class="kategori-filter-icon" aria-hidden="true">
                        <?= htmlspecialchars($kategori['icon']) ?>
                    </span>
                    <?= htmlspecialchars($kategori['nama_kategori']) ?>
                </a>
            <?php endwhile; ?>

        </div>

        <?php if ($totalAksi > 0): ?>

            <div class="aksi-grid">


                <?php while (
                    $aksi = mysqli_fetch_assoc(
                        $queryAksi
                    )
                ): ?>

                    <article class="aksi-card">


                        <div class="aksi-card-top">

                            <div class="aksi-icon">

                                <span aria-hidden="true">
                                    <?= $aksiIcons[(int) $aksi['id']]
                                        ?? htmlspecialchars($aksi['icon']) ?>
                                </span>

                            </div>


                            <div class="aksi-poin">

                                +<?= $aksi['poin'] ?>
                                Poin

                            </div>

                        </div>


                        <h3>

                            <?= htmlspecialchars(
                                $aksi['nama_aksi']
                            ) ?>

                        </h3>


                        <p class="aksi-card-description">

                            <?= htmlspecialchars(
                                $aksi['deskripsi']
                            ) ?>

                        </p>


                        <div class="aksi-category">

                            <span class="aksi-category-badge">
                                <span class="aksi-category-icon" aria-hidden="true">
                                    <?= htmlspecialchars($aksi['icon']) ?>
                                </span>
                                <?= htmlspecialchars($aksi['nama_kategori']) ?>
                            </span>

                        </div>

                        <div class="aksi-participants">
                            <strong><?= number_format((int) $aksi['total_peserta'], 0, ',', '.') ?></strong> peserta telah ikut
                        </div>


                        <div class="aksi-footer">


                            <div class="aksi-difficulty <?= strtolower($aksi['tingkat_kesulitan']) ?>">

                                <?= htmlspecialchars(
                                    $aksi[
                                        'tingkat_kesulitan'
                                    ]
                                ) ?>

                            </div>


                            <a
                            href="<?= isset($_SESSION['user_id'])
                                ? '../lakukan-aksi.php?aksi=' . $aksi['id']
                                : '../login.php?aksi=' . $aksi['id'] ?>"
                            class="aksi-button"
                        >
                            Lakukan Aksi
                        </a>

                        </div>


                    </article>

                <?php endwhile; ?>


            </div>

        <?php else: ?>


            <div class="empty-state">

                <div class="empty-state-icon">
                    <span class="empty-state-icon-mark" aria-hidden="true">?</span>
                </div>


                <h3>
                    Belum Ada Aksi
                </h3>


                <p>
                    Belum ada aksi yang tersedia
                    pada kategori ini.
                </p>


                <a
                    href="aksi.php"
                    class="btn btn-primary"
                    style="margin-top: 20px;"
                >
                    Lihat Semua Aksi
                </a>

            </div>


        <?php endif; ?>


    </div>

</section>

<!-- 
<section class="aksi-map-section" id="peta-aksi">
    <div class="container">
        <div class="aksi-map-heading">
            <h2>Lihat aksi yang sedang berlangsung di Indonesia</h2>
            <p>Pilih wilayah untuk menemukan aksi yang sedang dilakukan komunitas di sana.</p>
        </div>

        <div class="aksi-map-layout">
            <div class="aksi-region-panel">
                <div class="aksi-region-panel-heading">
                    <strong>Temukan berdasarkan lokasi</strong>
                    <span><?= number_format(array_sum($dataWilayah), 0, ',', '.') ?> aksi disetujui</span>
                </div>

                <div class="aksi-region-grid">
                    <?php foreach ($wilayahValid as $wilayah): ?>
                        <?php $jumlahWilayah = $dataWilayah[$wilayah] ?? 0; ?>
                        <a
                            href="peta.php?<?= http_build_query(['wilayah' => $wilayah]) ?>#peta"
                            class="aksi-region-item <?= $wilayahDipilih === $wilayah ? 'active' : '' ?>"
                        >
                            <span class="aksi-region-icon" aria-hidden="true">●</span>
                            <span>
                                <strong><?= htmlspecialchars($wilayah) ?></strong>
                                <small><?= number_format($jumlahWilayah, 0, ',', '.') ?> aksi</small>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="aksi-location-results">
                <?php if ($wilayahDipilih !== ''): ?>
                    <div class="aksi-location-heading">
                        <span class="section-label">AKSI DI WILAYAH TERPILIH</span>
                        <h3>Aksi di <?= htmlspecialchars($wilayahDipilih) ?></h3>
                    </div>

                    <?php if ($aksiWilayah): ?>
                        <div class="aksi-location-list">
                            <?php foreach ($aksiWilayah as $aksiLokasi): ?>
                                <div class="aksi-location-item">
                                    <span class="aksi-location-icon" aria-hidden="true"><?= htmlspecialchars($aksiLokasi['kategori_icon'] ?: '●') ?></span>
                                    <div>
                                        <strong><?= htmlspecialchars($aksiLokasi['nama_aksi']) ?></strong>
                                        <small><?= number_format((int) $aksiLokasi['total_peserta'], 0, ',', '.') ?> peserta</small>
                                    </div>
                                    <a
                                        href="<?= isset($_SESSION['user_id']) ? '../lakukan-aksi.php?aksi=' . (int) $aksiLokasi['id'] : '../login.php?aksi=' . (int) $aksiLokasi['id'] ?>"
                                        class="aksi-button"
                                    >Ikut Aksi</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="aksi-location-empty">Belum ada aksi disetujui di wilayah ini. Pilih wilayah lain atau mulai aksi baru.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="aksi-location-empty aksi-location-empty-prompt">
                        <span aria-hidden="true">●</span>
                        <strong>Pilih wilayah untuk melihat aksi di sekitarnya</strong>
                        <p>Dari kategori ke lokasi, temukan aksi yang paling dekat denganmu.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section> -->

<!-- =====================================================
     CTA
===================================================== -->
<!-- 
<section class="cta-section">

    <div class="container">

        <div class="cta-content">

            <span>
                 SATU AKSI HARI INI
            </span>


            <h2>
                Jadilah Bagian
                dari Perubahan.
            </h2>


            <p>
                Indonesia tidak hanya dibangun
                oleh mereka yang berkuasa,
                tetapi juga oleh mereka yang mau
                bergerak.
            </p>


            <a
                href="../register.php"
                class="btn btn-white btn-large"
            >
                Gabung Sekarang
            </a>

        </div>

    </div>

</section>
 -->

<!-- =====================================================
     FOOTER
===================================================== -->

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

            <a href="../login.php">
                Masuk
            </a>

            <a href="../register.php">
                Daftar
            </a>

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


<script src="../assets/js/aksi-select.js"></script>
<script src="../assets/js/icons.js?v=3"></script>
</body>

</html>
