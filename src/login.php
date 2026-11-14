<?php
require __DIR__ . '/../app/auth.php';
require __DIR__ . '/../app/conexion.php';
$titulo = "Iniciar sesión";
$errores = [];
$email = "";


if (usuarioActual() !== null) {
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '') {
        $errores['email'] = "El email es obligatorio";

    }

    if ($password === '') {
        $errores['password'] = "La contraseña es obligatoria";

    }

    if (empty($errores)) {
        $usuario = obtenerBD()->usuarios->findOne(['email' => $email]);

        if ($usuario === null || !password_verify($password, $usuario['password'])) {
            $errores['general'] = "Email o contraseña incorrectos";
        } else {
            session_regenerate_id(true);
            $_SESSION['usuario'] = [
                'id' => (string) $usuario['_id'],
                'nombre' => $usuario['nombre'],
                'rol' => $usuario['rol'],
            ];
            $destino = $_SESSION['destino'] ?? '/';
            unset($_SESSION['destino']);
            header('Location: ' . $destino);
            exit;
        }



    }

}




require __DIR__ . "/../app/partials/header.php";
?>
<main class="flex-grow-1 d-flex align-items-center py-5">
    <div class="container contenedor-estrecho">
        <h1 class="text-center mb-4">Iniciar <span class="text-acento">sesión</span></h1>

        <div class="card bg-black border-top border-3 borde-acento">
            <div class="card-body p-4">

                <?php if (isset($_GET['registrado'])): ?>
                    <div class="alert alert-success">Cuenta creada. Ya puedes iniciar sesión.</div>
                <?php endif; ?>

                <?php if (isset($errores['general'])): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $errores['general'] ?>
                    </div>
                <?php endif; ?>

                <form method="POST" novalidate>
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
                            name="password" required autocomplete="current-password">
                        <div class="invalid-feedback">
                            <?= $errores['password'] ?? '' ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-acento btn-lg w-100 mt-2">Entrar</button>
                </form>

                <p class="text-center text-body-secondary mt-4 mb-0">
                    ¿No tienes cuenta? <a href="/registro.php" class="text-acento">Regístrate</a>
                </p>
            </div>
        </div>
    </div>
</main>

<?php
require __DIR__ . "/../app/partials/footer.php";