<?php
$titulo = "Contacto";
$nombre = "";
$email = "";
$asunto = "";
$mensaje = "";
$errores = [];
$enviado = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($_SERVER['REQUEST_METHOD'] === "POST") {



    $nombre = trim($_POST["nombre"] ?? "");

    if ($nombre === "") {
        $errores["nombre"] = "El nombre es obligatorio";
    } elseif (mb_strlen($nombre) > 100) {
        $errores["nombre"] = "El nombre es demasiado largo";
    }

    $email = trim($_POST["email"] ?? "");

    if ($email === "") {
        $errores["email"] = "El email es obligatorio";
    } elseif (mb_strlen($email) > 100) {
        $errores["email"] = "El email es demasiado largo";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores["email"] = "El formato del email es incorrecto";
    }

    $asunto = trim($_POST["asunto"] ?? "");

    if ($asunto === "") {
        $errores["asunto"] = "El asunto es obligatorio";
    } elseif (mb_strlen($asunto) > 100) {
        $errores["asunto"] = "El asunto es demasiado largo";
    }

    $mensaje = trim($_POST["mensaje"] ?? "");

    if ($mensaje === "") {
        $errores["mensaje"] = "El mensaje es obligatorio";
    } elseif (mb_strlen($mensaje) > 300) {
        $errores["mensaje"] = "El mensaje es demasiado largo";
    }

    if (empty($errores)) {
        header('Location: /gracias.php');
        exit;
    }

}


require __DIR__ . "/../app/partials/header.php";
?>
<main class="flex-grow-1 d-flex align-items-center py-5">
    <div class="container">
        <div class="row gy-5 gx-lg-5 align-items-center">
            <div class="col-lg-5">
                <h1>¡Contacta <span class="text-acento">con nosotros</span>!</h1>
                <p class="text-body-secondary mb-4">
                    ¿Dudas con un pedido o buscas un juego? Escríbenos y te respondemos en 24 horas.
                </p>

                <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                    <li class="d-flex align-items-center gap-3">
                        <i class="bi bi-envelope text-acento fs-4" aria-hidden="true"></i>
                        <span>soporte@minimalgaming.es</span>
                    </li>
                    <li class="d-flex align-items-center gap-3">
                        <i class="bi bi-clock text-acento fs-4" aria-hidden="true"></i>
                        <span>Lunes a viernes, de 9:00 a 18:00</span>
                    </li>
                    <li class="d-flex align-items-center gap-3">
                        <i class="bi bi-discord text-acento fs-4" aria-hidden="true"></i>
                        <span>Únete a nuestro servidor de Discord</span>
                    </li>
                    <li class="d-flex align-items-center gap-3">
                        <i class="bi bi-instagram text-acento fs-4" aria-hidden="true"></i>
                        <span>@minimalgaming</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-7">
                <div class="card bg-black border-top border-3 borde-acento">
                    <div class="card-body p-4">
                        <form method="POST" novalidate id="form-contacto">
                            <div>
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text"
                                    class="form-control <?= isset($errores['nombre']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                                    id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" required
                                    maxlength="100">
                                <div class="invalid-feedback">
                                    <?= $errores['nombre'] ?? '' ?>
                                </div>
                            </div>
                            <div>
                                <label for="email" class="form-label">Email</label>
                                <input type="email"
                                    class="form-control <?= isset($errores['email']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                                    id="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                                    maxlength="100">
                                <div class="invalid-feedback">
                                    <?= $errores['email'] ?? '' ?>
                                </div>
                            </div>
                            <div>
                                <label for="asunto" class="form-label">Asunto</label>
                                <input type="text"
                                    class="form-control <?= isset($errores['asunto']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                                    id="asunto" name="asunto" value="<?= htmlspecialchars($asunto) ?>" required
                                    maxlength="100">
                                <div class="invalid-feedback">
                                    <?= $errores['asunto'] ?? '' ?>
                                </div>
                            </div>
                            <div>
                                <label for="mensaje" class="form-label">Mensaje</label>
                                <textarea
                                    class="form-control <?= isset($errores['mensaje']) ? 'is-invalid' : ($enviado ? 'is-valid' : '') ?>"
                                    rows="5" id="mensaje" name="mensaje" required
                                    maxlength="300"><?= htmlspecialchars($mensaje) ?></textarea>
                                <div class="invalid-feedback">
                                    <?= $errores['mensaje'] ?? '' ?>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-acento btn-lg mt-2">Enviar</button>

                        </form>
                    </div>
                </div>

            </div>

        </div>


    </div>

</main>


<?php
require __DIR__ . "/../app/partials/footer.php";

