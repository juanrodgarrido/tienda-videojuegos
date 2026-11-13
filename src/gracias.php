<?php
require __DIR__ . '/../app/auth.php';
$titulo = "Mensaje enviado";
require __DIR__ . "/../app/partials/header.php";
?>

<main class="flex-grow-1 d-flex align-items-center py-5">
    <div class="container text-center">
        <i class="bi bi-check-circle display-1 text-acento"></i>
        <h1 class="mt-3">¡Mensaje enviado!</h1>
        <p class="lead mb-4">Gracias por escribirnos. Te responderemos lo antes posible.</p>
        <a href="/" class="btn btn-acento btn-lg">Volver al inicio</a>
    </div>
</main>

<?php
require __DIR__ . "/../app/partials/footer.php";