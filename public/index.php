<?php declare(strict_types=1);

require_once __DIR__ . '/../src/Kernel.php';

$extensions = [
    'jpg',
    'jpeg',
    'png',
    'gif',
    'css',
    'js',
];

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);
    
if (in_array($ext, $extensions, true)) {
    // let the server handle the request as-is
    return false;
}

$kernel = new \Wolnosciowiec\UptimeAdminBoard\Kernel();
$kernel->emitResponse(
    \Symfony\Component\HttpFoundation\Request::createFromGlobals()
);
