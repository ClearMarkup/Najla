<?php
use ClearMarkup\Classes\View;
use ClearMarkup\Classes\Db;

$router->map('GET', '/', function () {
    $view = new View;

    $view->assign('page', [
        'title' => _('My awesome website'),
    ]);

    $view->render('index');
});