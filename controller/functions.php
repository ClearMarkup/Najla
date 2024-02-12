<?php

/**
 * This file contains functions that are used in multiple places in the application.
 * 
 */

$lang_list = json_decode(file_get_contents(__DIR__ . '/../lang/' . $config->language . '/' . $config->language . '.lang.json'), true);

function __($code)
{
    global $lang_list;
    if (isset($lang_list[$code])) {
        return $lang_list[$code];
    } else {
        return $code;
    }
}

function applyCallbackToFiles($fileExt, $dir, $callback) {
    foreach (glob($dir . '/*.'. $fileExt) as $file) {
        $callback($file);
    }

    foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subDir) {
        applyCallbackToFiles($fileExt, $subDir, $callback);
    }
}