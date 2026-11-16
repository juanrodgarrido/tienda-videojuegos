<?php

function formatearPrecio($centimos){
    return number_format($centimos/100, 2, ",", ".") . " €";
}

function formatearFecha($fecha){
    return $fecha->toDateTime()->setTimezone(new DateTimeZone('Europe/Madrid'))->format('d/m/Y H:i');
}