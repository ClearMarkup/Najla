<?php
use ClearMarkup\Classes\View;

// CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Pre-flight request. Exit successfully.
    exit(0);
}

/* Config */
if (!file_exists(__DIR__ . '/config.php')) {
    die('Please run <code>php ClearMarkup init</code> to create the config file.');
} else {
    require_once(__DIR__ . '/config.php');
}

// Set custom session name
session_name($config->session_name);
session_start();

// Show errors if debug is true
if ($config->debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/controller/functions.php');

// Router
$router = new AltoRouter();

applyCallbackToFiles('php' , __DIR__ . '/controller/routes', function ($file) use ($config, $router) {
    require_once($file);
});

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    $view = new View();
    $view->render('404', 404);
};
