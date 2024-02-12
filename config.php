<?php
$config = (object) [
    "sitename" => 'najla',
    "url" => 'http://localhost',
    "version" => '0.1.0',
    "language" => "en",
    "debug" => true,
    "openssl_key" => '',
    "session_name" => 'najla',
    "database" => [
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'najla',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb3',
        'collation' => 'utf8mb3_general_ci',
    ],
    "password_policy" => [
        'length' => 8,
        'uppercase' => 1,
        'lowercase' => 1,
        'digit' => 1,
        'special' => 1
    ],
    "smtp" => [
        'host' => 'localhost',
        'SMTPAuth' => false,
        'username' => 'mail@localhost',
        'password' => '',
        'SMTPSecure' => false,
        'port' => 2500
    ],
    "google_client_id" => '',
    "google_client_secret" => '',
    "google_redirect_uri" => '',
    "microsoft_client_id" => '',
    "microsoft_client_secret" => '',
    "microsoft_redirect_uri" => '',
    "sendgrid_api" => '',
    "mail_from" => '',
    "mail_from_text" => '',
    "stripe_pk" => '',
    "stripe_sk" => '',
    "stripe_signing_secret" => ''
];