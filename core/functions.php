<?php
function slugify($text){
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  $text = trim($text, '-');
  $text = preg_replace('~-+~', '-', $text);
  return strtolower($text);
}

function redirect($url)
{
    // Jika URL sudah absolute (mengandung http:// atau https://), langsung redirect
    if (preg_match('/^https?:\/\//', $url)) {
        header("Location: $url");
        exit;
    }

    // Jika URL relatif ─ otomatis prepend APP_URL
    header("Location: " . APP_URL . $url);
    exit;
}

function isBookmarked($conn, $user_id, $destinasi_id)
{
    $user_id = intval($user_id);
    $destinasi_id = intval($destinasi_id);

    $q = mysqli_query($conn, "
        SELECT id FROM bookmark 
        WHERE pengguna_id=$user_id AND destinasi_id=$destinasi_id
        LIMIT 1
    ");

    return mysqli_num_rows($q) > 0;
}

function addBookmark($conn, $user_id, $destinasi_id)
{
    $user_id = intval($user_id);
    $destinasi_id = intval($destinasi_id);

    // tidak boleh duplikat
    mysqli_query($conn, "
        INSERT IGNORE INTO bookmark (pengguna_id, destinasi_id)
        VALUES ($user_id, $destinasi_id)
    ");
}

function removeBookmark($conn, $user_id, $destinasi_id)
{
    $user_id = intval($user_id);
    $destinasi_id = intval($destinasi_id);

    mysqli_query($conn, "
        DELETE FROM bookmark
        WHERE pengguna_id=$user_id AND destinasi_id=$destinasi_id
    ");
}


?>
