<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

if (isLoggedIn()) {
    redirect("/");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["password"];

    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");

    if (mysqli_num_rows($q) === 1) {
        $u = mysqli_fetch_assoc($q);

        if (password_verify($password, $u["password_hash"])) {

            $_SESSION["user"] = [
                "id"    => $u["id"],
                "nama"  => $u["nama"],
                "email" => $u["email"],
                "role"  => $u["peran"],
            ];

            redirect("/");
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="mx-auto" style="max-width:420px;">
    <h3 class="mb-3">Login</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <label>Email</label>
        <input type="email" name="email" class="form-control mb-2" required>

        <label>Password</label>
        <input type="password" name="password" class="form-control mb-3" required>

        <button class="btn btn-success w-100">
            <i class="fa fa-sign-in-alt"></i> Login
        </button>
    </form>

    <hr>
    <p>Belum punya akun?  
       <a href="<?= APP_URL ?>/auth/register.php">Daftar di sini</a>
    </p>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
