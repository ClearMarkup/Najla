<?php

if (!file_exists(__DIR__ . '/../build')) {
    mkdir(__DIR__ . '/../build');
} else {
    shell_exec('rm -rf ' . __DIR__ . '/../build');
    mkdir(__DIR__ . '/../build');
}

$config = json_decode(file_get_contents(__DIR__ . '/../najla.json'), true);

$build_files = $config['buildFiles'];

foreach ($build_files as $file) {
    if (substr($file, -2) === '/!') {
        shell_exec('rsync -a -f"+ */" -f"- *" ' . __DIR__ . '/../' . substr($file, 0, -2) . ' ' . __DIR__ . '/../build/');
        continue;
    } else if (is_dir(__DIR__ . '/../' . $file)) {
        shell_exec('rsync -a ' . __DIR__ . '/../' . $file . ' ' . __DIR__ . '/../build/' . $file);
    } else {
        copy(__DIR__ . '/../' . $file, __DIR__ . '/../build/' . $file);
    }
}

$index_file = file_get_contents(__DIR__ . '/../build/index.php');
$index_file = preg_replace_callback('/(\/\* CONFIG \*\/)(.*?)(\/\* END_CONFIG \*\/)/s', function ($matches) {
    return $matches[1] . preg_replace('/require_once\(__DIR__ \. \'\/(.*?)\'\);/', 'require_once(__DIR__ . \'/../$1\');', $matches[2]) . $matches[3];
}, $index_file);
file_put_contents(__DIR__ . '/../build/index.php', $index_file);

echo "\033[32m✅ Build complete!\033[0m You can find the build files in the build/ directory\n";

// Gul text
echo "\033[33mYou need to move config.php to the parent directory of the public directory\033[0m\n";
