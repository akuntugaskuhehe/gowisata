<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

// ===========================
// GET DATA DESTINASI
// ===========================
$id = intval($_GET["id"]);
$dest = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM destinasi WHERE id=$id
"));

if (!$dest) {
    die("Destinasi tidak ditemukan!");
}

// Ambil gambar
$gambar = mysqli_query($conn, "
    SELECT * FROM gambar_destinasi WHERE destinasi_id=$id ORDER BY id DESC
");

$success = "";
$error   = "";

// ===========================
// UPDATE DATA DESTINASI
// ===========================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama      = mysqli_real_escape_string($conn, $_POST["nama"]);
    $slug      = slugify($nama);
    $deskripsi = mysqli_real_escape_string($conn, $_POST["deskripsi"]);
    $kota      = mysqli_real_escape_string($conn, $_POST["kota"]);
    $provinsi  = mysqli_real_escape_string($conn, $_POST["provinsi"]);
    $harga     = intval($_POST["harga"]);
    $jam       = mysqli_real_escape_string($conn, $_POST["jam_buka"]);
    $lat       = $_POST["latitude"];
    $lng       = $_POST["longitude"];

    if ($lat == "" || $lng == "") {
        $error = "Lokasi belum dipilih dari peta!";
    } else {

        mysqli_query($conn, "
            UPDATE destinasi SET 
                nama='$nama',
                slug='$slug',
                deskripsi='$deskripsi',
                kota='$kota',
                provinsi='$provinsi',
                harga_tiket=$harga,
                jam_buka='$jam',
                latitude='$lat',
                longitude='$lng'
            WHERE id=$id
        ");

        // ===========================
        // UPLOAD GAMBAR TAMBAHAN
        // ===========================
        foreach ($_FILES['gambar']['name'] as $i => $nm) {
            if ($nm == "") continue;

            $safeName = time() . "_" . preg_replace("/[^A-Za-z0-9_.-]/", "_", $nm);
            $tmp      = $_FILES['gambar']['tmp_name'][$i];
            $target   = __DIR__ . '/../assets/img/' . $safeName;

            move_uploaded_file($tmp, $target);

            mysqli_query($conn, "
                INSERT INTO gambar_destinasi (destinasi_id, url_gambar)
                VALUES ($id, '$safeName')
            ");
        }

        $success = "Data destinasi berhasil diperbarui!";
    }
}

?>

<?php include __DIR__ . '/admin-header.php'; ?>

<h3><i class="fa fa-edit"></i> Edit Destinasi</h3>

<?php if($success): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- FORM EDIT -->
<form method="POST" enctype="multipart/form-data">

    <label>Nama Destinasi</label>
    <input type="text" name="nama" value="<?= $dest['nama'] ?>" class="form-control mb-2" required>

    <label>Kota</label>
    <input name="kota" id="kota" value="<?= $dest['kota'] ?>" class="form-control mb-2">

    <label>Provinsi</label>
    <input name="provinsi" id="provinsi" value="<?= $dest['provinsi'] ?>" class="form-control mb-2">

    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control mb-2" rows="4"><?= $dest['deskripsi'] ?></textarea>

    <div class="row">
        <div class="col">
            <label>Harga Tiket</label>
            <input type="number" name="harga" value="<?= $dest['harga_tiket'] ?>" class="form-control mb-2">
        </div>

        <div class="col">
            <label>Jam Buka</label>
            <input type="text" name="jam_buka" value="<?= $dest['jam_buka'] ?>" class="form-control mb-2">
        </div>
    </div>

    <!-- Lokasi -->
    <div class="mb-3 mt-3">
        <label class="form-label">Cari Lokasi (OpenStreetMap)</label>
        <input type="text" id="searchBox" class="form-control" placeholder="Cari lokasi...">
        <div id="resultList" class="list-group mt-2"></div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Latitude</label>
            <input type="text" name="latitude" id="latitude" class="form-control" value="<?= $dest['latitude'] ?>" readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label>Longitude</label>
            <input type="text" name="longitude" id="longitude" class="form-control" value="<?= $dest['longitude'] ?>" readonly>
        </div>
    </div>

    <div id="map" style="height:350px;" class="mb-3"></div>

    <label>Tambah Gambar Baru</label>
    <input type="file" name="gambar[]" class="form-control mb-3" multiple>

    <button class="btn btn-success">
        <i class="fa fa-check"></i> Update
    </button>

</form>


<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
// =======================
// MAP INITIALIZATION
// =======================
let map = L.map('map').setView(
    [<?= $dest['latitude'] ?>, <?= $dest['longitude'] ?>],
    15
);
let marker;

// TILE OSM
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

// SET MARKER DI POSISI EXISTING
function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);

    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function (e) {
        let pos = e.target.getLatLng();
        latitude.value  = pos.lat;
        longitude.value = pos.lng;
    });
}

setMarker(<?= $dest['latitude'] ?>, <?= $dest['longitude'] ?>);


// =======================
// AUTOCOMPLETE SEARCH
// =======================
const searchBox  = document.getElementById("searchBox");
const resultList = document.getElementById("resultList");

searchBox.addEventListener("keyup", function () {

    let q = searchBox.value.trim();
    resultList.innerHTML = "";

    if (q.length < 3) return;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${q}`)
        .then(res => res.json())
        .then(data => {

            data.slice(0, 6).forEach(loc => {

                let item = document.createElement("a");
                item.href = "#";
                item.className = "list-group-item list-group-item-action";
                item.textContent = loc.display_name;

                item.onclick = function () {

                    searchBox.value = loc.display_name;
                    resultList.innerHTML = "";

                    let lat = parseFloat(loc.lat);
                    let lon = parseFloat(loc.lon);

                    latitude.value  = lat;
                    longitude.value = lon;

                    map.setView([lat, lon], 16);
                    setMarker(lat, lon);

                    // AUTO KOTA / PROVINSI
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(r => r.json())
                        .then(addr => {
                            if (addr.address) {
                                kota.value     = addr.address.city || addr.address.town || addr.address.village || "";
                                provinsi.value = addr.address.state || "";
                            }
                        });
                };

                resultList.appendChild(item);
            });
        });
});
</script>

<hr>

<h4><i class="fa fa-images"></i> Gambar Destinasi</h4>

<div class="row">
<?php while($g = mysqli_fetch_assoc($gambar)): ?>
    <div class="col-md-3 mb-3 text-center">
        <img src="<?= APP_URL ?>/assets/img/<?= $g['url_gambar'] ?>" class="img-fluid rounded mb-2">
        <a href="<?= APP_URL ?>/admin/destinasi-gambar-delete.php?id=<?= $g['id'] ?>&dest=<?= $id ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Hapus gambar ini?')">
           <i class="fa fa-trash"></i> Hapus
        </a>
    </div>
<?php endwhile; ?>
</div>

<?php include __DIR__ . '/admin-footer.php'; ?>
