<?php
require_once __DIR__ . '/../includes/db.php';

$id = $_GET["id"];
$dest = $_GET["dest"];

mysqli_query($conn, "DELETE FROM gambar_destinasi WHERE id=$id");
header("Location: " . APP_URL . "/admin/destinasi-edit.php?id=$dest");
