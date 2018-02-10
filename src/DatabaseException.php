<?php
namespace onlineshop;

use Exception;

/**
 * Definiert eine eigene Exception Klasse für Datenbankexceptions
 */
class DatabaseException extends Exception
{
    /**
     * Der Konstruktur wird umdefiniert, so dass $message nicht mehr optional ist
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Erzeugen einer string Representaion des Objects
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
