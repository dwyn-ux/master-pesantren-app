<?php

/**
 * Laravel - PHP built-in dev server router (Windows-friendly).
 * Run from project root:
 *   php -S 127.0.0.1:8000 server.php
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$publicPath = __DIR__ . '/public';
$requested  = $publicPath . $uri;

// Serve static files langsung dari public/ kalau ada (CSS, JS, gambar, dll).
if ($uri !== '/' && is_file($requested)) {
    $ext = strtolower(pathinfo($requested, PATHINFO_EXTENSION));
    $mime = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'mjs'   => 'application/javascript',
        'json'  => 'application/json',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'ico'   => 'image/x-icon',
        'webp'  => 'image/webp',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
        'eot'   => 'application/vnd.ms-fontobject',
        'map'   => 'application/json',
        'txt'   => 'text/plain',
        'pdf'   => 'application/pdf',
    ][$ext] ?? null;

    if ($mime) {
        header('Content-Type: ' . $mime);
    }
    header('Content-Length: ' . filesize($requested));
    readfile($requested);
    return true;
}

// Selain itu, serahkan ke Laravel front controller.
require_once $publicPath . '/index.php';
