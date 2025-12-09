<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

$user = user();
$id = $user["id"];

$data = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM users WHERE id=$id
"));
?>

<h3><i class="fa fa-user"></i> Profil Saya</h3>
<hr>

<div class="row">
    <div class="col-md-4 text-center">

        <img src="<?= APP_URL ?>/assets/img/user/<?= $data['foto'] ?? 'default.png' ?>" 
             class="rounded-circle mb-3"
             width="150" height="150" 
             style="object-fit:cover;">

    </div>

    <div class="col-md-8">

        <form method="POST" action="<?= APP_URL ?>/user/profile-update.php"
              enctype="multipart/form-data">

            <label>Nama</label>
            <input type="text" name="nama" class="form-control mb-2"
                   value="<?= $data['nama'] ?>" required>

            <label>Email</label>
            <input type="email" name="email" class="form-control mb-2"
                   value="<?= $data['email'] ?>" required>

            <label>Foto Profil</label>
            <input type="file" name="foto" class="form-control mb-3">

            <button class="btn btn-success">
                <i class="fa fa-save"></i> Simpan Perubahan
            </button>
        </form>

    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
