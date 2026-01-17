<?php

declare(strict_types=1);

// ini_set('display_errors', 'true');
// ini_set('track_errors', 'true');
// ini_set('display_startup_errors', 'true');
// error_reporting(E_ALL);

// require_once '../../../cesp/cesp_log.php';
// cesp_log('start');

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__) . '/vendor/autoload_runtime.php';

// Move logging statements here, before the return
$kernel = require dirname(__DIR__) . '/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};

$request = Request::createFromGlobals();
$response = $kernel($request->server->all(), [], []); // Invoke the callable

$response->send(false);
$kernel->terminate($request, $response);
