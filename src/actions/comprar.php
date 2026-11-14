<?php
require __DIR__ . '/../../app/auth.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

exigirLogin("/cesta.php");


require __DIR__ . '/../../app/conexion.php';
$cesta = $_SESSION['cesta'] ?? [];
$bd = obtenerBD();
$coleccion = $bd->productos;

$descontadas = [];
$sinStock = [];

foreach ($cesta as $clave => $cantidad) {
    $resultado = $coleccion->updateOne(
        [
            "_id" => new MongoDB\BSON\ObjectId($clave),
            "activo" => true,
            "stock" => ['$gte' => $cantidad]
        ],
        ['$inc' => ['stock' => -$cantidad]]
    );
    if ($resultado->getModifiedCount() === 1) {
        $descontadas[$clave] = $cantidad;
    } else {
        $juegoSinStock = $coleccion->findOne(["_id" => new MongoDB\BSON\ObjectId($clave)]);
        $sinStock[] = $juegoSinStock['nombre'];


    }
}

if (count($sinStock) > 0) {
    foreach ($descontadas as $claveDescontada => $cantidadDescontada) {
        $coleccion->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($claveDescontada)],
            ['$inc' => ['stock' => $cantidadDescontada]]
        );
    }
    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'No hay suficiente stock de ' . implode(', ', $sinStock)];

    header('Location: /cesta.php');
    exit;
}

