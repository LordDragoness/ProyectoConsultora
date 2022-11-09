<?php

namespace SistemaTique\Database;


use Exception;
use PDO;
use Psr\Log\LoggerInterface;
use SistemaTique\Helpers\NewLogger;


/* It's a class that connects to a database and returns a PDO object. */
class Connection
{

    private static LoggerInterface $log;

    /**
     * It connects to the database and returns a PDO object.
     * 
     * @return bool|PDO A PDO object
     */
    public static function dbConnection(): bool|PDO
    {
        self::$log = NewLogger::newLogger('DATABASE','FirePHPHandler');

        $result = false;

        try {
            self::$log->info('Connecting to Database');
            $result = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
            $result->setAttribute(PDO::ATTR_EMULATE_PREPARES, 1);

            self::$log->info('Database connection established');


        } catch (Exception $exception) {
            self::$log->emergency("Oh no, cannot connect to the Database", array('exception' => $exception));
        }

        return $result;
    }
}