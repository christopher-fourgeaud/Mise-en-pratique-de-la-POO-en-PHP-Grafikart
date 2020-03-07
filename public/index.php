<?php

use Framework\App;
use App\blog\BlogModule;
use Framework\Renderer;

use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;

require '../vendor/autoload.php';

$renderer = new Renderer();
$renderer->addPath(dirname(__DIR__) . '/views');

$app = new App([
    BlogModule::class
], [
    'renderer' => $renderer
]);

$response = $app->run(ServerRequest::fromGlobals());
send($response);
