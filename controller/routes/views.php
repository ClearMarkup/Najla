<?php

$router->map('GET', '/', function () {
    $view = new View;

    $view->render('index');
});