<?php

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

function logException(Exception $ex) {
    /* getConnection()->query("INSERT INTO error", [
            "error_info" => json_encode([
                "exception" => $ex,
                "server" => $_SERVER,
                "session" => $_SESSION,
                "request" => $_REQUEST])
        ]);*/
    //todo send mail
}

set_error_handler("handleWarningsAndErrors");

if (isset($_SERVER["TOKEN"]) && $session_id = $_SERVER["TOKEN"]) {
    session_id($session_id);
}

require_once __DIR__ . "/../vendor/autoload.php";

// Configure application
$configurator = new Nette\Configurator;

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
    $container->getByType(Nette\Application\Application::class)
        ->run();

    $connection->commit();
} catch (Exception $ex) {
    $connection->rollBack();
    logException($ex);
    throw $ex;
    //echo "error occured";
}