<?php

use ClearMarkup\Classes\View;

$router->map('GET', '/', function () {
    $view = new View;

    $view->assign('page', [
        'title' => _('My awesome website'),
    ]);

    $view->render('index');
});

$router->map('GET', '/twig', function () {
    $view = new View;

    $view->assign('page', [
        'title' => _('My awesome website'),
    ]);

    $view->twig('test');
});
