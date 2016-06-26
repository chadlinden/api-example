<?php
error_reporting(E_ALL); ini_set('display_errors', 1);
require __DIR__ . '/../src/helpers.php';

// Setup environment
define('BASE_PATH', realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR);
require BASE_PATH . 'vendor/autoload.php';
define('API_VERSION', "v1");

// Bootstrap database and Eloquent
$injector = new Auryn\Injector();
$boot = new ChadLinden\Api\Bootstrap( $injector );

// Build mostly default Equip
Equip\Application::build( $injector )
    ->setConfiguration([
        Equip\Configuration\AurynConfiguration::class,
        Equip\Configuration\DiactorosConfiguration::class,
        Equip\Configuration\PayloadConfiguration::class,
        Equip\Configuration\RelayConfiguration::class,
        Equip\Configuration\WhoopsConfiguration::class,
    ])
    ->setMiddleware([
        ChadLinden\Api\AuthHandler::class,
        Relay\Middleware\ResponseSender::class,
        Equip\Handler\ExceptionHandler::class,
        Equip\Handler\DispatchHandler::class,
        Equip\Handler\JsonContentHandler::class,
        Equip\Handler\FormContentHandler::class,
        Equip\Handler\ActionHandler::class,
    ])
    ->setRouting(function (Equip\Directory $directory) {
        return $directory
            ->get('/'.API_VERSION.'/shifts/{user_id}', ChadLinden\Api\Domains\Shift\GetShift::class)
            ->get('/'.API_VERSION.'/user/{user_id}', ChadLinden\Api\Domains\User\GetUser::class)

            ->put('/'.API_VERSION.'/shift/update', ChadLinden\Api\Domains\Shift\PutShift::class)
            
            ->post('/'.API_VERSION.'/shift/create', ChadLinden\Api\Domains\Shift\PostShift::class)            
            ->post('/login', ChadLinden\Api\Domains\Auth\Authenticate::class)
            ->post('/'.API_VERSION.'/login', ChadLinden\Api\Domains\Auth\Authenticate::class);
    })
    ->run();

