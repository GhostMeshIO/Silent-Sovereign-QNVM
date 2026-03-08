<?php
/**
 * download.php – Serves output files securely.
 * Now only serves files from the exact session directory (no fallback).
 */

define('OUTPUT_DIR', __DIR__ . '/outputs/');

$sid  = preg_replace('/[^a-f0-9]/i', '', $_GET['sid'] ?? '');
$file = isset($_GET['file']) ? basename($_GET['file']) : null;

if (!$sid || !$file) {
    http_response_code(400);
    exit('Missing session ID or file name.');
}

$session_dir = OUTPUT_DIR . $sid . '/';
$path = $session_dir . $file;

if (!file_exists($path) || !is_file($path)) {
    http_response_code(404);
    exit('File not found.');
}

$real = realpath($path);
$base = realpath($session_dir);
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
    'glb'  => 'model/gltf-binary',
    'gltf' => 'model/gltf+json',
    'usdz' => 'model/vnd.usdz+zip',
    'zkp'  => 'application/octet-stream', // placeholder
    'nft'  => 'application/json',
];
$mime = $mime_map[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . addslashes($file) . '"');
header('Content-Length: ' . filesize($real));
header('Cache-Control: no-cache, must-revalidate');
readfile($real);
exit;
