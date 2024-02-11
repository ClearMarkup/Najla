<?php 

// Get config file
$config_json = file_get_contents(__DIR__ . '/../najla.json');

// Decode JSON
$config = json_decode($config_json, true);

// Add $argv[3] to last element of $config['build']
$config['buildFiles'][] = $argv[2];

// Encode JSON
$config_json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Save to file
file_put_contents(__DIR__ . '/../najla.json', $config_json);

echo "\033[32mAdded " . $argv[2] . " to buildFiles\033[0m\n";
