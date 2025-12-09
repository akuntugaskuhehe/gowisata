<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

/*
   SUPPORT:
   - detail.php?slug=slug-wisata
   - detail.php?id=10
*/

// -----------------------------
// Ambil parameter
// -----------------------------
$slug = $_GET["slug"] ?? null;
$id   = $_GET["id"] ?? null;

if ($slug) {
    $slug = mysqli_real_escape_string($conn, $slug);
    $dest = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * FROM destinasi WHERE slug='$slug'
    "));
} elseif ($id) {
    $id = intval($id);
    $dest = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT * FROM destinasi WHERE id=$id
    "));
} else {
    echo "<div class='alert alert-danger'>Parameter tidak valid.</div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

if (!$dest) {
    echo "<div class='alert alert-warning'>Destinasi tidak ditemukan.</div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

$dest_id = $dest["id"];

// -----------------------------
// Ambil gambar
// -----------------------------
$gambars = mysqli_query($conn, "
    SELECT * FROM gambar_destinasi 
    WHERE destinasi_id=$dest_id 
    ORDER BY utama DESC, id DESC
");

// -----------------------------
// Ambil ulasan
// -----------------------------
$ulasans = mysqli_query($conn, "
    SELECT u.nama, ul.rating, ul.komentar, ul.dibuat_pada
    FROM ulasan ul
    JOIN users u ON ul.pengguna_id = u.id
    WHERE ul.destinasi_id=$dest_id
    ORDER BY ul.id DESC
");

// -----------------------------
// Bookmark
// -----------------------------
$user_id = isLoggedIn() ? user()["id"] : 0;
$isSaved = $user_id ? isBookmarked($conn, $user_id, $dest_id) : false;

// -----------------------------
// Kategori aman
// -----------------------------
$kategori = null;
if (!empty($dest["kategori_id"])) {
    $katID = intval($dest["kategori_id"]);
    $kategori = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT nama, slug 
        FROM kategori 
        WHERE id=$katID
    "));
}

?>

<style>
.detail-title {
    font-size: 28px;
    font-weight: 700;
}
.detail-rating {
    font-size: 20px;
}
.detail-info-box {
    font-size: 16px;
}
.review-box {
    background: #f8f9fa;
    border-radius: 8px;
}
</style>

<div class="container my-4">

    <div class="row">

        <!-- ===================== -->
        <!-- LEFT: GAMBAR / SLIDER -->
        <!-- ===================== -->
        <div class="col-md-6 mb-4">

            <div id="sliderWisata" class="carousel slide">
                <div class="carousel-inner">

                    <?php $i=0; while ($g = mysqli_fetch_assoc($gambars)): ?>
                    <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>">
                        <img src="<?= APP_URL ?>/assets/img/<?= $g['url_gambar'] ?>"
                             class="d-block w-100 rounded"
                             style="height:400px;object-fit:cover;">
                    </div>
                    <?php $i++; endwhile; ?>

                </div>

                <?php if ($i > 1): ?>
                <button class="carousel-control-prev" data-bs-target="#sliderWisata" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" data-bs-target="#sliderWisata" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                <?php endif; ?>
            </div>

        </div>

        <!-- ===================== -->
        <!-- RIGHT: DETAIL WISATA -->
        <!-- ===================== -->
        <div class="col-md-6">

            <div class="detail-title mb-2">
                <?= htmlspecialchars($dest["nama"]) ?>
            </div>

            <div class="detail-rating text-warning mb-2">
                <?= $dest["rata_rating"] ?> ⭐
                <small class="text-dark">(<?= $dest["jumlah_rating"] ?> ulasan)</small>
            </div>

            <?php if ($kategori): ?>
            <div class="mb-3">
                <a class="badge bg-success text-white"
                    href="<?= APP_URL ?>/kategori/<?= $kategori["slug"] ?>">
                    <i class="fa fa-folder"></i> <?= $kategori["nama"] ?>
                </a>
            </div>
            <?php endif; ?>

            <!-- Bookmark -->
            <?php if (isLoggedIn()): ?>
                <?php if (!$isSaved): ?>
                    <a href="<?= APP_URL ?>/destinasi/bookmark.php?id=<?= $dest_id ?>" 
                       class="btn btn-warning mb-3">
                       <i class="fa fa-bookmark"></i> Simpan
                    </a>
                <?php else: ?>
                    <a href="<?= APP_URL ?>/destinasi/unbookmark.php?id=<?= $dest_id ?>" 
                       class="btn btn-danger mb-3">
                       <i class="fa fa-trash"></i> Hapus Bookmark
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info py-2">Login untuk bookmark.</div>
            <?php endif; ?>

            <div class="detail-info-box">
                <p><?= nl2br(htmlspecialchars($dest["deskripsi"])) ?></p>

                <ul class="list-unstyled">
                    <li><strong>Kota:</strong> <?= $dest["kota"] ?></li>
                    <li><strong>Provinsi:</strong> <?= $dest["provinsi"] ?></li>
                    <li><strong>Harga Tiket:</strong> Rp <?= number_format($dest["harga_tiket"],0,",",".") ?></li>
                    <li><strong>Jam Buka:</strong> <?= $dest["jam_buka"] ?></li>
                </ul>
            </div>

            <!-- Share -->
            <h5>Bagikan:</h5>
            <a class="btn btn-primary btn-sm"
                href="https://facebook.com/sharer/sharer.php?u=<?= APP_URL ?>/destinasi/<?= $dest["slug"] ?>"
                target="_blank"><i class="fab fa-facebook"></i> Facebook</a>

            <a class="btn btn-success btn-sm"
                href="https://api.whatsapp.com/send?text=<?= urlencode($dest['nama'].' '.APP_URL.'/destinasi/'.$dest['slug']) ?>"
                target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>

            <a class="btn btn-info btn-sm text-white"
                href="https://twitter.com/intent/tweet?url=<?= APP_URL ?>/destinasi/<?= $dest["slug"] ?>"
                target="_blank"><i class="fab fa-twitter"></i> Twitter</a>

        </div>
    </div>

    <!-- MAP SECTION -->
    <div class="my-4">
        <h4>Lokasi di Peta</h4>
        <div id="map" style="height:350px;" class="rounded"></div>
    </div>

    <!-- RATING SECTION -->
    <hr>
    <h4>Beri Rating</h4>

    <?php if (isLoggedIn()): ?>
        <form method="POST" action="<?= APP_URL ?>/destinasi/rate.php" class="mb-4">

            <input type="hidden" name="destinasi_id" value="<?= $dest_id ?>">

            <select name="rating" class="form-select w-25 mb-2">
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>

            <textarea name="komentar" class="form-control mb-2"
                      placeholder="Tulis komentar..."></textarea>

            <button class="btn btn-success"><i class="fa fa-paper-plane"></i> Kirim</button>

        </form>
    <?php else: ?>
        <p class="text-muted">Login untuk memberikan ulasan.</p>
    <?php endif; ?>

    <!-- ULASAN -->
    <h4>Ulasan Pengunjung</h4>

    <?php while ($u = mysqli_fetch_assoc($ulasans)): ?>
        <div class="review-box p-3 mb-3">
            <strong><?= htmlspecialchars($u["nama"]) ?></strong>
            <span class="text-warning"><?= $u["rating"] ?> ⭐</span>
            <small class="text-muted"><?= $u["dibuat_pada"] ?></small>
            <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($u["komentar"])) ?></p>
        </div>
    <?php endwhile; ?>

</div>

<!-- LEAFLET MAP -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
var map = L.map('map').setView(
    [<?= $dest['latitude'] ?>, <?= $dest['longitude'] ?>],
    14
);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

L.marker([<?= $dest['latitude'] ?>, <?= $dest['longitude'] ?>]).addTo(map);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
