<?php
$class_name = ucfirst($argv[2]);

if (!is_dir($rootpath . 'controller/extends')) {
    mkdir($rootpath . 'controller/extends', 0777, true);
}

file_put_contents($rootpath . 'controller/extends/Extended' . $class_name . '.php', "<?php
namespace Najla\\Classes\\extends;
use Najla\\Classes\\$class_name;

class Extended$class_name extends $class_name
{
    public function __construct()
    {
        parent::__construct();

        
    }
}");

echo "\033[32mClass Extended$class_name has been created and extends $class_name.\n\033[0m";

if (isset($argv[3]) && $argv[3] == '-r' || isset($argv[3]) && $argv[3] == '-replace') {

    $directory = new RecursiveDirectoryIterator($rootpath . 'controller/routes');
    $iterator = new RecursiveIteratorIterator($directory);

    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $file_content = file_get_contents($file->getPathname());

            $pattern = '/new\s+' . $class_name . '(\(|\;)/';
            $replacement = 'new Extended' . $class_name . '$1';

            $file_content = preg_replace($pattern, $replacement, $file_content);

            // Add this block
            $patternUse = '/use Najla\\\\Classes\\\\' . $class_name . ';/';
            $replacementUse = 'use Najla\\\\Classes\\\\Extends\\\\' . 'Extended' . $class_name . ';';

            $file_content = preg_replace($patternUse, $replacementUse, $file_content);

            file_put_contents($file->getPathname(), $file_content);
        }
    }

    echo "\033[32mAll instances of $class_name and 'use Najla\Classes\\$class_name;' have been replaced with Extended$class_name and 'use Najla\Validation\Extends\$class_name;'.\n\033[0m";
}
