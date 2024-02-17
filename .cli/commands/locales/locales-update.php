<?php

// loop through all .php files in the controller and views directories
$directories = [$rootpath . 'controller', $rootpath . 'views'];
$files = [];

foreach ($directories as $directory) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') continue;
        $files[] = escapeshellarg($file->getPathname());
    }
}

// loop through all .po files in locales/ and update them
$locales = glob($rootpath . 'locales/*', GLOB_ONLYDIR);
foreach ($locales as $locale) {
    $tempFile = $locale . '/LC_MESSAGES/messages_temp.po';
    $outputFile = $locale . '/LC_MESSAGES/messages.po';
    $command = 'xgettext -o ' . escapeshellarg($tempFile) . ' ' . implode(' ', $files);
    shell_exec($command);

    // Replace "CHARSET" with "UTF-8" in the .po file
    $contents = file_get_contents($tempFile);
    $contents = str_replace('"Content-Type: text/plain; charset=CHARSET\\n"', '"Content-Type: text/plain; charset=UTF-8\\n"', $contents);
    $contents = str_replace('"Language: \\n"', '"Language: ' . basename($locale) . '\\n"', $contents);
    file_put_contents($tempFile, $contents);

    if (file_exists($outputFile)) {
        $mergeCommand = 'msgmerge --update --backup=none ' . escapeshellarg($outputFile) . ' ' . escapeshellarg($tempFile);
        shell_exec($mergeCommand);
        unlink($tempFile);
    } else {
        rename($tempFile, $outputFile);
    }
}

echo "Locales updated.\n";