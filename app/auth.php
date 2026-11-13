<?php 
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

function usuarioActual(){
    return $_SESSION['usuario'] ?? null;
}