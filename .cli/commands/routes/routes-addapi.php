<?php
$dir = $rootpath . 'controller/routes/api/';

$method = strtoupper($argv[2]);
$route_name = str_replace(' ', '_', $argv[3]);

$url = $route_name;
if (substr($url, 0, 1) !== '/') {
    $url = '/' . $url;
}

$file_name = $route_name;
if (strpos($file_name, '/') !== false) {
    $file_name = substr($file_name, strrpos($file_name, '/') + 1);
}

if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

file_put_contents($dir . $file_name . '.api.php', "<?php
use Najla\\Classes\\Api;
use Najla\\Classes\\ExceptionHandler;

\$router->map('$method', '$url', function () {
    \$api = new Api;
    \$exceptionHandler = new ExceptionHandler;
    

});");

echo "\033[32mRoute $route_name has been created.\n\033[0m";