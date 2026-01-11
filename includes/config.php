<?php
if ($_SERVER['HTTP_HOST'] === 'localhost') {
    require_once __DIR__ . '/config.local.php';
} else {
    require_once __DIR__ . '/config.prod.php';
}
