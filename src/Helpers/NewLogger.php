<?php

namespace SistemaTique\Helpers;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;


/* This class creates a new logger for debugging tasks. */
class NewLogger
{

    // This method creates a news logger for debugging tasks
    public static function newLogger($name , $handler = null) : LoggerInterface
    {
        $logger = new Logger($name);
        $handlerName = "\Monolog\Handler\\"."$handler";

        $logger->pushHandler(new StreamHandler(__DIR__.'../../../my_app.log', Level::Debug));
        $terminal = new StreamHandler('php://stderr', Level::Debug);
        $logger->pushHandler($terminal);
        $logger->pushHandler(new FirePHPHandler());


        return $logger;
    }

}

