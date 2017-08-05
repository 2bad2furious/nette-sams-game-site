<?php

use Nette\Utils\DateTime;

function diedump($var) {
    foreach (func_get_args() as $arg) {
        \Tracy\Debugger::dump($arg);
    }
    exit();
}

function exception() {
    throw new Exception("called");
}

function handleWarningsAndErrors(int $errno, string $errstr, string $errfile, int $errline, array $errcontext) {
    throw new ErrorException($errstr, $errno, 1, $errfile, $errline, null);
}

function getConnection(\Nette\DI\Container $container): Nette\Database\Connection {
    return $container->getByType(\Nette\Database\Connection::class);
}

set_error_handler("handleWarningsAndErrors");

if (isset($_SERVER["TOKEN"]) && $session_id = $_SERVER["TOKEN"]) {
    session_id($session_id);
}

require_once __DIR__ . "/../vendor/autoload.php";

// Configure application
$configurator = new Nette\Configurator;

$configurator->setDebugMode("192.168.1.54");

// prod mode => $configurator->setDebugMode(false);

// Enable Tracy for error visualisation & logging
$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$loader = $configurator->createRobotLoader();
$loader->addDirectory(__DIR__ . "/../app");
$loader->register();

$configurator->addConfig(__DIR__ . "/../config/config.neon");
$configurator->addConfig(__DIR__ . "/../config/config.local.neon");
$configurator->addConfig(__DIR__ . "/../config/config.product.neon");

$container = $configurator->createContainer();

$connection = getConnection($container);
$connection->beginTransaction();

try {
    $app = $container->getByType(Nette\Application\Application::class);

    $app->run();

    $connection->commit();
} catch (Exception $ex) {
    $connection->rollBack();
    throw $ex;
    //echo "error occured";
}