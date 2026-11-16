<?php
require __DIR__ . '/../app/auth.php';
require __DIR__ . '/../app/conexion.php';
require __DIR__ . '/../app/funciones.php';
exigirLogin('/perfil.php');

$esAdmin = (usuarioActual()['rol'] ?? null) === 'admin';
$coloresEstado = ['pendiente' => 'warning', 'aceptado' => 'success', 'rechazado' => 'danger'];
$bd = obtenerBD();
$idUsuario = new MongoDB\BSON\ObjectId(usuarioActual()['id']);
$usuarioBD = $bd->usuarios->findOne(['_id' => $idUsuario]);

$errores = [];
$nombre = $usuarioBD['nombre'];
$email = $usuarioBD['email'];
$accion = $_POST['accion'] ?? '';
$enviadoDatos = $accion === 'datos';
$enviadoPassword = $accion === 'password';

if ($accion === 'datos') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = mb_strtolower(trim($_POST['email'] ?? ''));

    if ($nombre === '') {
        $errores['nombre'] = 'El nombre es obligatorio';
    } elseif (mb_strlen($nombre) > 100) {
        $errores['nombre'] = 'El nombre no puede ser tan largo';
    }

    if ($email === '') {
        $errores['email'] = 'El email es obligatorio';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El email debe tener un formato correcto';
    } elseif (mb_strlen($email) > 100) {
        $errores['email'] = 'El email no puede ser tan largo';
    }

    if (empty($errores)) {
        try {
            $bd->usuarios->updateOne(
                ['_id' => $idUsuario],
                ['$set' => ['nombre' => $nombre, 'email' => $email]]
            );
            $_SESSION['usuario']['nombre'] = $nombre;
            $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'Datos actualizados'];
            header('Location: /perfil.php');
            exit;
        } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
            if ($e->getCode() === 11000) {
                $errores['email'] = 'Este email ya está registrado';
            } else {
                throw $e;
            }
        }
    }
} elseif ($accion === 'password') {
    $actual = $_POST['actual'] ?? '';
    $nueva = $_POST['nueva'] ?? '';
    $repetir = $_POST['repetir'] ?? '';

    if ($nueva === '') {
        $errores['nueva'] = 'La contraseña nueva es obligatoria';
    } elseif (mb_strlen($nueva) < 8) {
        $errores['nueva'] = 'La contraseña debe tener al menos 8 caracteres';
    }

    if ($repetir !== $nueva) {
        $errores['repetir'] = 'Las contraseñas no coinciden';
    }

    if (!password_verify($actual, $usuarioBD['password'])) {
        $errores['actual'] = 'La contraseña actual no es correcta';
    }

    if (empty($errores)) {
        $bd->usuarios->updateOne(
            ['_id' => $idUsuario],
            ['$set' => ['password' => password_hash($nueva, PASSWORD_DEFAULT)]]
        );
        $_SESSION['aviso'] = ['tipo' => 'success', 'texto' => 'Contraseña cambiada'];
        header('Location: /perfil.php');
        exit;
    }
}

$pedidos = [];
if (!$esAdmin) {
    $pedidos = $bd->pedidos->find(
        ['usuario' => $idUsuario],
        ['sort' => ['fecha' => -1]]
    )->toArray();
}

$titulo = 'Mi perfil';
require __DIR__ . '/../app/partials/header.php';
?>

<main class="flex-grow-1">
    <section class="py-5">
        <div class="container">
            <div class="border-bottom pb-3 mb-4">
                <h1 class="text-uppercase fs-2 mb-0">Mi perfil</h1>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card bg-black border-top border-3 borde-acento h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <h2 class="fs-4 mb-4">Mis datos</h2>
                            <form method="POST" novalidate class="flex-grow-1 d-flex flex-column">
                                <div>
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" autocomplete="name"
                                        class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : ($enviadoDatos ? 'is-valid' : '') ?>"
                                        id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required
                                        maxlength="100">
                                    <div class="invalid-feedback"><?= $errores['nombre'] ?? '' ?></div>
                                </div>
                                <div>
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" autocomplete="email"
                                        class="form-control <?= isset($errores['email']) ? 'is-invalid' : ($enviadoDatos ? 'is-valid' : '') ?>"
                                        id="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                                        maxlength="100">
                                    <div class="invalid-feedback"><?= $errores['email'] ?? '' ?></div>
                                </div>
                                <button type="submit" name="accion" value="datos"
                                    class="btn btn-acento w-100 mt-auto">Guardar datos</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card bg-black border-top border-3 borde-acento h-100">
                        <div class="card-body p-4 d-flex flex-column">
                            <h2 class="fs-4 mb-4">Contraseña</h2>
                            <form method="POST" novalidate class="flex-grow-1 d-flex flex-column">
                                <div>
                                    <label for="actual" class="form-label">Contraseña actual</label>
                                    <input type="password" autocomplete="current-password"
                                        class="form-control <?= isset($errores['actual']) ? 'is-invalid' : ($enviadoPassword ? 'is-valid' : '') ?>"
                                        id="actual" name="actual" required>
                                    <div class="invalid-feedback"><?= $errores['actual'] ?? '' ?></div>
                                </div>
                                <div>
                                    <label for="nueva" class="form-label">Contraseña nueva</label>
                                    <input type="password" autocomplete="new-password"
                                        class="form-control <?= isset($errores['nueva']) ? 'is-invalid' : ($enviadoPassword ? 'is-valid' : '') ?>"
                                        id="nueva" name="nueva" required>
                                    <div class="invalid-feedback"><?= $errores['nueva'] ?? '' ?></div>
                                </div>
                                <div>
                                    <label for="repetir" class="form-label">Repetir contraseña nueva</label>
                                    <input type="password" autocomplete="new-password"
                                        class="form-control <?= isset($errores['repetir']) ? 'is-invalid' : ($enviadoPassword ? 'is-valid' : '') ?>"
                                        id="repetir" name="repetir" required>
                                    <div class="invalid-feedback"><?= $errores['repetir'] ?? '' ?></div>
                                </div>
                                <button type="submit" name="accion" value="password"
                                    class="btn btn-acento w-100 mt-auto">Cambiar contraseña</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!$esAdmin): ?>
                <div class="d-flex align-items-baseline justify-content-between border-bottom pb-3 mb-4 mt-5">
                    <h2 class="text-uppercase fs-2 mb-0">Mis pedidos</h2>
                    <span class="text-body-secondary"><?= count($pedidos) ?> pedidos</span>
                </div>

                <?php if (count($pedidos) === 0): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bag display-1 text-body-secondary"></i>
                        <p class="lead mt-3">Todavía no tienes pedidos</p>
                        <a href="/tienda.php" class="btn btn-acento btn-lg">Ir a la tienda</a>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-4">
                        <?php foreach ($pedidos as $pedido): ?>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center gap-2 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="fs-5"><?= formatearFecha($pedido['fecha']) ?></span>
                                        <span
                                            class="badge text-capitalize text-bg-<?= $coloresEstado[$pedido['estado']] ?>"><?= $pedido['estado'] ?></span>
                                    </div>
                                    <span class="precio fs-4 fw-semibold"><?= formatearPrecio($pedido['total']) ?></span>
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
                                                <?php foreach ($pedido['lineas'] as $linea): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($linea['nombre']) ?></td>
                                                        <td class="text-end"><?= formatearPrecio($linea['precio']) ?></td>
                                                        <td class="text-end"><?= $linea['cantidad'] ?></td>
                                                        <td class="text-end">
                                                            <?= formatearPrecio($linea['precio'] * $linea['cantidad']) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
require __DIR__ . '/../app/partials/footer.php';