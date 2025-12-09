<?php
require_once __DIR__ . '/../includes/db.php';
include __DIR__ . '/admin-header.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

$q = mysqli_query($conn, "
    SELECT * FROM destinasi ORDER BY id DESC
");
?>

<h3 class="mb-4"><i class="fa fa-map"></i> Data Destinasi</h3>

<a href="<?= APP_URL ?>/admin/destinasi-create.php" class="btn btn-success mb-3">
    <i class="fa fa-plus"></i> Tambah Destinasi
</a>

<table class="table table-bordered table-striped">
    <thead class="table-success">
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>Kota</th>
            <th>Rating</th>
            <th>Jumlah Rating</th>
            <th>Aksi</th>
        </tr>
    </thead>

    <tbody>
    <?php $no=1; while($d=mysqli_fetch_assoc($q)): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $d['nama'] ?></td>
            <td><?= $d['kota'] ?></td>
            <td><?= $d['rata_rating'] ?> ⭐</td>
            <td><?= $d['jumlah_rating'] ?></td>
            <td>

                <a href="<?= APP_URL ?>/admin/destinasi-edit.php?id=<?= $d['id'] ?>"
                   class="btn btn-warning btn-sm">
                   <i class="fa fa-edit"></i>
                </a>

                <a href="<?= APP_URL ?>/admin/destinasi-delete.php?id=<?= $d['id'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Yakin hapus destinasi ini?')">
                   <i class="fa fa-trash"></i>
                </a>

            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include __DIR__ . '/admin-footer.php'; ?>
