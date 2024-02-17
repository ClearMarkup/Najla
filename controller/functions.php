<?php

/**
 * This file contains functions that are used in multiple places in the application.
 * 
 */

function applyCallbackToFiles($fileExt, $dir, $callback) {
    foreach (glob($dir . '/*.'. $fileExt) as $file) {
        $callback($file);
    }

    foreach (glob($dir . '/*', GLOB_ONLYDIR) as $subDir) {
        applyCallbackToFiles($fileExt, $subDir, $callback);
    }
}