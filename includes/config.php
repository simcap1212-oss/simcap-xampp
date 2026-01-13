<?php
/* Desde aquí voy a editar temporalmente para ver si se soluciona el error
Más abajo tengo editado para ver si puedo conectar. Este código es para InfinityFree

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    require_once __DIR__ . '/config.local.php';
} else {
    require_once __DIR__ . '/config.prod.php';
}
*/

if ($_SERVER['HTTP_HOST'] === 'localhost') {
    return require __DIR__ . '/config.local.php';
}

return require __DIR__ . '/config.prod.php';
