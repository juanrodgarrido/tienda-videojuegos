<?php
require __DIR__ . '/../../app/auth.php';
require __DIR__ . '/../../app/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

exigirAdmin();

$id = trim($_POST['id'] ?? '');

try {
    $objectId = new MongoDB\BSON\ObjectId($id);
} catch (MongoDB\Driver\Exception\InvalidArgumentException) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El pedido no existe'];
    header('Location: /admin/solicitudes.php');
    exit;
}

$bd = obtenerBD();


$resultado = $bd->pedidos->updateOne(
    ['_id' => $objectId, 'estado' => 'pendiente'],
    ['$set' => ['estado' => 'rechazado']]
);

if ($resultado->getMatchedCount() === 0) {
    $_SESSION['aviso'] = ['tipo' => 'danger', 'texto' => 'El pedido no existe o ya fue revisado'];
    header('Location: /admin/solicitudes.php');
    exit;
}

$pedido = $bd->pedidos->findOne(['_id' => $objectId]);

foreach ($pedido['lineas'] as $linea) {
    $bd->productos->updateOne(
        ['_id' => $linea['producto']],
        ['$inc' => ['stock' => $linea['cantidad']]]
    );
}

$_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'Pedido rechazado y stock devuelto'];
header('Location: /admin/solicitudes.php');
exit;