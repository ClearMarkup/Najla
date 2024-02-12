<?php

echo "
\033[32m-------------------------------------------------
Welcome to the installation page of Najla v.9.0.1
-------------------------------------------------\033[0m

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

echo "Type the version of your project (default is 0.9.0):\n";
$project_version = rtrim(fgets(STDIN), PHP_EOL);
if (empty($project_version)) {
    $project_version = "0.1.0";
}

echo "Type the database host (default is 127.0.0.1):\n";
$database_host = rtrim(fgets(STDIN), PHP_EOL);
if (empty($database_host)) {
    $database_host = "127.0.0.1";
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
$pdo = new PDO("mysql:host=$database_host_pdo", $database_username, $database_password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("CREATE DATABASE IF NOT EXISTS $database_name");

// run the SQL file
$pdo->exec("USE $database_name");
$pdo->exec(file_get_contents(__DIR__ . '/database/MySQL.sql'));

// create the config file
file_put_contents(__DIR__ . '/../' . 'najla-config.php', "<?php
\$config = (object) [
    \"sitename\" => '$project_name',
    \"url\" => '$project_url',
    \"version\" => '$project_version',
    \"language\" => \"en\",
    \"debug\" => true,
    \"openssl_key\" => '',
    \"session_name\" => 'najla',
    \"database\" => [
        'type' => 'mysql',
        'host' => '$database_host',
        'database' => '$database_name',
        'username' => '$database_username',
        'password' => '$database_password',
        'charset' => 'utf8mb3',
        'collation' => 'utf8mb3_general_ci',
    ],
    \"password_policy\" => [
        'length' => 8,
        'uppercase' => 1,
        'lowercase' => 1,
        'digit' => 1,
        'special' => 1
    ],
    \"smtp\" => [
        'host' => 'localhost',
        'SMTPAuth' => false,
        'username' => 'mail@localhost',
        'password' => '',
        'SMTPSecure' => false,
        'port' => 2500
    ],
    \"google_client_id\" => '',
    \"google_client_secret\" => '',
    \"google_redirect_uri\" => '',
    \"microsoft_client_id\" => '',
    \"microsoft_client_secret\" => '',
    \"microsoft_redirect_uri\" => '',
    \"sendgrid_api\" => '',
    \"mail_from\" => '',
    \"mail_from_text\" => '',
    \"stripe_pk\" => '',
    \"stripe_sk\" => '',
    \"stripe_signing_secret\" => ''
];");

// create the najla.json file
file_put_contents(__DIR__ . '/../najla.json', json_encode([
    'buildFiles' => [
        "classes/",
        "controller/",
        "lang/",
        "vendor/",
        "views/",
        ".htaccess",
        "index.php",
        "najla-config.php",
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "\033[32m✅ Installation complete!\033[0m\n";

?>