<?php

require __DIR__ . "/conexion.php";

$bd = obtenerBD();

$coleccion = $bd->productos;
$coleccion->deleteMany([]);
$productos = [
    [
        "nombre" => "Phantom Blade Zero",
        "descripcion" => "RPG de acción en mundo abierto",
        "precio" => 5999,
        "stock" => 10,
        "imagen" => "https://gaming-cdn.com/images/products/14205/380x218/phantom-blade-zero-pc-steam-cover.jpg?v=1794247200",
        "activo" => true,
    ],
    [
        "nombre" => "Call of Duty: Modern Warfare 4",
        "descripcion" => "FPS Multijugador de la saga Call of Duty",
        "precio" => 5999,
        "stock" => 15,
        "imagen" => "https://gaming-cdn.com/images/products/16809/380x218/call-of-duty-modern-warfare-4-xbox-series-x-s-pc-microsoft-store-cover.jpg?v=1794247200",
        "activo" => true,
    ],
    [
        "nombre" => "EA Sports FC 27",
        "descripcion" => "Juego de fútbol de EA con apertura de sobres",
        "precio" => 5999,
        "stock" => 15,
        "imagen" => "https://gaming-cdn.com/images/products/21656/380x218/ea-sports-fc-27-pc-ea-app-cover.jpg?v=1794247200",
        "activo" => true,
    ],
    [
        "nombre" => "Metro 2039",
        "descripcion" => "FPS post-apocalíptico de la saga Metro",
        "precio" => 4999,
        "stock" => 15,
        "imagen" => "https://gaming-cdn.com/images/products/18889/380x218/metro-2039-pc-steam-cover.jpg?v=1794247200",
        "activo" => true,
    ],
    [
        "nombre" => "Escape The Backrooms",
        "descripcion" => "Videojuego de escapar de unas salas de baño",
        "precio" => 1999,
        "stock" => 0,
        "imagen" => "https://gaming-cdn.com/images/products/16670/380x218/escape-the-backrooms-pc-steam-cover.jpg?v=1761554167",
        "activo" => true,
    ],
    [
        "nombre" => "Final Fantasy Resonance",
        "descripcion" => "Última edición de la saga Final Fantasy",
        "precio" => 5999,
        "stock" => 13,
        "imagen" => "https://gaming-cdn.com/images/products/22936/380x218/final-fantasy-resonance-pc-steam-cover.jpg?v=1794247200",
        "activo" => true,
    ],
    [
        "nombre" => "Wardogs",
        "descripcion" => "FPS que simula una batalla en un campo de guerra",
        "precio" => 3999,
        "stock" => 21,
        "imagen" => "https://gaming-cdn.com/images/products/21740/380x218/wardogs-pc-steam-cover.jpg?v=1794247200",
        "activo" => true,
    ],
    [
        "nombre" => "Los Simpson: Hit and Run",
        "descripcion" => "Juego clásico que no puede fallar",
        "precio" => 2999,
        "stock" => 21,
        "imagen" => null,
        "activo" => true,
    ],
    [
        "nombre" => "Juego ejemplo",
        "descripcion" => "Este juego no debe salir por activo = false",
        "precio" => 2999,
        "stock" => 21,
        "imagen" => null,
        "activo" => false,
    ],
];

$resultado = $coleccion->insertMany($productos);
echo $resultado->getInsertedCount() . " productos insertados\n";