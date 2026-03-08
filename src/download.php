<?php
/**
 * download.php – Serves output files to the browser.
 * Enhanced security checks.
 */

define('OUTPUT_DIR', __DIR__ . '/outputs/');

$sid  = preg_replace('/[^a-f0-9]/i', '', $_GET['sid'] ?? '');
$file = isset($_GET['file']) ? basename($_GET['file']) : null;

if (!$sid) {
    http_response_code(400);
    exit('Missing session ID.');
}

$base = realpath(OUTPUT_DIR);
if (!$base) {
    http_response_code(500);
    exit('Output directory not found.');
}

if ($file) {
    // Serve a single file
    $path = OUTPUT_DIR . $sid . '/' . $file;

    if (!file_exists($path) || !is_file($path)) {
        // Try in the outputs root
        $path = OUTPUT_DIR . $file;
    }

    if (!file_exists($path) || !is_file($path)) {
        http_response_code(404);
        exit('File not found: ' . htmlspecialchars($file));
    }

    $real = realpath($path);
    if (!$real || strpos($real, $base) !== 0) {
        http_response_code(403);
        exit('Access denied.');
    }

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $mime_map = [
        'csv'  => 'text/csv',
        'json' => 'application/json',
        'txt'  => 'text/plain',
        'log'  => 'text/plain',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'zip'  => 'application/zip',
    ];
    $mime = $mime_map[$ext] ?? 'application/octet-stream';

    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . addslashes(basename($file)) . '"');
    header('Content-Length: ' . filesize($real));
    header('Cache-Control: no-cache, must-revalidate');
    readfile($real);
    exit;
}

// Serve the ZIP archive
$zip_path = OUTPUT_DIR . $sid . '.zip';
if (!file_exists($zip_path)) {
    http_response_code(404);
    exit('ZIP archive not found for session: ' . htmlspecialchars($sid));
}

$real = realpath($zip_path);
if (!$real || strpos($real, $base) !== 0) {
    http_response_code(403);
    exit('Access denied.');
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="simulation_results_' . $sid . '.zip"');
header('Content-Length: ' . filesize($real));
header('Cache-Control: no-cache, must-revalidate');
readfile($real);
exit;
