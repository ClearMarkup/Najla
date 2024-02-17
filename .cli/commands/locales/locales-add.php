<?php

if (!file_exists($rootpath . 'locales')) {
    mkdir($rootpath . 'locales');
}

if (!file_exists($rootpath . 'locales/' . $argv[2])) {
    mkdir($rootpath . 'locales/' . $argv[2]);
}

if (!file_exists($rootpath . 'locales/' . $argv[2] . '/LC_MESSAGES')) {
    mkdir($rootpath . 'locales/' . $argv[2] . '/LC_MESSAGES');
}

$directories = [$rootpath . 'controller', $rootpath . 'views'];
$files = [];

foreach ($directories as $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') continue;
        $files[] = escapeshellarg($file->getPathname());
    }
}

if ($files) {
    $outputFile = $rootpath . 'locales/' . $argv[2] . '/LC_MESSAGES/messages.po';
    $command = 'xgettext -o ' . escapeshellarg($outputFile) . ' ' . implode(' ', $files);
    shell_exec($command);
}

// Replace "CHARSET" with "UTF-8" in the .po file
$contents = file_get_contents($outputFile);
$contents = str_replace('CHARSET', 'UTF-8', $contents);
$contents = str_replace('"Language: \\n"', '"Language: ' . $argv[2] . '\\n"', $contents);
file_put_contents($outputFile, $contents);

echo "Locale " . $argv[2] . " added.\n";