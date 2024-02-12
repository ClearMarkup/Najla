<?php 

// Get config file
$config_json = file_get_contents(__DIR__ . '/../najla.json');

// Decode JSON
$config = json_decode($config_json, true);

// Remove $argv[3] from $config['build']
$index = array_search($argv[2], $config['buildFiles']);
if ($index !== false) {
    unset($config['buildFiles'][$index]);
    $config['buildFiles'] = array_values($config['buildFiles']); // Reindex array
}

// Encode JSON
$config_json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Save to file
file_put_contents(__DIR__ . '/../najla.json', $config_json);

echo "\033[32mRemoved " . $argv[2] . " from buildFiles\033[0m\n";
