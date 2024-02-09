<?php
file_put_contents(__DIR__ . '/../controller/extends/' . $class_name . '.php', "<?php
class $class_name
{
    public function __construct()
    {
        
    }
}");

echo "\033[32mClass $class_name has been created.\n\033[0m";