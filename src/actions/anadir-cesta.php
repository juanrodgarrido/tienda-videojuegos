<?php
require __DIR__ . '/../../app/auth.php';
require __DIR__ . '/../../app/conexion.php';



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$id = trim($_POST['id'] ?? "");
$cantidad = filter_var($_POST['cantidad'] ?? "", FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);


if ($cantidad === false) {
    header('Location: /tienda.php');
    exit;
}

$bd = obtenerBD();
$coleccion = $bd->productos;

try {
    $producto = $coleccion->findOne(["activo" => true, "_id" => new MongoDB\BSON\ObjectId($id)]);
} catch (MongoDB\Driver\Exception\InvalidArgumentException) {
    header('Location: /tienda.php');
    exit;
}

if ($producto === null) {
    header('Location: /tienda.php');
    exit;
}

$clave = (string) $producto['_id'];
$enCesta = $_SESSION['cesta'][$clave] ?? 0;
$disponible = $producto['stock'] - $enCesta;
$aAnadir = min($cantidad, $disponible);
if ($aAnadir < 1) {
    header('Location: /tienda.php');
    exit;
}

$_SESSION['cesta'][$clave] = $enCesta + $aAnadir;
header('Location: /tienda.php');
exit;
