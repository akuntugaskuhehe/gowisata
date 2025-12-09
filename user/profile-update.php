<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

$user_id = user()["id"];

$nama = mysqli_real_escape_string($conn, $_POST["nama"]);
$email = mysqli_real_escape_string($conn, $_POST["email"]);

// cek email unik
$check = mysqli_query($conn, "
    SELECT id FROM users WHERE email='$email' AND id != $user_id
");

if (mysqli_num_rows($check) > 0) {
    echo "<script>alert('Email sudah digunakan!');window.history.back();</script>";
    exit;
}

// upload foto profil
$foto_name = null;

if (!empty($_FILES["foto"]["name"])) {

    $foto_name = time() . "_" . $_FILES["foto"]["name"];
    $target = __DIR__ . "/../assets/img/user/" . $foto_name;

    move_uploaded_file($_FILES["foto"]["tmp_name"], $target);

    // update foto
    mysqli_query($conn, "
        UPDATE users SET foto='$foto_name' WHERE id=$user_id
    ");
}

// update data user
mysqli_query($conn, "
    UPDATE users SET 
        nama='$nama', 
        email='$email'
    WHERE id=$user_id
");

// update session
$_SESSION["user"]["nama"]  = $nama;
$_SESSION["user"]["email"] = $email;

if ($foto_name) {
    $_SESSION["user"]["foto"] = $foto_name;
}

header("Location: " . APP_URL . "/user/profile.php");
exit;
