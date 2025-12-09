<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ================== HERO SECTION ================== -->
<div class="p-5 mb-4 rounded-3 text-white"
     style="background: linear-gradient(135deg,#7fe7a1,#3fb874);">
    <div class="container py-5">
        <h1 class="display-5 fw-bold"><?= APP_NAME ?></h1>
        <p class="fs-4">
            Temukan destinasi wisata terbaik, ulasan jujur, dan rekomendasi menarik!
        </p>
        <a href="#cari" class="btn btn-light btn-lg">
            <i class="fa fa-search"></i> Mulai Jelajah
        </a>
    </div>
</div>

<!-- ================== SEARCH BAR ================== -->
<div id="cari" class="card shadow-sm p-4 mb-4">
    <h4 class="mb-3"><i class="fa fa-search"></i> Cari Destinasi</h4>

    <form method="GET" action="">

        <div class="row g-3">
            <div class="col-md-5">
                <input type="text" name="q" class="form-control"
                       placeholder="Cari nama destinasi..."
                       value="<?= $_GET['q'] ?? '' ?>">
            </div>

            <div class="col-md-3">
                <select name="kota" class="form-select">
                    <option value="">Semua Kota</option>
                    <?php
                    $kotas = mysqli_query($conn, "SELECT DISTINCT kota FROM destinasi WHERE kota != '' ORDER BY kota ASC");
                    while ($k = mysqli_fetch_assoc($kotas)):
                    ?>
                       <option <?= (($_GET['kota']??'') == $k['kota']) ? 'selected':'' ?>>
                            <?= $k['kota'] ?>
                       </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-3">
                <select name="provinsi" class="form-select">
                    <option value="">Semua Provinsi</option>
                    <?php
                    $prov = mysqli_query($conn, "SELECT DISTINCT provinsi FROM destinasi WHERE provinsi != '' ORDER BY provinsi ASC");
                    while ($p = mysqli_fetch_assoc($prov)):
                    ?>
                       <option <?= (($_GET['provinsi']??'') == $p['provinsi']) ? 'selected':'' ?>>
                            <?= $p['provinsi'] ?>
                       </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="col-md-1 d-grid">
                <button class="btn btn-success">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>

    </form>
</div>

<!-- ================== HASIL FILTER & PENCARIAN ================== -->
<?php
$where = [];

if (!empty($_GET['q'])) {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where[] = "nama LIKE '%$q%'";
}

if (!empty($_GET['kota'])) {
    $where[] = "kota = '" . mysqli_real_escape_string($conn, $_GET['kota']) . "'";
}

if (!empty($_GET['provinsi'])) {
    $where[] = "provinsi = '" . mysqli_real_escape_string($conn, $_GET['provinsi']) . "'";
}

$whereSQL = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";

$hasil = mysqli_query($conn, "
    SELECT * FROM destinasi
    $whereSQL
    ORDER BY rata_rating DESC
");
?>

<?php if (isset($_GET["q"]) || isset($_GET["kota"]) || isset($_GET["provinsi"])): ?>
<h4 class="mb-3">Hasil Pencarian</h4>

<div class="row">
<?php while ($d = mysqli_fetch_assoc($hasil)): ?>
    <?php
        $g = mysqli_fetch_assoc(mysqli_query($conn, "
            SELECT url_gambar FROM gambar_destinasi
            WHERE destinasi_id={$d['id']} AND utama=1 LIMIT 1
        "));
        $img = $g["url_gambar"] ?? "default.jpg";
    ?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm">
            <img src="<?= APP_URL ?>/assets/img/<?= $img ?>" 
                 class="card-img-top" style="height:200px;object-fit:cover;">

            <div class="card-body">
                <h5><?= $d["nama"] ?></h5>
                <p class="text-muted small">
                    <i class="fa fa-map-marker text-danger"></i>
                    <?= $d["kota"] ?>, <?= $d["provinsi"] ?>
                </p>
                <p>
                    <span class="badge bg-warning text-dark">⭐ <?= $d["rata_rating"] ?></span>
                </p>

                <a href="<?= APP_URL ?>/destinasi/detail.php?id=<?= $d["id"] ?>" 
                   class="btn btn-success btn-sm w-100">Lihat Detail</a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

<hr>
<?php endif; ?>

<!-- ================== TOP RATED ================== -->
<h3 class="mb-3"><i class="fa fa-trophy text-warning"></i> Wisata Terbaik</h3>

<div class="row mb-4">
<?php
$top = mysqli_query($conn, "
    SELECT * FROM destinasi ORDER BY rata_rating DESC, jumlah_rating DESC LIMIT 6
");
while ($d = mysqli_fetch_assoc($top)):
    $g = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT url_gambar FROM gambar_destinasi
        WHERE destinasi_id={$d['id']} AND utama=1 LIMIT 1
    "));
    $img = $g["url_gambar"] ?? "default.jpg";
?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">

            <img src="<?= APP_URL ?>/assets/img/<?= $img ?>"
                 class="card-img-top"
                 style="height:220px;object-fit:cover;">

            <div class="card-body">
                <h5><?= $d["nama"] ?></h5>
                <p class="text-muted small">
                    <i class="fa fa-map-marker text-danger"></i> 
                    <?= $d["kota"] ?>, <?= $d["provinsi"] ?>
                </p>

                <p class="mb-2">
                    <span class="badge bg-warning text-dark">⭐ <?= $d["rata_rating"] ?></span>
                    <small><?= $d["jumlah_rating"] ?> ulasan</small>
                </p>

                <a href="<?= APP_URL ?>/destinasi/detail.php?id=<?= $d["id"] ?>"
                   class="btn btn-success btn-sm w-100">
                   <i class="fa fa-eye"></i> Lihat Detail
                </a>

            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

<!-- ================== TERBARU ================== -->
<h3 class="mb-3"><i class="fa fa-clock text-primary"></i> Destinasi Terbaru</h3>

<div class="row mb-5">
<?php
$new = mysqli_query($conn, "
    SELECT * FROM destinasi ORDER BY id DESC LIMIT 6
");
while ($d = mysqli_fetch_assoc($new)):
    $g = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT url_gambar FROM gambar_destinasi
        WHERE destinasi_id={$d['id']} AND utama=1 LIMIT 1
    "));
    $img = $g["url_gambar"] ?? "default.jpg";
?>
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">

            <img src="<?= APP_URL ?>/assets/img/<?= $img ?>"
                 class="card-img-top"
                 style="height:220px;object-fit:cover;">

            <div class="card-body">
                <h5><?= $d["nama"] ?></h5>

                <a href="<?= APP_URL ?>/wisata/<?= $d["slug"] ?>"
                   class="btn btn-primary btn-sm w-100">
                   Lihat
                </a>

            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
