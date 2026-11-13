<?php

$kodeOwner = $argv[1] ?? null;

if (!$kodeOwner) {
    echo "Usage: php run_merge.php <kodeOwner>\n";
    exit(1);
}

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\MergeController;

$controller = new MergeController();
$response = $controller->mergeOwnerDatabases($kodeOwner);

if ($response instanceof \Illuminate\Http\JsonResponse) {
    echo $response->getContent();
} elseif (is_array($response) || is_object($response)) {
    echo json_encode($response, JSON_PRETTY_PRINT);
} else {
    echo $response;
}
