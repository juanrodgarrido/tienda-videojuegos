<?php
require __DIR__ . '/../app/auth.php';
require __DIR__ . "/../app/conexion.php";
require __DIR__ . "/../app/funciones.php";

$bd = obtenerBD();
$coleccion = $bd->productos;
$rejilla = $coleccion->find(["activo" => true, "destacado" => ['$ne' => true]], ["sort" => ["_id" => -1]]);
$destacado = $coleccion->findOne(["activo" => true, "destacado" => true]);

$titulo = "Tienda";
require __DIR__ . "/../app/partials/header.php";
?>
<main>
    <?php if ($destacado): ?>
        <section class="bg-black py-5 border-bottom border-3 borde-acento">
            <div class="container">
                <div class="row gy-4 gx-lg-5 align-items-center">
                    <div class="col-lg-5">
                        <span class="badge border borde-acento text-acento fs-6 mb-3 text-uppercase">Destacado</span>
                        <h1 class="mb-3 display-5 fw-bold text-uppercase"><?= htmlspecialchars($destacado['nombre']) ?></h1>
                        <p class="lead text-body-secondary"><?= htmlspecialchars($destacado['descripcion']) ?></p>
                        <p class="precio fs-2 fw-bold"><?= formatearPrecio($destacado['precio']) ?></p>
                        <?php if ($destacado['stock'] > 0): ?>
                            <div class="d-flex gap-3 align-items-end">
                                <div>
                                    <label for="cantidad-destacado" class="form-label">Cantidad</label>
                                    <input id="cantidad-destacado" name="cantidad"
                                        class="form-control form-control-lg input-cantidad" type="number" value="1" min="1"
                                        max="<?= $destacado['stock'] ?>">
                                </div>
                                <button class="btn btn-acento btn-lg" type="button">Añadir a la cesta</button>
                            </div>

                        <?php else: ?>
                            <button disabled class="btn btn-outline-secondary btn-lg">Agotado</button>
                        <?php endif; ?>

                    </div>
                    <div class="col-lg-7">
                        <?php if (!$destacado['imagen']): ?>
                            <div class="ratio ratio-16x9 bg-secondary rounded overflow-hidden"></div>
                        <?php else: ?>
                            <div class="ratio ratio-16x9 bg-secondary  rounded overflow-hidden">
                                <img class="object-fit-cover" alt="<?= htmlspecialchars($destacado['nombre']) ?>"
                                    src="<?= htmlspecialchars($destacado['imagen']) ?>">
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-baseline justify-content-between border-bottom pb-3 mb-4">
                <h2 class="text-uppercase mb-0">Catálogo</h2>
                <span class="text-body-secondary">Lo último, primero</span>
            </div>
            <div class="row g-4">
                <?php foreach ($rejilla as $producto): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            <?php if (!$producto['imagen']): ?>
                                <div class="ratio ratio-16x9 bg-secondary card-img-top overflow-hidden"></div>
                            <?php else: ?>
                                <div class="ratio ratio-16x9 bg-secondary  card-img-top overflow-hidden">
                                    <img class="object-fit-cover" alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                        src="<?= htmlspecialchars($producto['imagen']) ?>">
                                </div>
                            <?php endif; ?>
                            <?php if ($producto['stock'] < 1): ?>
                                <span
                                    class="badge bg-black m-2 border position-absolute top-0 start-0 text-uppercase">Agotado</span>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between gap-2 mb-2">
                                    <h3 class="card-title fs-5 mb-0"><?= htmlspecialchars($producto['nombre']) ?></h3>
                                    <span
                                        class="precio text-nowrap fw-bold"><?= formatearPrecio($producto['precio']) ?></span>
                                </div>
                                <p class="text-body-secondary"><?= htmlspecialchars($producto['descripcion']) ?></p>
                                <?php if ($producto['stock'] > 0): ?>
                                    <div class="d-flex gap-2 align-items-center mt-auto">
                                        <label for="cantidad-<?= $producto['_id'] ?>">Cant.</label>
                                        <input id="cantidad-<?= $producto['_id'] ?>" name="cantidad"
                                            class="form-control input-cantidad" type="number" value="1" min="1"
                                            max="<?= $producto['stock'] ?>">
                                        <button class="btn btn-acento flex-grow-1" type="button">Añadir</button>

                                    </div>

                                <?php else: ?>
                                    <button disabled class="btn btn-outline-secondary w-100 opacity-100 mt-auto">Agotado</button>
                                <?php endif; ?>
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

