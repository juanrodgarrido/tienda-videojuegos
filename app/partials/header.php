<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.2/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="/css/style.css" />
    <link rel="icon" href="/img/icono.svg" />

    <title><?=$titulo ?? 'Minimal Gaming'?></title>
</head>


<body class="d-flex flex-column min-vh-100">

    <nav class="navbar bg-black navbar-expand-lg">
        <div class="container-fluid px-4">
            <a href="/" class="navbar-brand lado m-0">
                <img src="/img/logo.svg" alt="Minimal Gaming" height="44">
            </a>
            <div class="order-lg-last justify-content-end ms-auto navbar-nav flex-row gap-3 lado">
                <a class="nav-link" href="#" aria-label="Cesta">
                    <i class="bi bi-cart fs-4"></i>
                </a>
                <a class="nav-link" href="#" aria-label="Mi cuenta">
                    <i class="bi bi-person fs-3"></i>
                </a>
            </div>
            <button type="button" class="navbar-toggler ms-3" data-bs-toggle="collapse" data-bs-target="#menu"
                aria-label="Abrir menú">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse flex-lg-grow-0" id="menu">
                <ul class="navbar-nav mx-auto gap-2">
                    <li class="nav-item">
                        <a href="/" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="/tienda.php" class="nav-link">Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a href="/contacto.php" class="nav-link">Contacto</a>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
