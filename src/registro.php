<?php
require __DIR__ . '/../app/auth.php';
require __DIR__ . '/../app/conexion.php';
$titulo = "Registrarse";
$errores = [];
$email = "";
$nombre = "";
$bd = obtenerBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');

    if ($email === '') {
        $errores['email'] = "El email es obligatorio";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = "El email debe tener un formato correcto";
    } elseif (mb_strlen($email) > 100) {
        $errores['email'] = "El email no puede ser tan largo";
    }

    if ($password === '') {
        $errores['password'] = "La contraseña es obligatoria";
    } elseif (mb_strlen($password) < 8) {
        $errores['password'] = "La contraseña debe tener al menos 8 caracteres";
    }

    if ($nombre === '') {
        $errores['nombre'] = "El nombre es obligatorio";
    } elseif (mb_strlen($nombre) > 100) {
        $errores['nombre'] = "El nombre no puede ser tan largo";
    }

    if (empty($errores)) {
        $coleccionUsuarios = $bd->usuarios;
        try {
            $coleccionUsuarios->insertOne([
                'nombre' => $nombre,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'rol' => "comprador"
            ]);
            header('Location: /login.php?registrado=1');
            exit;


        } catch (MongoDB\Driver\Exception\BulkWriteException $e) {
            $codigoerror = $e->getCode();
            if ($codigoerror === 11000) {
                $errores['email'] = "Este email ya está registrado";
            } else {
                throw $e;
            }
        }



    }

}




require __DIR__ . "/../app/partials/header.php";
?>
<main class="flex-grow-1 d-flex align-items-center py-5">
    <div class="container contenedor-estrecho">
        <h1 class="text-center mb-4">Crear <span class="text-acento">cuenta</span></h1>

        <div class="card bg-black border-top border-3 borde-acento">
            <div class="card-body p-4">

                <form method="POST" novalidate>
                    <div>
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" autocomplete="name"
                            class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : '' ?>" id="nombre"
                            name="nombre" value="<?= htmlspecialchars($nombre) ?>" required maxlength="100">
                        <div class="invalid-feedback">
                            <?= $errores['nombre'] ?? '' ?>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control <?= isset($errores['email']) ? 'is-invalid' : '' ?>"
                            id="email" name="email" value="<?= htmlspecialchars($email) ?>" required maxlength="100"
                            autocomplete="email">
                        <div class="invalid-feedback">
                            <?= $errores['email'] ?? '' ?>
                        </div>
                    </div>
                    <div>
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password"
                            class="form-control <?= isset($errores['password']) ? 'is-invalid' : '' ?>" id="password"
                            name="password" required autocomplete="new-password">
                        <div class="invalid-feedback">
                            <?= $errores['password'] ?? '' ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-acento btn-lg w-100 mt-2">Crear cuenta</button>
                </form>

                <p class="text-center text-body-secondary mt-4 mb-0">
                    ¿Ya tienes cuenta? <a href="/login.php" class="text-acento">Inicia sesión</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php
require __DIR__ . "/../app/partials/footer.php";