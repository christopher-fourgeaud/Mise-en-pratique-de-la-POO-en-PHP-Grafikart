<?php

use Framework\App;
use App\blog\BlogModule;
use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;

require '../vendor/autoload.php';

$modules = [
    BlogModule::class
];

// Conteneur de dépendance
$builder = new DI\ContainerBuilder;
$builder->addDefinitions(dirname(__DIR__) . '/config/config.php');

foreach ($modules as $module) {
    if ($module::DEFINITIONS) {
        $builder->addDefinitions($module::DEFINITIONS);
    }
}
$builder->addDefinitions(dirname(__DIR__) . '/config.php');


$container = $builder->build();

$app = new App($container, $modules);

$response = $app->run(ServerRequest::fromGlobals());
send($response);
