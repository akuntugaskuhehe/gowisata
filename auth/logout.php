<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../core/session.php';
require_once __DIR__ . '/../core/functions.php';

session_destroy();

header("Location: " . APP_URL . "/");
exit;
