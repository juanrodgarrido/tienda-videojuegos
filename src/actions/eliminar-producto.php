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
    $resultado = $coleccion->updateOne(
        [
            "_id" => new MongoDB\BSON\ObjectId($id),
        ],
        ['$set' => ['activo' => false, 'destacado' => false]]
    );
} catch (MongoDB\Driver\Exception\InvalidArgumentException) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe'];
    header('Location: /tienda.php');
    exit;
}
if ($resultado->getMatchedCount() === 0) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El producto no existe'];
} else if ($resultado->getModifiedCount() === 0) {
    $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'El producto ya había sido borrado'];
} else {
    $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'El producto se ha borrado correctamente'];
}

header('Location: /tienda.php');
exit;