<?php 
spl_autoload_register(function ($className) {
    $classFile = __DIR__ . '/' . $className . '.class.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }

    $classFile = __DIR__ . '/../controller/extends/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});