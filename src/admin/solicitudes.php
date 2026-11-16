<?php
require __DIR__ . '/../../app/auth.php';
require __DIR__ . '/../../app/conexion.php';
require __DIR__ . '/../../app/funciones.php';
exigirAdmin();
$bd = obtenerBD();
$coleccion = $bd->pedidos;
$listaPedidos = $coleccion->find(['estado' => 'pendiente'], ['sort' => ['fecha' => 1]]);
$solicitudes = [];
foreach ($listaPedidos as $pedido) {
    $comprador = $bd->usuarios->findOne(['_id' => $pedido['usuario']]);

    $solicitudes[] = [
        'id' => (string) $pedido['_id'],
        'comprador' => $comprador['nombre'] ?? 'Usuario eliminado',
        'fecha' => $pedido['fecha'],
        'total' => $pedido['total'],
        'lineas' => $pedido['lineas'],
    ];
}

$titulo = 'Solicitudes';
require __DIR__ . '/../../app/partials/header.php';
?>

<main class="flex-grow-1">
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-baseline justify-content-between border-bottom pb-3 mb-4">
                <h1 class="text-uppercase fs-2 mb-0">Solicitudes</h1>
                <span class="text-body-secondary"><?= count($solicitudes) ?> pendientes</span>
            </div>

            <?php if (count($solicitudes) === 0): ?>
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-body-secondary"></i>
                    <p class="lead mt-3">No hay solicitudes pendientes</p>
                    <a href="/tienda.php" class="btn btn-acento btn-lg">Ir a la tienda</a>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-4">
                    <?php foreach ($solicitudes as $solicitud): ?>
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center gap-2 py-3">
                                <div>
                                    <h2 class="fs-5 mb-0"><?= htmlspecialchars($solicitud['comprador']) ?></h2>
                                    <span class="text-body-secondary small"><?= formatearFecha($solicitud['fecha']) ?></span>
                                </div>
                                <span class="precio fs-4 fw-semibold"><?= formatearPrecio($solicitud['total']) ?></span>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Producto</th>
                                                <th scope="col" class="text-end col-numero">Precio</th>
                                                <th scope="col" class="text-end col-numero">Cant.</th>
                                                <th scope="col" class="text-end col-numero">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($solicitud['lineas'] as $linea): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($linea['nombre']) ?></td>
                                                    <td class="text-end"><?= formatearPrecio($linea['precio']) ?></td>
                                                    <td class="text-end"><?= $linea['cantidad'] ?></td>
                                                    <td class="text-end"><?= formatearPrecio($linea['precio'] * $linea['cantidad']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-end gap-2 py-3">
                                <form method="POST" action="/actions/rechazar-pedido.php">
                                    <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger">Rechazar</button>
                                </form>
                                <form method="POST" action="/actions/aceptar-pedido.php">
                                    <input type="hidden" name="id" value="<?= $solicitud['id'] ?>">
                                    <button type="submit" class="btn btn-acento">Aceptar</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
require __DIR__ . '/../../app/partials/footer.php';

