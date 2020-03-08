<?php

use Framework\App;
use Twig\Environment;
use App\blog\BlogModule;
use function Http\Response\send;
use Twig\Loader\FilesystemLoader;
use GuzzleHttp\Psr7\ServerRequest;
use Framework\Renderer\TwigRenderer;

require '../vendor/autoload.php';

$renderer = new TwigRenderer(dirname(__DIR__) . '/views');

$app = new App([
    BlogModule::class
], [
    'renderer' => $renderer
]);

$response = $app->run(ServerRequest::fromGlobals());
send($response);
