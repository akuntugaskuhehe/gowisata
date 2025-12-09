<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';


// ================== QUERY FILTER ================== //

$where = [];
$order = "ORDER BY d.id DESC";

// FILTER SEARCH (nama destinasi)
if (!empty($_GET["q"])) {
    $q = mysqli_real_escape_string($conn, $_GET["q"]);
    $where[] = "d.nama LIKE '%$q%'";
}

// FILTER KATEGORI
if (!empty($_GET["kategori"])) {
    $where[] = "d.kategori_id = " . intval($_GET["kategori"]);
}

// FILTER KOTA
if (!empty($_GET["kota"])) {
    $where[] = "d.kota = '" . mysqli_real_escape_string($conn, $_GET["kota"]) . "'";
}

// FILTER PROVINSI
if (!empty($_GET["provinsi"])) {
    $where[] = "d.provinsi = '" . mysqli_real_escape_string($conn, $_GET["provinsi"]) . "'";
}

// FILTER HARGA MINIMAL
if (!empty($_GET['harga_min'])) {
    $where[] = "d.harga_tiket >= " . intval($_GET["harga_min"]);
}

// FILTER HARGA MAKSIMAL
if (!empty($_GET['harga_max'])) {
    $where[] = "d.harga_tiket <= " . intval($_GET["harga_max"]);
}

// FILTER RATING MINIMAL
if (!empty($_GET['rating_min'])) {
    $where[] = "d.rata_rating >= " . floatval($_GET["rating_min"]);
}

// FILTER MINIMAL ULASAN
if (!empty($_GET['ulasan_min'])) {
    $where[] = "d.jumlah_rating >= " . intval($_GET["ulasan_min"]);
}


// ========== SORTING ========== //
if (!empty($_GET["sort"])) {

    switch ($_GET["sort"]) {

        case "rating":
            $order = "ORDER BY d.rata_rating DESC";
            break;

        case "ulasan":
            $order = "ORDER BY d.jumlah_rating DESC";
            break;

        case "harga":
            $order = "ORDER BY d.harga_tiket ASC";
            break;

        case "baru":
            $order = "ORDER BY d.id DESC";
            break;

        case "dekat":
            if (!empty($_GET["lat_user"]) && !empty($_GET["lng_user"])) {

                $lat = floatval($_GET["lat_user"]);
                $lng = floatval($_GET["lng_user"]);

                $order = "
                    ORDER BY (
                        6371 * acos(
                            cos(radians($lat)) 
                            * cos(radians(latitude)) 
                            * cos(radians(longitude) - radians($lng)) 
                            + sin(radians($lat)) * sin(radians(latitude))
                        )
                    ) ASC
                ";
            }
            break;
    }
}


// ================== WHERE STRING ================== //

$whereSQL = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";


// ================== PAGINATION ================== //

$perPage  = 9;
$page     = isset($_GET["page"]) ? (int)$_GET["page"] : 1;
$offset   = ($page - 1) * $perPage;

$totalRows = mysqli_fetch_assoc(mysqli_query($conn,
"SELECT COUNT(*) AS total FROM destinasi d $whereSQL"
))["total"];

$totalPages = ceil($totalRows / $perPage);


// ================== MAIN QUERY ================== //

