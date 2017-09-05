<?php

use Nette\Utils\DateTime;

function diedump($var) {
    foreach (func_get_args() as $arg) {
        \Tracy\Debugger::dump($arg);
    }
    exit();
}

function handleWarningsAndErrors(int $errno, string $errstr, string $errfile, int $errline, array $errcontext) {
    throw new ErrorException($errstr, $errno, 1, $errfile, $errline, null);
}

set_error_handler("handleWarningsAndErrors");


/** checks headers for token
 * if found, sets the session_id as the token
 * else sends back a new token
 */
if (isset($_SERVER["HTTP_TOKEN"]) && $session_id = $_SERVER["HTTP_TOKEN"]) {
    session_id($session_id);
    $token = $session_id;
} else if (!isset($_COOKIE["PHPSESSID"])) {
    $token = session_id();
    if (!$token) {
        $token = session_create_id();
    }
    header("HTTP_TOKEN: $token", false);
}

require_once __DIR__ . "/../vendor/autoload.php";

// Configure application
$configurator = new Nette\Configurator;

$configurator->setDebugMode("192.168.1.54");

/* prod mode =>S $configurator->setDebugMode(false);/* */

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

$connection = $container->getByType(\Nette\Database\Context::class);
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