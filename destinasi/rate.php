<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

requireLogin();

// ===========================
// Ambil dan validasi input
// ===========================
$user_id = user()["id"];

$dest     = intval($_POST["destinasi_id"]);
$rating   = intval($_POST["rating"]);
$komentar = mysqli_real_escape_string($conn, $_POST["komentar"] ?? "");

// Rating harus 1–5
if ($rating < 1 || $rating > 5) {
    redirect(APP_URL . "/destinasi/detail.php?id=$dest");
}

// ===========================
// Insert / update ulasan (ON DUPLICATE KEY)
// Pastikan di tabel ulasan ada UNIQUE(destinasi_id, pengguna_id)
// ===========================
mysqli_query($conn, "
    INSERT INTO ulasan (destinasi_id, pengguna_id, rating, komentar)
    VALUES ($dest, $user_id, $rating, '$komentar')
    ON DUPLICATE KEY UPDATE rating=$rating, komentar='$komentar'
");

// ===========================
// Hitung ulang rating rata-rata
// ===========================
$avg = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT ROUND(AVG(rating), 2) AS rata, COUNT(*) AS jml
    FROM ulasan
    WHERE destinasi_id=$dest
"));

$rata = $avg["rata"];
$jml  = $avg["jml"];

// Update row destinasi
mysqli_query($conn, "
    UPDATE destinasi SET 
        rata_rating=$rata,
        jumlah_rating=$jml
    WHERE id=$dest
");

// Redirect kembali ke detail
redirect(APP_URL . "/destinasi/detail.php?id=$dest");
