<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

requireLogin(); // user wajib login

$user = user();
$user_id = $user["id"];

// Ambil daftar bookmark user
$items = mysqli_query($conn, "
    SELECT d.id, d.nama, d.slug, d.kota, d.provinsi, d.rata_rating, d.jumlah_rating,
           (SELECT url_gambar FROM gambar_destinasi 
            WHERE destinasi_id = d.id ORDER BY utama DESC, id ASC LIMIT 1) AS foto
    FROM bookmark b
    JOIN destinasi d ON b.destinasi_id = d.id
    WHERE b.pengguna_id = $user_id
    ORDER BY b.id DESC
");
?>

<div class="container my-4">

    <h2 class="mb-4"><i class="fa fa-bookmark"></i> Wishlist Saya</h2>

    <?php if (mysqli_num_rows($items) == 0): ?>

        <div class="alert alert-info">
            Anda belum menyimpan destinasi apa pun.
        </div>

    <?php else: ?>

        <div class="row">

            <?php while ($d = mysqli_fetch_assoc($items)): ?>
            <div class="col-md-4 mb-4">

                <div class="card shadow-sm">

                    <?php if ($d["foto"]): ?>
                        <img src="<?= APP_URL ?>/assets/img/<?= $d["foto"] ?>"
                             class="card-img-top"
                             style="height:180px;object-fit:cover;">
                    <?php else: ?>
                        <img src="<?= APP_URL ?>/assets/img/noimage.jpg"
                             class="card-img-top"
                             style="height:180px;object-fit:cover;">
                    <?php endif; ?>

                    <div class="card-body">

                        <h5 class="card-title"><?= htmlspecialchars($d["nama"]) ?></h5>

                        <p class="text-muted mb-1">
                            <i class="fa fa-map-marker-alt"></i>
                            <?= $d["kota"] ?>, <?= $d["provinsi"] ?>
                        </p>

                        <p class="mb-2">
                            <span class="text-warning"><?= $d["rata_rating"] ?> ⭐</span>
                            <small>(<?= $d["jumlah_rating"] ?> ulasan)</small>
                        </p>

                        <a href="<?= APP_URL ?>/destinasi/detail.php?slug=<?= $d['slug'] ?>"
                           class="btn btn-sm btn-primary">
                           <i class="fa fa-eye"></i> Lihat
                        </a>

                        <a href="<?= APP_URL ?>/destinasi/unbookmark.php?id=<?= $d['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Hapus dari wishlist?');">
                           <i class="fa fa-trash"></i> Hapus
                        </a>

                    </div>
                </div>

            </div>
            <?php endwhile; ?>

        </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
