<?php
require __DIR__ . '/../../app/auth.php';



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$_SESSION = [];
setcookie(session_name(), '', time() - 3600, '/');
session_destroy();
header('Location: /');
exit;