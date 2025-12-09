<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

if (isLoggedIn()) {
    redirect("/");
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = mysqli_real_escape_string($conn, $_POST["nama"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    if ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $q = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");

        if (mysqli_num_rows($q) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            mysqli_query($conn, "
                INSERT INTO users (nama,email,password_hash)
                VALUES ('$nama','$email','$hash')
            ");
            $success = "Pendaftaran berhasil! Silakan login.";
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="mx-auto" style="max-width:460px;">
    <h3 class="mb-3">Daftar Akun</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Nama</label>
        <input type="text" name="nama" class="form-control mb-2" required>

        <label>Email</label>
        <input type="email" name="email" class="form-control mb-2" required>

        <label>Password</label>
        <input type="password" name="password" class="form-control mb-2" required>

        <label>Konfirmasi Password</label>
        <input type="password" name="confirm" class="form-control mb-3" required>

        <button class="btn btn-success w-100">
            <i class="fa fa-user-plus"></i> Daftar
        </button>

    </form>

    <hr>
    <p>Sudah punya akun?  
       <a href="<?= APP_URL ?>/auth/login.php">Login di sini</a>
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
