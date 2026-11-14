<?php
require __DIR__ . '/../app/auth.php';
require __DIR__ . '/../app/conexion.php';
require __DIR__ . '/../app/funciones.php';
$titulo = "Cesta";

$cesta = $_SESSION['cesta'] ?? [];
$lineas = [];
$total = 0;
$bd = obtenerBD();
$coleccion = $bd->productos;

foreach ($cesta as $clave => $cantidad) {
    $producto = $coleccion->findOne(["activo" => true, "_id" => new MongoDB\BSON\ObjectId($clave)]);
    if ($producto !== null) {
        $subtotal = $cantidad * $producto['precio'];
        $lineas[] = [
            'producto' => $producto,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
        ];
        $total = $total + $subtotal;
    }
}


require __DIR__ . "/../app/partials/header.php";
?>


<main class="flex-grow-1">
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-baseline justify-content-between border-bottom pb-3 mb-4">
                <h1 class="text-uppercase fs-2 mb-0">Tu cesta</h1>
            </div>

            <?php if (count($lineas) === 0): ?>
                <div class="text-center py-5">
                    <i class="bi bi-cart-x display-1 text-body-secondary"></i>
                    <p class="lead mt-3">Tu cesta está vacía</p>
                    <a href="/tienda.php" class="btn btn-acento btn-lg">Ir a la tienda</a>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="d-flex flex-column gap-3">
                            <?php foreach ($lineas as $linea): ?>
                                <?php $producto = $linea['producto']; ?>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-4 col-md-2">
                                                <div class="ratio ratio-16x9 bg-secondary rounded overflow-hidden">
                                                    <?php if ($producto['imagen']): ?>
                                                        <img class="object-fit-cover"
                                                            alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                                            src="<?= htmlspecialchars($producto['imagen']) ?>">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="col-8 col-md-6">
                                                <h2 class="fs-6 mb-1"><?= htmlspecialchars($producto['nombre']) ?></h2>
                                                <span
                                                    class="text-body-secondary small"><?= formatearPrecio($producto['precio']) ?>
                                                    / ud.</span>
                                            </div>
                                            <div class="col-6 col-md-2">
                                                <span class="text-body-secondary">Cant.</span> <?= $linea['cantidad'] ?>
                                            </div>
                                            <div class="col-6 col-md-2  d-flex justify-content-end align-items-center gap-3">
                                                <span class="precio fw-bold"><?= formatearPrecio($linea['subtotal']) ?></span>
                                                <form action="/actions/quitar-cesta.php" method="POST">
                                                    <input type="hidden" name="id" value="<?= $producto['_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                        aria-label="Quitar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card bg-black border-top border-3 borde-acento">
                            <div class="card-body">
                                <h2 class="fs-5 text-uppercase mb-3">Resumen</h2>
                                <div class="d-flex justify-content-between align-items-baseline mb-4">
                                    <span>Total</span>
                                    <span class="precio fs-3 fw-bold"><?= formatearPrecio($total) ?></span>
                                </div>  
                                <form action = "/actions/comprar.php" method="POST">
                                    <button type="submit" class="btn btn-acento btn-lg w-100 mb-2">Comprar</button>                                     
                                </form>                                    
                                <a href="/tienda.php" class="btn btn-outline-secondary w-100">Seguir comprando</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>



<?php
require __DIR__ . "/../app/partials/footer.php";

