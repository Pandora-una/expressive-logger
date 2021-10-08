<?php

namespace ExpressiveLogger;

use ExpressiveLogger\Exception\FacadeAlreadyInitializedException;
use ExpressiveLogger\Exception\FacadeNotInitializedException;
use Throwable;

class LoggerFacade
{
    /**
     * @var Logger
     */
    private static $logger;

    /**
     * @param Logger $logger
     * @throws Exception\FacadeAlreadyInitializedException
     */
    static public function setLogger(Logger $logger)
    {
        if (null !== self::$logger) {
            throw new FacadeAlreadyInitializedException('LoggerFacade already initialized');
        }

        self::$logger = $logger;
    }

    public static function __callStatic($name, $arguments)
    {
        if (null === self::$logger) {
            throw new FacadeNotInitializedException(FacadeNotInitializedException::ERROR_MSG);
        }
        self::exceptionFormat($arguments);
        try {
            return call_user_func_array([self::$logger, $name], $arguments);
        } catch (\Throwable $e) {
            return self::$logger->loggerError($e);
        }
    }

    protected static function exceptionFormat(&$args) {
        if ($args[0] instanceof Throwable) {
            $args[1]['exception'] = $args[0];
            $args[0] = ($args[0])->getMessage();
        }
    }
}
