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
$coleccion = $bd->pedidos;
try {
    $resultado = $coleccion->updateOne(
        [
            "_id" => new MongoDB\BSON\ObjectId($id),
            "estado" => "pendiente"
        ],
        ['$set' => ['estado' => 'aceptado']]
    );
} catch (MongoDB\Driver\Exception\InvalidArgumentException) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El pedido no existe'];
    header('Location: /admin/solicitudes.php');
    exit;
}
if ($resultado->getMatchedCount() === 0) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El pedido no existe o ya fue revisado'];
} else {
    $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'El pedido se ha aceptado'];
}

header('Location: /admin/solicitudes.php');
exit;