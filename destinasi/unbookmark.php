<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

$user = user()["id"];
$dest = $_GET["id"];

mysqli_query($conn, "
    DELETE FROM bookmark
    WHERE pengguna_id=$user AND destinasi_id=$dest
");

header("Location: " . APP_URL . "/user/wishlist.php");
exit;
