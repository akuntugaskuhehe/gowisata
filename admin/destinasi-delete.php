<?php
require_once __DIR__ . '/../includes/db.php';

$id = $_GET["id"];

mysqli_query($conn, "DELETE FROM gambar_destinasi WHERE destinasi_id=$id");
mysqli_query($conn, "DELETE FROM ulasan WHERE destinasi_id=$id");
mysqli_query($conn, "DELETE FROM bookmark WHERE destinasi_id=$id");
mysqli_query($conn, "DELETE FROM destinasi WHERE id=$id");

header("Location: " . APP_URL . "/admin/destinasi-index.php");
exit;
