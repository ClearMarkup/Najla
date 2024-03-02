<?php
/**
 * This is the main entry point for the application.
 * It's responsible for including the Composer autoloader and
 * instantiating the main application class.
 */
require_once(__DIR__ . '/../vendor/autoload.php');
new ClearMarkup\Classes\App;