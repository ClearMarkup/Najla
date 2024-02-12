<?php
$class_name = ucfirst($argv[2]);
file_put_contents($rootpath . 'controller/extends/' . $class_name . '.php', "<?php
namespace Najla\\Classes\\extends;

class $class_name
{
    public function __construct()
    {
        
    }
}");

echo "\033[32mClass $class_name has been created.\n\033[0m";