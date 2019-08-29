<?php
namespace DBAccess;

use Exception;

/**
 * Implements an exception used for file access errors.
 *
 * This exception can be used whenever database access problems occur. This might be in the case when using wrong
 * user credentials or writing SQL, that is syntactically not valid.
 *
 * @author  Wolfgang Hochleitner <wolfgang.hochleitner@fh-hagenberg.at>
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @version 2018
 */
class DatabaseException extends Exception
{
    /**
     * Creates a new DatabaseException. The constructor is redefined in order to make the message parameter mandatory.
     *
     * @param string         $message  The exception message.
     * @param int            $code     An optional exception code.
     * @param Exception|null $previous The previous exception used for the exception chaining.
     */
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Creates a string representation of this exception.
     *
     * @return string The string representation.
     */
    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
