<?php
// CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Pre-flight request. Exit successfully.
    exit(0);
}

/* CONFIG */
require_once(__DIR__ . '/najla2.config.php');
/* END_CONFIG */

// Set custom session name
session_name($config->session_name);

// Show errors if debug is true
if ($config->debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/classes/autoloader.php');

// Database
use Medoo\Medoo;

$database = new Medoo($config->database);

// auth
use Delight\Auth\Auth;

$auth = new Auth($database->pdo, null, null, $config->debug ? false : true);

// Validation
use Rakit\Validation\Validator;
use Rakit\Validation\Rule;

$validator = new Validator;
require_once(__DIR__ . '/controller/rules.php');

require_once(__DIR__ . '/controller/functions.php');

// Router
$router = new AltoRouter();

foreach (glob(__DIR__ . '/controller/routes/*.php') as $filename) {
    include $filename;
}

$match = $router->match();

if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    $view = new View();
    $view->render('404', 404);
};
