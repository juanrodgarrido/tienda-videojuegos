<?php

require __DIR__ . '/../app/conexion.php';


$db = obtenerBD();
$colecciones = $db->listCollections();

foreach($colecciones as $coleccion){

    echo $coleccion->getName() . "<br>";

}
