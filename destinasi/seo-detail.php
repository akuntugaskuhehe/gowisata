<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

$slug = mysqli_real_escape_string($conn, $_GET["slug"]);

$dest = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM destinasi WHERE slug='$slug'
"));

if (!$dest) {
    echo "<h3>Destinasi tidak ditemukan.</h3>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

$id = $dest["id"];

$gambars = mysqli_query($conn, "
    SELECT * FROM gambar_destinasi WHERE destinasi_id=$id ORDER BY utama DESC
");

$ulasans = mysqli_query($conn, "
    SELECT u.nama, ul.rating, ul.komentar, ul.dibuat_pada
    FROM ulasan ul
    JOIN users u ON ul.pengguna_id = u.id
    WHERE destinasi_id=$id
    ORDER BY ul.id DESC
");

$user_id = isLoggedIn() ? user()["id"] : 0;
$isSaved = $user_id ? isBookmarked($conn, $user_id, $id) : false;

// Kategori
$kategori = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT nama, slug FROM kategori WHERE id={$dest['kategori_id']}
"));
?>

<h2 class="mb-3">
    <?= $dest["nama"] ?>
    <span class="text-warning"><?= $dest["rata_rating"] ?> ⭐</span>
    <small>(<?= $dest["jumlah_rating"] ?> ulasan)</small>
</h2>

<?php if ($kategori): ?>
<div class="mb-3">
    <a href="<?= APP_URL ?>/kategori/<?= $kategori['slug'] ?>" 
       class="badge bg-success text-white">
        <i class="fa fa-folder"></i> <?= $kategori["nama"] ?>
    </a>
</div>
<?php endif; ?>


<!-- BOOKMARK -->
<?php if (isLoggedIn()): ?>
    <?php if (!$isSaved): ?>
        <a href="<?= APP_URL ?>/destinasi/bookmark.php?id=<?= $id ?>" 
           class="btn btn-warning mb-3"><i class="fa fa-bookmark"></i> Simpan</a>
    <?php else: ?>
        <a href="<?= APP_URL ?>/destinasi/unbookmark.php?id=<?= $id ?>" 
           class="btn btn-danger mb-3"><i class="fa fa-trash"></i> Hapus</a>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-info">Login untuk bookmark.</div>
<?php endif; ?>

<hr>

<!-- SLIDER -->
<div id="sliderWisata" class="carousel slide mb-4">
    <div class="carousel-inner">
        <?php $i=0; while ($g = mysqli_fetch_assoc($gambars)): ?>
        <div class="carousel-item <?= $i==0 ? 'active' : '' ?>">
            <img src="<?= APP_URL ?>/assets/img/<?= $g['url_gambar'] ?>" 
                class="d-block w-100 rounded" style="height:400px;object-fit:cover;">
        </div>
        <?php $i++; endwhile; ?>
    </div>

    <button class="carousel-control-prev" data-bs-target="#sliderWisata" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" data-bs-target="#sliderWisata" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<hr>

<h4>Deskripsi</h4>
<p><?= nl2br($dest["deskripsi"]) ?></p>

<h4>Detail Lokasi</h4>
<ul>
    <li>Kota: <?= $dest["kota"] ?></li>
    <li>Provinsi: <?= $dest["provinsi"] ?></li>
    <li>Harga Tiket: Rp <?= $dest["harga_tiket"] ?></li>
    <li>Jam Buka: <?= $dest["jam_buka"] ?></li>
</ul>

<!-- Maps -->
<div id="map" style="height:350px;" class="rounded mb-4"></div>

<script>
function initMap() {
    var loc = { lat: <?= $dest['latitude'] ?>, lng: <?= $dest['longitude'] ?> };
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 14, center: loc
    });
    new google.maps.Marker({ position: loc, map: map });
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"
        async defer></script>

<hr>

<!-- Share -->
<h4>Bagikan</h4>
<a class="btn btn-primary btn-sm" 
   href="https://facebook.com/sharer/sharer.php?u=<?= APP_URL ?>/wisata/<?= $slug ?>" target="_blank">
   <i class="fab fa-facebook"></i> Facebook
</a>

<a class="btn btn-success btn-sm" 
   href="https://api.whatsapp.com/send?text=<?= urlencode($dest['nama'].' '.APP_URL.'/wisata/'.$slug) ?>" target="_blank">
   <i class="fab fa-whatsapp"></i> WhatsApp
</a>

<a class="btn btn-info btn-sm text-white" 
   href="https://twitter.com/intent/tweet?url=<?= APP_URL ?>/wisata/<?= $slug ?>" target="_blank">
   <i class="fab fa-twitter"></i> Twitter
</a>

<hr>

<!-- Rating -->
<h4>Beri Rating</h4>
<?php if (isLoggedIn()): ?>
<form method="POST" action="<?= APP_URL ?>/destinasi/rate.php">

    <input type="hidden" name="destinasi_id" value="<?= $id ?>">

    <select name="rating" class="form-select w-25 mb-2">
        <option value="5">⭐⭐⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="2">⭐⭐</option>
        <option value="1">⭐</option>
    </select>

    <textarea name="komentar" class="form-control mb-2" placeholder="Tulis komentar..."></textarea>

    <button class="btn btn-success"><i class="fa fa-paper-plane"></i> Kirim</button>

</form>
<?php endif; ?>

<hr>

<!-- Ulasan -->
<h4>Ulasan</h4>
<?php while ($u = mysqli_fetch_assoc($ulasans)): ?>
<div class="border rounded p-3 mb-2">
    <strong><?= $u["nama"] ?></strong>
    <span class="text-warning"><?= $u["rating"] ?> ⭐</span>
    <small class="text-muted">(<?= $u["dibuat_pada"] ?>)</small>
    <p><?= nl2br($u["komentar"]) ?></p>
</div>
<?php endwhile; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
