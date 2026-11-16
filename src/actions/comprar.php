<?php
require __DIR__ . '/../../app/auth.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

exigirLogin("/cesta.php");



$cesta = $_SESSION['cesta'] ?? [];

if (count($cesta) === 0) {
    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'La cesta está vacía'];
    header('Location: /cesta.php');
    exit;
}

if (usuarioActual()['rol'] === "admin") {
    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'La cuenta de administrador no puede comprar'];
    header('Location: /');
    exit;
}

require __DIR__ . '/../../app/conexion.php';
$bd = obtenerBD();
$coleccion = $bd->productos;

$descontadas = [];
$sinStock = [];
$eliminados = [];

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
        $juego = $coleccion->findOne(["_id" => new MongoDB\BSON\ObjectId($clave)]);

        if ($juego === null || $juego['activo'] === false) {
            unset($_SESSION['cesta'][$clave]);
            $eliminados[] = $juego === null ? 'un producto que ya no existe' : $juego['nombre'];
        } else {
            $sinStock[] = $juego['nombre'];
        }
    }

}


if (count($sinStock) > 0 || count($eliminados) > 0) {
    foreach ($descontadas as $claveDescontada => $cantidadDescontada) {
        $coleccion->updateOne(
            ["_id" => new MongoDB\BSON\ObjectId($claveDescontada)],
            ['$inc' => ['stock' => $cantidadDescontada]]
        );
    }
    $texto = '';

    if (count($eliminados) > 0) {
        $texto .= 'Se han quitado de tu cesta porque ya no están disponibles: ' . implode(', ', $eliminados) . '. ';
    }
    if (count($sinStock) > 0) {
        $texto .= 'No hay suficiente stock de ' . implode(', ', $sinStock) . '.';
    }

    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => $texto];

    header('Location: /cesta.php');
    exit;
}

$lineas = [];
$total = 0;

foreach ($descontadas as $idDescontadas => $cantidad) {
    $producto = $coleccion->findOne(["_id" => new MongoDB\BSON\ObjectId($idDescontadas)]);
    $lineas[] = [
        'producto' => $producto['_id'],
        'nombre' => $producto['nombre'],
        'precio' => $producto['precio'],
        'cantidad' => $cantidad
    ];
    $total += $producto['precio'] * $cantidad;
}

$bd->pedidos->insertOne([
    'usuario' => new MongoDB\BSON\ObjectId($_SESSION['usuario']['id']),
    'lineas' => $lineas,
    'total' => $total,
    'fecha' => new MongoDB\BSON\UTCDateTime(),
    'estado' => 'pendiente'
]);
unset($_SESSION['cesta']);
$_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'Pedido realizado. Está pendiente de que lo revise la tienda'];
header('Location: /');
exit;