<?php

require __DIR__ . "/conexion.php";

$bd = obtenerBD();

if (!getenv("ADMIN_EMAIL") || !getenv("ADMIN_PASSWORD")) {
    echo "Faltan datos del env para crear el admin\n";
    exit;
}

$coleccionProductos = $bd->productos;
$coleccionProductos->deleteMany([]);
$productos = [
    [
        "nombre" => "Phantom Blade Zero",
        "descripcion" => "S-GAME presenta Phantom Blade Zero, un juego hecho en Unreal Engine 5 que combina narrativa «wuxia» y acción frenética",
        "precio" => 5999,
        "stock" => 10,
        "imagen" => "https://todojuegos.com/img/caratulas/PhantomBladeZeroSocial.jpg",
        "activo" => true,
        "destacado" => true,
    ],
    [
        "nombre" => "Call of Duty: Modern Warfare 4",
        "descripcion" => "FPS Multijugador de la saga Call of Duty",
        "precio" => 5999,
        "stock" => 15,
        "imagen" => "https://gaming-cdn.com/images/products/16809/616x353/call-of-duty-modern-warfare-4-xbox-series-x-s-pc-microsoft-store-cover.jpg?v=1794247200",
        "activo" => true,
        "destacado" => false,
    ],
    [
        "nombre" => "EA Sports FC 27",
        "descripcion" => "Juego de fútbol de EA con apertura de sobres",
        "precio" => 5999,
        "stock" => 15,
        "imagen" => "https://gaming-cdn.com/images/products/21656/616x353/ea-sports-fc-27-pc-ea-app-cover.jpg?v=1794247200",
        "activo" => true,
        "destacado" => false,
    ],
    [
        "nombre" => "Metro 2039",
        "descripcion" => "FPS post-apocalíptico de la saga Metro",
        "precio" => 4999,
        "stock" => 15,
        "imagen" => "https://gaming-cdn.com/images/products/18889/616x353/metro-2039-pc-steam-cover.jpg?v=1794247200",
        "activo" => true,
        "destacado" => false,
    ],
    [
        "nombre" => "Escape The Backrooms",
        "descripcion" => "Videojuego de escapar de unas salas de baño",
        "precio" => 1999,
        "stock" => 0,
        "imagen" => "https://gaming-cdn.com/images/products/16670/616x353/escape-the-backrooms-pc-steam-cover.jpg?v=1761554167",
        "activo" => true,
        "destacado" => false,
    ],
    [
        "nombre" => "Final Fantasy Resonance",
        "descripcion" => "Última edición de la saga Final Fantasy",
        "precio" => 5999,
        "stock" => 13,
        "imagen" => "https://gaming-cdn.com/images/products/22936/616x353/final-fantasy-resonance-pc-steam-cover.jpg?v=1794247200",
        "activo" => true,
        "destacado" => false,
    ],
    [
        "nombre" => "Wardogs",
        "descripcion" => "FPS que simula una batalla en un campo de guerra",
        "precio" => 3999,
        "stock" => 21,
        "imagen" => "https://gaming-cdn.com/images/products/21740/616x353/wardogs-pc-steam-cover.jpg?v=1794247200",
        "activo" => true,
        "destacado" => false,
    ],
    [
        "nombre" => "Juego ejemplo",
        "descripcion" => "Este juego no debe salir por activo = false",
        "precio" => 2999,
        "stock" => 21,
        "imagen" => null,
        "activo" => false,
        "destacado" => false,
    ],
];

$resultado = $coleccionProductos->insertMany($productos);
echo $resultado->getInsertedCount() . " productos insertados\n";


$adminEmail = trim(mb_strtolower(getenv("ADMIN_EMAIL")));
$adminPass = password_hash(getenv("ADMIN_PASSWORD"), PASSWORD_DEFAULT);

$coleccionUsuarios = $bd->usuarios;
$coleccionUsuarios->createIndex(['email' => 1], ['unique' => true]);
try {
    $coleccionUsuarios->insertOne([
        'nombre' => "Administrador",
        'email' => $adminEmail,
        'password' => $adminPass,
        'rol' => "admin"
    ]);
    echo "Admin creado\n";
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    $codigoerror = $e->getCode();
    if ($codigoerror === 11000) {
        echo "El admin ya existe\n";
    } else {
        throw $e;
    }
}


