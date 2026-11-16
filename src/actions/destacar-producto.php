<?php
require __DIR__ . '/../../app/auth.php';
require __DIR__ . '/../../app/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

exigirAdmin();

$id = trim($_POST['id'] ?? '');
$bd = obtenerBD();
$coleccion = $bd->productos;

try {
    $objectId = new MongoDB\BSON\ObjectId($id);
} catch (MongoDB\Driver\Exception\InvalidArgumentException) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe'];
    header('Location: /tienda.php');
    exit;
}


$resultado = $coleccion->updateOne(
    [
        "_id" => $objectId,
        "activo" => true
    ],
    ['$set' => ['destacado' => true]]
);

if ($resultado->getMatchedCount() === 0) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe o está eliminado'];
    header('Location: /tienda.php');
    exit;
}


$coleccion->updateMany(
    [
        "destacado" => true,
        "_id" => ['$ne' => $objectId]
    ],
    ['$set' => ['destacado' => false,]]
);


if ($resultado->getModifiedCount() === 0) {
    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'Este producto ya es el destacado'];
} else {
    $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'El producto se ha destacado correctamente'];
}
header('Location: /tienda.php');
exit;