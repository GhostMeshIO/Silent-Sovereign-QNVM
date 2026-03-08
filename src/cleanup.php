#!/usr/bin/env php
<?php
/**
 * cleanup.php – Remove old session files.
 * Run via cron: 0 3 * * * /usr/bin/php /path/to/cleanup.php
 */

define('OUTPUT_DIR', __DIR__ . '/outputs/');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_AGE', 86400); // 24 hours

foreach ([OUTPUT_DIR, UPLOAD_DIR] as $dir) {
    if (!is_dir($dir)) continue;
    $files = glob($dir . '*');
    foreach ($files as $file) {
        if (is_file($file) && (time() - filemtime($file)) > MAX_AGE) {
            unlink($file);
            echo "Removed $file\n";
        } elseif (is_dir($file)) {
            // Recursively delete old session directories
            $session_age = time() - filemtime($file);
            if ($session_age > MAX_AGE) {
                delete_directory($file);
                echo "Removed directory $file\n";
            }
        }
    }
}

function delete_directory($dir) {
    if (!is_dir($dir)) return;
    $items = glob($dir . '/*');
    foreach ($items as $item) {
        is_dir($item) ? delete_directory($item) : unlink($item);
    }
    rmdir($dir);
}
