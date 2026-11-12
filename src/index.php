<?php
require __DIR__ . "/../app/conexion.php";
require __DIR__ . "/../app/funciones.php";
$bd = obtenerBD();
$coleccion = $bd->productos;
$ultimos = $coleccion->find(["activo" => true], ["sort" => ["_id" => -1], "limit" => 4]);
$headerTransparente = true;
$titulo = "Inicio";
require __DIR__ . "/../app/partials/header.php";
?>

<main>
    <section class="hero d-flex align-items-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold text-uppercase">Compra tus videojuegos al mejor precio</h1>
                    <p class="lead mb-4">Claves para PC y consolas en menos de 1 minuto</p>
                    <a href="/tienda.php" class="btn btn-acento btn-lg">Ver tienda</a>
                </div>
            </div>

        </div>
    </section>
    <section class="py-5">
        <div class="container">
            <h2 class="text-uppercase mb-4">Últimas salidas</h2>
            <div class="row g-4">
                <?php foreach ($ultimos as $producto): ?>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100">
                            <?php if (!$producto['imagen']): ?>
                                <div class="card-img-top ratio ratio-16x9 bg-secondary overflow-hidden"></div>
                            <?php else: ?>
                                <div class="card-img-top ratio ratio-16x9 bg-secondary overflow-hidden">
                                    <img class="object-fit-cover" alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                        src="<?= htmlspecialchars($producto['imagen']) ?>">
                                </div>
                            <?php endif; ?>
                            <?php if ($producto['stock'] < 1): ?>
                                <span
                                    class="badge bg-black m-2 border position-absolute top-0 start-0 text-uppercase">Agotado</span>
                            <?php endif; ?>
                            <div class="card-body d-flex justify-content-between gap-2">
                                <h3 class="card-title fs-6 mb-0">
                                    <a href="/tienda.php"
                                        class="stretched-link text-reset text-decoration-none"><?= htmlspecialchars($producto['nombre']) ?></a>
                                </h3>
                                <span class="precio fw-bold text-nowrap"><?= formatearPrecio($producto['precio']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>


<?php
require __DIR__ . "/../app/partials/footer.php";