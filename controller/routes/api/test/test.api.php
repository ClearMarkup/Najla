<?php
use Najla\Classes\Api;
use Najla\Classes\ExceptionHandler;

$router->map('GET', '/test', function () {
    $api = new Api;
    $exceptionHandler = new ExceptionHandler;
    
    $api->success('test success');
});