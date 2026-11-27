<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/api/v1/loans', 'POST', [], [], [], [
    'HTTP_ACCEPT' => 'application/json',
    'CONTENT_TYPE' => 'application/json',
    'HTTP_AUTHORIZATION' => 'Bearer ' . $argv[1],
], json_encode(['family_id' => $argv[2], 'toy_ids' => [$argv[3]]]));
$response = $kernel->handle($request);
echo $response->getStatusCode();
$kernel->terminate($request, $response);
