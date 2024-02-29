<?php

function arrayToString($array) {
    $result = "[\n";
    foreach ($array as $key => $value) {
        if (strpos($value, '__DIR__') === 0) {
            $result .= "        '" . $key . "' => " . $value . ",\n";
        } else {
            $result .= "        '" . $key . "' => '" . $value . "',\n";
        }
    }
    $result .= "    ]";
    return $result;
}

echo "
\033[32m----------------------------------
Welcome to ClearMarkup Starter Kit 
----------------------------------\033[0m

Name your project:\n";
$project_name = rtrim(fgets(STDIN), PHP_EOL);
while (empty($project_name)) {
    echo "Please enter a project name:\n";
    $project_name = rtrim(fgets(STDIN), PHP_EOL);
}

echo "Type the URL of your project: (default is http://localhost)\n";
$project_url = rtrim(fgets(STDIN), PHP_EOL);
if (empty($project_url)) {
    $project_url = "http://localhost";
}

echo "Type the database type (default is mysql):\n";
$database_type = rtrim(fgets(STDIN), PHP_EOL);
if (empty($database_type)) {
    $database_type = "mysql";
}

$database = [];

switch ($database_type) {
    case 'mysql':
    case 'pgsql':
        echo "Type the database host (default is 127.0.0.1):\n";
        $database_host = rtrim(fgets(STDIN), PHP_EOL);
        if (empty($database_host)) {
            $database_host = "127.0.0.1";
        }

        echo "Type the database port (default is 3306):\n";
        $database_port = rtrim(fgets(STDIN), PHP_EOL);
        if (empty($database_port)) {
            $database_port = "3306";
        }

        echo "Type the database name: (default is " . strtolower(str_replace(' ', '_', $project_name)) . ")\n";
        $database_name = rtrim(fgets(STDIN), PHP_EOL);
        if (empty($database_name)) {
            $database_name = strtolower(str_replace(' ', '_', $project_name));
        }

        echo "Type the database username: (default is root)\n";
        $database_username = rtrim(fgets(STDIN), PHP_EOL);
        if (empty($database_username)) {
            $database_username = "root";
        }

        echo "Type the database password:\n";
        $database_password = rtrim(fgets(STDIN), PHP_EOL);

        // create the database
        $database_host_pdo = $database_host === 'localhost' ? '127.0.0.1' : $database_host;
        $pdo = new PDO("$database_type:host=$database_host_pdo;port=$database_port", $database_username, $database_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $database_name");

        // run the SQL file
        if ($database_type === 'mysql') {
            $pdo->exec("USE $database_name");
            $pdo->exec(file_get_contents(__DIR__ . '/../database/MySQL.sql'));
        } else {
            $pdo->exec("ALTER DATABASE $database_name SET search_path TO public");
            $pdo->exec(file_get_contents(__DIR__ . '/../database/PostgreSQL.sql'));
        }

        $database = [
            'type' => $database_type,
            'host' => $database_host,
            'port' => $database_port,
            'database' => $database_name,
            'username' => $database_username,
            'password' => $database_password,
            'charset' => 'utf8mb3',
            'collation' => 'utf8mb3_general_ci',
        ];
        break;

    case 'sqlite':
        echo "Type the database name: (default is database.sqlite)\n";
        $database_name = rtrim(fgets(STDIN), PHP_EOL);
        if (empty($database_name)) {
            $database_name = "database.sqlite";
        }

        $pdo = new PDO("sqlite:$database_name");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec(file_get_contents(__DIR__ . '/../database/SQLite.sql'));

        $database = [
            'type' => 'sqlite',
            'database' => '__DIR__ . \'/\' . \'' . $database_name . '\'',
        ];
        break;
}

// create the config file
file_put_contents($rootpath . 'config.php', "<?php
/**
 * ClearMarkup Configuration
 * 
 * This file contains the configuration for the ClearMarkup application.
 * 
 * @package ClearMarkup
 * 
 */

\$config = (object) [
    'sitename' => '$project_name',
    'url' => '$project_url',
    'version' => '0.1.0',
    'locale' => 'en_US',
    'debug' => true,
    'openssl_key' => '',
    'session_name' => 'ClearMarkup',
    'database' => " . arrayToString($database) . ",
    'password_policy' => [
        'length' => 8,
        'uppercase' => 1,
        'lowercase' => 1,
        'digit' => 1,
        'special' => 1
    ],
    'remember_duration' => (int) (60 * 60 * 24 * 365.25 / 12),
    'smtp' => [
        'host' => 'localhost',
        'SMTPAuth' => false,
        'username' => 'mail@localhost',
        'password' => '',
        'SMTPSecure' => false,
        'port' => 2500
    ],
    'mail_from' => '',
    'mail_from_text' => ''
];");

// create the ClearMarkup.json file

// if db type is sqlite, add the database file to the buildFiles
$buildFiles = [
    "classes/",
    "controller/",
    "locales/",
    "public/",
    "vendor/",
    "views/",
    "index.php",
    "config.php",
];

if ($database_type === 'sqlite') {
    $buildFiles[] = $database_name;
}


file_put_contents($rootpath . 'ClearMarkup.json', json_encode([
    "buildFiles" => $buildFiles
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "\033[32m✅ Installation complete!\033[0m\n";
