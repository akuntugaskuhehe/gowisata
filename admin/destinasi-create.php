<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

$error = "";
$success = "";

// =======================
// FORM SUBMIT
// =======================
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

    // === VALIDASI MINIMAL ===
    if ($nama == "" || $slug == "") {
        $error = "Nama destinasi wajib diisi.";
    } elseif ($lat == "" || $lng == "") {
        $error = "Silakan pilih lokasi dari peta.";
    } else {

        // INSERT DESTINASI
        mysqli_query($conn, "
            INSERT INTO destinasi
            (nama, slug, deskripsi, kota, provinsi, harga_tiket, jam_buka, latitude, longitude, jumlah_rating)
            VALUES
            ('$nama','$slug','$deskripsi','$kota','$provinsi',$harga,'$jam','$lat','$lng',0)
        ");

        $dest_id = mysqli_insert_id($conn);

        // === UPLOAD FOTO MULTIPLE ===
        $isFirst = true;
        foreach ($_FILES['gambar']['name'] as $i => $nm) {

            if ($nm == "") continue;

            $safeName = time() . "_" . preg_replace("/[^A-Za-z0-9_.-]/", "_", $nm);
            $tmp      = $_FILES['gambar']['tmp_name'][$i];
            $target   = __DIR__ . '/../assets/img/' . $safeName;

            move_uploaded_file($tmp, $target);

            $utama = $isFirst ? 1 : 0;
            $isFirst = false;

            mysqli_query($conn, "
                INSERT INTO gambar_destinasi (destinasi_id, url_gambar, utama)
                VALUES ($dest_id, '$safeName', $utama)
            ");
        }

        $success = "Destinasi berhasil ditambahkan!";
    }
}
?>

<?php include __DIR__ . '/admin-header.php'; ?>

<h3 class="mb-3"><i class="fa fa-plus-circle"></i> Tambah Destinasi</h3>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div class="row mb-3">
        <div class="col">
            <label>Nama Destinasi</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="col">
            <label>Kota</label>
            <input name="kota" id="kota" class="form-control">
        </div>

        <div class="col">
            <label>Provinsi</label>
            <input name="provinsi" id="provinsi" class="form-control">
        </div>
    </div>

    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control mb-3" rows="5"></textarea>

    <div class="row mb-3">
        <div class="col">
            <label>Harga Tiket</label>
            <input type="number" name="harga" class="form-control">
        </div>

        <div class="col">
            <label>Jam Buka</label>
            <input type="text" name="jam_buka" class="form-control" placeholder="08:00 - 17:00">
        </div>
    </div>

    <!-- Lokasi -->
    <div class="mb-3">
        <label class="form-label">Cari Lokasi (OpenStreetMap)</label>
        <input type="text" id="searchBox" class="form-control" placeholder="Cari lokasi...">
        <div id="resultList" class="list-group mt-2"></div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Latitude</label>
            <input type="text" name="latitude" id="latitude" class="form-control" readonly>
        </div>

        <div class="col-md-6 mb-3">
            <label>Longitude</label>
            <input type="text" name="longitude" id="longitude" class="form-control" readonly>
        </div>
    </div>

    <div id="map" style="height:350px;" class="mb-3"></div>

    <label>Gambar (multiple)</label>
    <input type="file" name="gambar[]" class="form-control mb-3" multiple required>

    <button class="btn btn-success">
        <i class="fa fa-check"></i> Simpan
    </button>

</form>


<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
let map = L.map('map').setView([-6.2, 106.8], 12);
let marker;

// MAP OSM
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19
}).addTo(map);

// SET DEFAULT MARKER
function setMarker(lat, lng) {
    if (marker) map.removeLayer(marker);

    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function (e) {
        let pos = e.target.getLatLng();
        latitude.value = pos.lat;
        longitude.value = pos.lng;
    });
}

setMarker(-6.2, 106.8);

// AUTOCOMPLETE
const searchBox = document.getElementById("searchBox");
const resultList = document.getElementById("resultList");

searchBox.addEventListener("keyup", function () {
    let q = searchBox.value.trim();

    if (q.length < 3) {
        resultList.innerHTML = "";
        return;
    }

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${q}`)
        .then(res => res.json())
        .then(data => {
            resultList.innerHTML = "";

            data.slice(0, 5).forEach(loc => {

                let item = document.createElement("a");
                item.href = "#";
                item.className = "list-group-item list-group-item-action";
                item.textContent = loc.display_name;

                item.onclick = function () {
                    searchBox.value = loc.display_name;
                    resultList.innerHTML = "";

                    map.setView([loc.lat, loc.lon], 16);
                    setMarker(loc.lat, loc.lon);

                    latitude.value  = loc.lat;
                    longitude.value = loc.lon;

                    // Auto kota / provinsi
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${loc.lat}&lon=${loc.lon}`)
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

<?php include __DIR__ . '/admin-footer.php'; ?>
