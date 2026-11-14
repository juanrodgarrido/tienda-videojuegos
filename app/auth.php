<?php 
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

function usuarioActual(){
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin($destino){
    if(usuarioActual() === null){
        $_SESSION['destino'] = $destino;
        $_SESSION['aviso'] = ['tipo' => 'warning', 'texto' => 'Tienes que iniciar sesión para continuar'];
        header('Location: /login.php');        
        exit;
    }
}