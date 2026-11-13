<?php
require __DIR__ . '/../../app/auth.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}


$id = trim($_POST['id'] ?? "");
unset($_SESSION['cesta'][$id]);
header('Location: /cesta.php');
exit;