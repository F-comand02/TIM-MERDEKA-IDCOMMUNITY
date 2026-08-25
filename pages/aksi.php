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
                kategori.sdg
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
        Pilih Aksi — Aksi Untuk Negeri
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

    

    <link rel="stylesheet" href="../assets/css/pages.css">
</head>


<body class="aksi-page">


<!-- =====================================================
     NAVBAR
===================================================== -->

<header class="navbar">

    <div class="container nav-container">

        <a
            href="../index.php"
            class="logo"
        >

            <span class="logo-icon">
                🇮🇩
            </span>

            <span>
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


<!-- =====================================================
     HERO
===================================================== -->

<section class="aksi-hero">

    <div class="container">

        <div class="aksi-hero-content">

            <div class="aksi-badge">
                🔥 PILIH AKSIMU
            </div>


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

                    Setiap Aksi
                    <span>
                        Berarti.
                    </span>

                </h1>


                <p>

                    Pilih bidang yang paling dekat
                    denganmu. Temukan aksi sederhana
                    yang dapat kamu lakukan untuk
                    memberikan dampak nyata.

                </p>

            <?php endif; ?>

        </div>

        <form method="get" class="aksi-search">
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
            <select name="tingkat" aria-label="Filter tingkat kesulitan">
                <option value="">Semua tingkat</option>
                <?php foreach ($tingkatValid as $tingkat): ?>
                    <option value="<?= $tingkat ?>" <?= $tingkatDipilih === $tingkat ? 'selected' : '' ?>>
                        <?= $tingkat ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Cari Aksi</button>
        </form>

    </div>

</section>


<!-- =====================================================
     FILTER KATEGORI
===================================================== -->

<section class="kategori-filter">

    <div class="container">

        <div class="kategori-filter-list">

            <a
                href="aksi.php"
                class="kategori-filter-item
                <?= $kategoriId === 0
                    ? 'active'
                    : '' ?>"
            >
                🔥 Semua Aksi
            </a>


            <?php

            mysqli_data_seek(
                $queryKategori,
                0
            );

            ?>


            <?php while (
                $kategori = mysqli_fetch_assoc(
                    $queryKategori
                )
            ): ?>

                <a
                    href="aksi.php?kategori=<?= $kategori['id'] ?>"
                    class="kategori-filter-item
                    <?= $kategoriId === (int) $kategori['id']
                        ? 'active'
                        : '' ?>"
                >

                    <?= htmlspecialchars(
                        $kategori['icon']
                    ) ?>

                    <?= htmlspecialchars(
                        $kategori['nama_kategori']
                    ) ?>

                </a>

            <?php endwhile; ?>

        </div>

    </div>

</section>


<!-- =====================================================
     DAFTAR AKSI
===================================================== -->

<section class="aksi-list-section">

    <div class="container">


        <div class="aksi-list-header">

            <div>

                <span class="section-label">
                    AKSI NYATA
                </span>


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

                                <?= $aksiIcons[(int) $aksi['id']]
                                    ?? htmlspecialchars($aksi['icon']) ?>

                            </div>


                            <div class="aksi-poin">

                                +<?= $aksi['poin'] ?>
                                POIN

                            </div>

                        </div>


                        <span class="aksi-sdg">

                            <?= htmlspecialchars(
                                $aksi['sdg']
                            ) ?>

                        </span>


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

                            📂

                            <?= htmlspecialchars(
                                $aksi['nama_kategori']
                            ) ?>

                        </div>


                        <div class="aksi-footer">


                            <div class="aksi-difficulty">

                                <?php

                                $difficultyClass =
                                    strtolower(
                                        $aksi[
                                            'tingkat_kesulitan'
                                        ]
                                    );

                                ?>


                                <span
                                    class="aksi-difficulty-dot
                                    <?= $difficultyClass ?>"
                                ></span>


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
                            Lakukan Aksi →
                        </a>

                        </div>


                    </article>

                <?php endwhile; ?>


            </div>

        <?php else: ?>


            <div class="empty-state">

                <div class="empty-state-icon">
                    🔍
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


<!-- =====================================================
     CTA
===================================================== -->

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
                🔥 Gabung Sekarang
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     FOOTER
===================================================== -->

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


<script src="../assets/js/icons.js?v=2"></script>
</body>

</html>
