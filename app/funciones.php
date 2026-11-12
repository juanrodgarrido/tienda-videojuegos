<?php

function formatearPrecio($centimos){
    return number_format($centimos/100, 2, ",", ".") . " €";
}