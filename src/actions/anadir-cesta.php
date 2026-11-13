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
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'Cantidad no válida'];
    header('Location: /tienda.php');
    exit;
}

$bd = obtenerBD();
$coleccion = $bd->productos;

try {
    $producto = $coleccion->findOne(["activo" => true, "_id" => new MongoDB\BSON\ObjectId($id)]);
} catch (MongoDB\Driver\Exception\InvalidArgumentException) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe'];
    header('Location: /tienda.php');
    exit;
}

if ($producto === null) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe'];
    header('Location: /tienda.php');
    exit;
}

$clave = (string) $producto['_id'];
$enCesta = $_SESSION['cesta'][$clave] ?? 0;
$disponible = $producto['stock'] - $enCesta;
$aAnadir = min($cantidad, $disponible);
if ($aAnadir < 1) {
    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'Ya tienes en la cesta todas las unidades disponibles'];
    header('Location: /tienda.php');
    exit;
}
if($aAnadir !== $cantidad){
    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'No quedan tantas unidades, se han añadido '.$aAnadir];
}else{
    $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => $producto['nombre'].' añadido a la cesta'];
}

$_SESSION['cesta'][$clave] = $enCesta + $aAnadir;

header('Location: /tienda.php');
exit;
