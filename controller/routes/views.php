<?php
use ClearMarkup\Classes\View;

$router->map('GET', '/', function () {
    $view = new View;

    $view->render('index');
});