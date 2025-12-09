<?php
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/admin-header.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

// Hanya admin
if (user()["role"] !== "admin") {
    echo "<div class='alert alert-danger'>Anda tidak memiliki akses ke halaman admin!</div>";
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// Hitung data
$totalDest = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM destinasi"))["total"];
$totalUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))["total"];
$totalUlas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ulasan"))["total"];
$totalBook = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM bookmark"))["total"];

// Ambil data terbaru
$latestDest = mysqli_query($conn, "
    SELECT * FROM destinasi ORDER BY id DESC LIMIT 5
");

$latestUlas = mysqli_query($conn, "
    SELECT ul.*, u.nama AS user_nama, d.nama AS dest_nama
    FROM ulasan ul
    JOIN users u ON u.id = ul.pengguna_id
    JOIN destinasi d ON d.id = ul.destinasi_id
    ORDER BY ul.id DESC LIMIT 5
");
?>

<h3><i class="fa fa-chart-line"></i> Dashboard Admin</h3>
<hr>

<!-- CARDS -->
<div class="row mb-4">

    <div class="col-md-3">
        <div class="card shadow-sm border-left-success">
            <div class="card-body">
                <h6 class="text-muted">Total Destinasi</h6>
                <h3><?= $totalDest ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-left-primary">
            <div class="card-body">
                <h6 class="text-muted">Total Pengguna</h6>
                <h3><?= $totalUser ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-left-warning">
            <div class="card-body">
                <h6 class="text-muted">Total Ulasan</h6>
                <h3><?= $totalUlas ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-left-danger">
            <div class="card-body">
                <h6 class="text-muted">Total Bookmark</h6>
                <h3><?= $totalBook ?></h3>
            </div>
        </div>
    </div>

</div>

<!-- STATISTIK GRAFIK -->
<div class="card shadow-sm p-4 mb-4">
    <h4><i class="fa fa-chart-pie"></i> Grafik Aktivitas</h4>

    <canvas id="chartAktivitas" height="90"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('chartAktivitas');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Destinasi', 'Pengguna', 'Ulasan', 'Bookmark'],
                datasets: [{
                    label: 'Jumlah Data',
                    data: [<?= $totalDest ?>, <?= $totalUser ?>, <?= $totalUlas ?>, <?= $totalBook ?>],
                    borderWidth: 1,
                    backgroundColor: ['#28a745', '#007bff', '#ffc107', '#dc3545']
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    </script>
</div>

<!-- DESTINASI TERBARU -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <i class="fa fa-map"></i> 5 Destinasi Terbaru
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tr>
                <th>Nama</th>
                <th>Kota</th>
                <th>Rating</th>
                <th>Aksi</th>
            </tr>

            <?php while ($d = mysqli_fetch_assoc($latestDest)): ?>
            <tr>
                <td><?= $d["nama"] ?></td>
                <td><?= $d["kota"] ?></td>
                <td><?= $d["rata_rating"] ?> ⭐</td>
                <td>
                    <a href="<?= APP_URL ?>/admin/destinasi-edit.php?id=<?= $d['id'] ?>" 
                       class="btn btn-sm btn-warning">
                       <i class="fa fa-edit"></i>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<!-- ULASAN TERBARU -->
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <i class="fa fa-comments"></i> 5 Ulasan Terbaru
    </div>

    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tr>
                <th>User</th>
                <th>Destinasi</th>
                <th>Rating</th>
                <th>Komentar</th>
            </tr>

            <?php while ($u = mysqli_fetch_assoc($latestUlas)): ?>
            <tr>
                <td><?= $u["user_nama"] ?></td>
                <td><?= $u["dest_nama"] ?></td>
                <td><?= $u["rating"] ?> ⭐</td>
                <td><?= $u["komentar"] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</div>

<?php include __DIR__ . '/admin-footer.php'; ?>
