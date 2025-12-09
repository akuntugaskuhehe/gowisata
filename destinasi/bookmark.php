<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/session.php';

requireLogin();

$user = user()["id"];
$dest = $_GET["id"];

mysqli_query($conn, "
    INSERT INTO bookmark(pengguna_id, destinasi_id)
    VALUES($user, $dest)
    ON DUPLICATE KEY UPDATE destinasi_id=destinasi_id
");

header("Location: " . APP_URL . "/user/wishlist.php");
exit;
