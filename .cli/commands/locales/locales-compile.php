<?php

// Create .mo files from all .po files in locales/
$locales = glob($rootpath . 'locales/*', GLOB_ONLYDIR);
foreach ($locales as $locale) {
    $poFiles = glob($locale . '/LC_MESSAGES/*.po');
    foreach ($poFiles as $poFile) {
        $moFile = str_replace('.po', '.mo', $poFile);
        $command = 'msgfmt -o ' . escapeshellarg($moFile) . ' ' . escapeshellarg($poFile);
        shell_exec($command);
    }
}

echo "Locales compiled.\n";