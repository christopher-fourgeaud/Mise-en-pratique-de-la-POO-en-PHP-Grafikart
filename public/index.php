<?php

use Framework\App;
use App\blog\BlogModule;
use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;

require '../vendor/autoload.php';

$app = new App([
    BlogModule::class
]);

$response = $app->run(ServerRequest::fromGlobals());
send($response);
