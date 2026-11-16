<?php
require __DIR__ . '/../app/auth.php';
require __DIR__ . "/../app/conexion.php";
require __DIR__ . "/../app/funciones.php";

$esAdmin = (usuarioActual()['rol'] ?? null) === 'admin';
$bd = obtenerBD();
$coleccion = $bd->productos;


$filtro = ["destacado" => ['$ne' => true]];
if (!$esAdmin) {
    $filtro['activo'] = true;
}
$rejilla = $coleccion->find($filtro, ["sort" => ["_id" => -1]]);
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
                        <?php if ($esAdmin): ?>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="text-body-secondary">Stock: <?= $destacado['stock'] ?></span>
                                <div class="boton-igual">
                                    <a class="btn btn-outline-light w-100 btn-lg"
                                        href="/admin/producto.php?id=<?= $destacado['_id'] ?>">Editar</a>
                                </div>
                                <form method="POST" action="/actions/eliminar-producto.php" class="boton-igual">
                                    <input type="hidden" name="id" value="<?= $destacado['_id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-lg w-100">
                                        Eliminar
                                    </button>

                                </form>

                            </div>
                        <?php elseif ($destacado['stock'] > 0): ?>
                            <form method="POST" action="/actions/anadir-cesta.php" class="d-flex gap-3 align-items-end">
                                <div>
                                    <input type="hidden" name="id" value="<?= $destacado['_id'] ?>">
                                    <label for="cantidad-destacado" class="form-label">Cantidad</label>
                                    <input id="cantidad-destacado" name="cantidad"
                                        class="form-control form-control-lg input-cantidad" type="number" value="1" min="1"
                                        max="<?= $destacado['stock'] ?>">
                                </div>
                                <button class="btn btn-acento btn-lg" type="submit">Añadir a la cesta</button>
                            </form>

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

                            <div class="d-flex flex-column gap-1 position-absolute top-0 start-0 m-2 align-items-start">
                                <?php if ($producto['stock'] < 1): ?>
                                    <span class="badge bg-black border text-uppercase">Agotado
                                    </span>
                                <?php endif; ?>
                                <?php if ($producto['activo'] === false): ?>
                                    <span class="badge bg-black border text-uppercase">Eliminado
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($esAdmin):
                                if ($producto['activo'] === true): ?>
                                    <form method="POST" action="/actions/eliminar-producto.php"
                                        class="position-absolute top-0 end-0 m-2">
                                        <input type="hidden" name="id" value="<?= $producto['_id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" aria-label="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </form>
                                <?php else: ?>
                                    <form method="POST" action="/actions/recuperar-producto.php"
                                        class="position-absolute top-0 end-0 m-2">
                                        <input type="hidden" name="id" value="<?= $producto['_id'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm border">
                                            Recuperar
                                        </button>

                                    </form>

                                <?php endif;
                            endif; ?>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between gap-2 mb-2">
                                    <h3 class="card-title fs-5 mb-0"><?= htmlspecialchars($producto['nombre']) ?></h3>
                                    <span
                                        class="precio text-nowrap fw-bold"><?= formatearPrecio($producto['precio']) ?></span>
                                </div>
                                <p class="text-body-secondary"><?= htmlspecialchars($producto['descripcion']) ?></p>
                                <?php if ($esAdmin): ?>
                                    <div class="d-flex gap-2 align-items-center mt-auto">
                                        <span class="text-body-secondary">Stock: <?= $producto['stock'] ?></span>
                                        <div class="boton-igual">
                                            <a class="btn btn-outline-light w-100"
                                                href="/admin/producto.php?id=<?= $producto['_id'] ?>">Editar</a>
                                        </div>
                                        <?php if ($producto['activo']): ?>
                                            <form class="boton-igual" method="POST" action="/actions/destacar-producto.php">
                                                <input type="hidden" name="id" value="<?= $producto['_id'] ?>">
                                                <button type="submit" class="btn w-100 btn-acento">
                                                    Destacar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>


                                <?php elseif ($producto['stock'] > 0): ?>
                                    <form method="POST" action="/actions/anadir-cesta.php"
                                        class="d-flex gap-2 align-items-center mt-auto">
                                        <input type="hidden" name="id" value="<?= $producto['_id'] ?>">
                                        <label for="cantidad-<?= $producto['_id'] ?>">Cant.</label>
                                        <input id="cantidad-<?= $producto['_id'] ?>" name="cantidad"
                                            class="form-control input-cantidad" type="number" value="1" min="1"
                                            max="<?= $producto['stock'] ?>">
                                        <button class="btn btn-acento flex-grow-1" type="submit">Añadir</button>

                                    </form>

                                <?php else: ?>
                                    <button disabled
                                        class="btn btn-outline-secondary w-100 opacity-100 mt-auto">Agotado</button>
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

