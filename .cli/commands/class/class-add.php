<?php
$class_name = ucfirst($argv[2]);

if (!is_dir($rootpath . 'controller/extends')) {
    mkdir($rootpath . 'controller/extends', 0777, true);
}

file_put_contents($rootpath . 'controller/extends/' . $class_name . '.php', "<?php
namespace ClearMarkup\\Classes\\extends;

class $class_name
{
    public function __construct()
    {
        
    }
}");

echo "\033[32mClass $class_name has been created.\n\033[0m";