$q = mysqli_query($conn, "
    SELECT d.*, 
           (SELECT url_gambar FROM gambar_destinasi 
            WHERE destinasi_id=d.id AND utama=1 LIMIT 1) AS gambar_utama
    FROM destinasi d
    $whereSQL
    $order
    LIMIT $offset, $perPage
");

?>

<h3 class="mb-4"><i class="fa fa-map"></i> Semua Destinasi</h3>


<!-- ================== FILTER FORM ================== -->

<div class="card shadow-sm p-4 mb-4">

<form method="GET">

<!-- BARIS 1 -->
<div class="row g-3">

    <div class="col-md-3">
        <input type="text" name="q" class="form-control"
               placeholder="Cari destinasi..."
               value="<?= $_GET['q'] ?? '' ?>">
    </div>

    <div class="col-md-3">
        <select name="kategori" class="form-select">
            <option value="">Semua Kategori</option>
            <?php
            $kat = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama ASC");
            while ($k = mysqli_fetch_assoc($kat)):
            ?>
                <option value="<?= $k['id'] ?>"
                    <?= (($_GET['kategori']??'') == $k['id']) ? 'selected':'' ?>>
                    <?= $k["nama"] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <select name="kota" class="form-select">
            <option value="">Semua Kota</option>
            <?php
            $kota = mysqli_query($conn, "SELECT DISTINCT kota FROM destinasi WHERE kota!=''");
            while ($k = mysqli_fetch_assoc($kota)):
            ?>
                <option <?= (($_GET['kota']??'') == $k['kota']) ? 'selected':'' ?>>
                    <?= $k["kota"] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-3">
        <select name="provinsi" class="form-select">
            <option value="">Semua Provinsi</option>
            <?php
            $prov = mysqli_query($conn, "SELECT DISTINCT provinsi FROM destinasi WHERE provinsi!=''");
            while ($p = mysqli_fetch_assoc($prov)):
            ?>
                <option <?= (($_GET['provinsi']??'') == $p['provinsi']) ? 'selected':'' ?>>
                    <?= $p["provinsi"] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

</div>

<!-- BARIS 2 -->
<div class="row g-3 mt-2">

    <div class="col-md-3">
        <input type="number" name="harga_min" class="form-control" 
               placeholder="Harga minimal"
               value="<?= $_GET['harga_min'] ?? '' ?>">
    </div>

    <div class="col-md-3">
        <input type="number" name="harga_max" class="form-control"
               placeholder="Harga maksimal"
               value="<?= $_GET['harga_max'] ?? '' ?>">
    </div>

    <div class="col-md-3">
        <select name="rating_min" class="form-select">
            <option value="">Rating Minimal</option>
            <option value="4" <?= ($_GET["rating_min"]??'')=="4"?'selected':'' ?>>⭐⭐⭐⭐ ke atas</option>
            <option value="3" <?= ($_GET["rating_min"]??'')=="3"?'selected':'' ?>>⭐⭐⭐ ke atas</option>
            <option value="2" <?= ($_GET["rating_min"]??'')=="2"?'selected':'' ?>>⭐⭐ ke atas</option>
            <option value="1" <?= ($_GET["rating_min"]??'')=="1"?'selected':'' ?>>⭐ ke atas</option>
        </select>
    </div>

    <div class="col-md-3">
        <input type="number" name="ulasan_min" class="form-control"
               placeholder="Minimal ulasan"
               value="<?= $_GET['ulasan_min'] ?? '' ?>">
    </div>

</div>

<!-- BARIS 3: LOKASI DAN SORT -->
<div class="row g-3 mt-3">

    <div class="col-md-3">
        <button type="button" class="btn btn-primary w-100"
                onclick="ambilLokasi()">
            <i class="fa fa-location-arrow"></i> Gunakan Lokasi Saya
        </button>

        <input type="hidden" name="lat_user" id="lat_user" value="<?= $_GET['lat_user'] ?? '' ?>">
        <input type="hidden" name="lng_user" id="lng_user" value="<?= $_GET['lng_user'] ?? '' ?>">
    </div>

    <div class="col-md-3">
        <select name="sort" class="form-select">
            <option value="">Urutkan</option>
            <option value="rating" <?= ($_GET["sort"]??'')=="rating"?'selected':'' ?>>Rating Tertinggi</option>
            <option value="ulasan" <?= ($_GET["sort"]??'')=="ulasan"?'selected':'' ?>>Paling Banyak Ulasan</option>
            <option value="harga"  <?= ($_GET["sort"]??'')=="harga"?'selected':'' ?>>Harga Termurah</option>
            <option value="baru"   <?= ($_GET["sort"]??'')=="baru" ?'selected':'' ?>>Terbaru</option>
            <option value="dekat"  <?= ($_GET["sort"]??'')=="dekat"?'selected':'' ?>>Terdekat dari saya</option>
        </select>
    </div>

    <div class="col-md-3 d-grid">
        <button class="btn btn-success"><i class="fa fa-search"></i> Terapkan</button>
    </div>

    <div class="col-md-3 d-grid">
        <a href="<?= APP_URL ?>/destinasi/index.php" class="btn btn-secondary">
            Reset Filter
        </a>
    </div>

</div>

</form>
</div>


<script>
// Ambil lokasi user
function ambilLokasi() {
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            document.getElementById("lat_user").value = pos.coords.latitude;
            document.getElementById("lng_user").value = pos.coords.longitude;
            alert("Lokasi didapat! Tekan Terapkan.");
        },
        function() {
            alert("Gagal mendapatkan lokasi.");
        }
    );
}
</script>


<!-- ================== LIST DESTINASI ================== -->

<div class="row">

<?php if(mysqli_num_rows($q) == 0): ?>
    <div class="alert alert-warning">Tidak ada destinasi ditemukan.</div>
<?php endif; ?>

<?php while ($d = mysqli_fetch_assoc($q)): ?>
    <div class="col-md-4 mb-4">

        <div class="card shadow-sm h-100">

            <img src="<?= APP_URL ?>/assets/img/<?= $d["gambar_utama"] ?? "default.jpg" ?>"
                 class="card-img-top"
                 style="height:220px;object-fit:cover;">

            <div class="card-body">

                <h5><?= $d["nama"] ?></h5>

                <p class="text-muted small">
                    <i class="fa fa-map-marker text-danger"></i>
                    <?= $d["kota"] ?>, <?= $d["provinsi"] ?>
                </p>

                <p class="mb-2">
                    <span class="badge bg-warning text-dark">
                        ⭐ <?= $d["rata_rating"] ?>
                    </span>
                    <small>(<?= $d["jumlah_rating"] ?> ulasan)</small>
                </p>

                <a href="<?= APP_URL ?>/wisata/<?= $d["slug"] ?>"
                   class="btn btn-success btn-sm w-100">Lihat Detail</a>

            </div>

        </div>

    </div>
<?php endwhile; ?>
</div>


<!-- ================== PAGINATION ================== -->
<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination">

        <li class="page-item <?= $page<=1 ? 'disabled':'' ?>">
            <a class="page-link" href="?page=<?= $page-1 ?>">«</a>
        </li>

        <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?= $page>=$totalPages ? 'disabled':'' ?>">
            <a class="page-link" href="?page=<?= $page+1 ?>">»</a>
        </li>

    </ul>
</nav>
<?php endif; ?>


<?php include __DIR__ . '/../includes/footer.php'; ?>
