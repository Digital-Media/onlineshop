<?php
namespace DBAccess;

use \PDOException;
use \PDO;
use Utilities\LogWriter;

/**
 * The class DBAccess uses object orientated PHP, PDO for access to MariaDB and implements prepared statements.
 *
 * The PDO driver for MySQL and MariaDB are identically at the moment.
 * You can use either MariaDB or MySQL for projects using this class.
 * All api functions for CRUD operations are implemented using prepared statements.
 * All PDOExceptions are enclosed by a Database Exception for easier debugging with monolog.
 *
 * @author  Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2018
 */
class DBAccess
{

    /**
     * Properties for the class DBAccess
     *
     * @var string $dbh Database handler
     * $var string $stmt Statement handler for database queries
     */
    private $dbh;
    private $stmt;

    /**
     * @var string $logWriter  Instance of monolog to write logs to onlineshop.log
     */
    private $logWriter;

    /**
     * Constructor for DBAccess.
     *
     * The database connection $dbh is set up using the given credentials
     * The connection to the database is persistent.
     * PDO is configured for using the ErrorMode Exceptions instead of Errors.
     *
     * @param string $dsn       Data Source Name including Database Type, Host, Port, Database Name
     * @param string $mysqlUser Database User to connect with
     * @param string $mysqlPwd  Password for Database User
     * @param string $names     Characterset for Database Connection, Default utf8
     * @param string $collate   Collation for Characterset, Default utf8_general_ci
     * @param string $multi     Defines if MULTI_STATEMENTS can be used
     *
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */

    public function __construct(
        $dsn = null,
        $mysqlUser = null,
        $mysqlPwd = null,
        $names = "utf8",
        $collate = 'utf8_general_ci',
        $multi = false
    ) {
        $this->logWriter = LogWriter::getInstance();
        $charsetAttr="SET NAMES $names COLLATE $collate";
        $options = array(
            // A warning is given for persistent connections in case of a interrupted database connection.
            // This warning is shown on the web page if error_reporting=E_ALL is set in php.ini
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => $charsetAttr,
            PDO::MYSQL_ATTR_MULTI_STATEMENTS => $multi
        );
        try {
            $this->dbh = new PDO($dsn, $mysqlUser, $mysqlPwd, $options);
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL($e->getMessage());
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Prepare the SQL statement
     *
     * Creates the statement handler for the execution step
     * This step covers
     * syntax parsing of keywords (SELECT, INSERT, WHERE, ...),
     * checking schema compliance (table names, column names, ...),
     * creating and optimizing the execution plan (using indexes, ...)
     *
     * @param string $query Contains the SQL statement, that is prepared by the database.
     * @param bool   $debug If true the SQL statement is returned to the browser
     *
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function prepareQuery(string $query, bool $debug = false):void
    {
        try {
            if ($this->dbh) {
                $this->stmt = $this->dbh->prepare($query);
                if ($debug) {
                    $this->logWriter->logDebug("DBAccess: " . $query);
                }
            }
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * Bind the given parameters to the parsed SQL statement for the execution step
     *
     * bindValueByType() is only needed when parameters are not passed directly to execute() with an array.
     * If parameters are sent to execute() directly with an array, all parameters are of type PDO::PARAM_STR (string).
     * This method can be used to give an type explicitly.
     * There are only data types for INT, BOOL, STRING and NULL.
     * FLOAT, DECIMAL, LOBs, DATE, ... are not available.
     *
     * @param string $param Name of the SQL named parameter
     * @param string $value Value of the SQL named parameter, that is assigned
     * @param null   $type  type of the parameter: PDO::PARAM_STR, PDO::PARAM_INT, PDO::PARAM_BOOL, PDO::PARAM_NULL
     */
    public function bindValueByType($param, $value, $type = null):void
    {
        if (is_null($type)) {
            switch (true) {
            case is_int($value):
                $type = PDO::PARAM_INT;
                break;
            case is_bool($value):
                $type = PDO::PARAM_BOOL;
                break;
            case is_null($value):
                $type = PDO::PARAM_NULL;
                break;
            default:
                $type = PDO::PARAM_STR;
            }
        }
        // bindValue() is used instead of bindParam(),
        // because bindParam() is only needed for INPUT/OUTPUT parameters f.e. used in stored procedures
        // With bindParam() values can be overwritten between bind() and execute().
        // That is not, what we need in our use cases.
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Executes the SQL statement
     *
     * Two versions are implemented.
     *      1. Parameters are given in an assigned array, while calling execute()
     *      2. No array is handed during execute() call, which requires the call of
     *
     * @method bindValueByType() before calling execute()
     * In case of an Error the SQL statement is dumped with debugDumpParams()
     *
     * @param array $params Array with the named parameters of the SQL statement
     *
     * @return bool Returns true if the statement could be executed
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function executeStmt($params = null):bool
    {
        try {
            if (isset($params)) {
                // If execute() is called with a parameter array, all values are of type PDO::PARAM_STR.
                return $this->stmt->execute($params);
            } else {
                // PDO::PARAM_FLOAT/DECIMAL/DATE are not available
                // PDO::PARAM_INT is mostly relevant for PK/FK.
                // Databases implicitly convert the data type
                // Therefore bindValue() can be omitted, although the default type in $stmt->execute() is PDO::PARAM_STR
                // The else branch is for statements using bindValue() or not using SQL named parameters.
                return $this->stmt->execute();
            }
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * Fetches all rows returned by @method executeStmt()
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return array Nested array with the result set of the SQL statement, if multiple rows are found
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function fetchResultset():array
    {
        try {
            return $this->stmt->fetchAll();
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * Fetches a single row from the database.
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return array|bool Plain array with the result set of the SQL statement, if result is only a single row.
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function fetchSingle()
    {
        try {
            return $this->stmt->fetch();
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * Counts the rows in a result set for INSERT, UPDATE, DELETE
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return int row count
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function rowCount():int
    {
        try {
            return $this->stmt->rowCount();
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * Returns the AutoincrementID value of the PK assigned in the most recent insert of the current session
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return string AutoincrementID of the last insert
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function lastInsertId():string
    {
        try {
            return $this->dbh->lastInsertId();
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * Starts a database transaction
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return bool TRUE if it worked, else FALSE
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function beginTransaction():bool
    {
        try {
            return $this->dbh->beginTransaction();
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * The current transaction is commited. The results are persisted in the database.
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return bool TRUE if it worked, else FALSE
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function commitTransaction():bool
    {
        try {
            return $this->dbh->commit();
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * A database transaction is rolled back.
     * The database returns to the state at the begin of the transaction (@method beginTransaction).
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return bool TRUE if it worked, else FALSE
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function rollbackTransaction():bool
    {
        try {
            return $this->dbh->rollBack();
        } catch (PDOException $e) {
            throw new DatabaseException($this->debugSQL());
        }
    }

    /**
     * Writing a full error message to a application log file using Utilities/LogWriter based on monolog
     *
     * @return string $formatedError Returns the SQL or PDO error, depending on which one is set
     */
    public function debugSQL($PDOError = null):string
    {
        if ($this->stmt) {
            // Split the PDO SQL Error Array to multiple variables.
            // This makes formatting them in HTML easier.
            $err_info   = $this->stmt->errorInfo();
            $ansisqlstate = $err_info[0];
            $sqlerror =  $err_info[1];
            $sqlerrormessage = $err_info[2];
            // Write the PDO SQL Statement from the output buffer to a PHP variable and empty it afterwards.
            // No direct output to the browser occurs in this case.
            ob_start();
            $this->stmt->debugDumpParams();
            $debugdumpparams = ob_get_contents();
            ob_clean();
        } else {
            // initialize variables, that are not set, if the error occurs in the constructor
            $sqlerror = null;
            $sqlerrormessage = null;
            $ansisqlstate = null;
            $debugdumpparams = null;
        }
        // Write the PHP Call Stack from the output buffer to a PHP variable and empty it afterwards.
        // No direct output to the browser occurs in this case.
        ob_start();
        debug_print_backtrace();
        $phpcallstack = ob_get_contents();
        ob_clean();
        // Write to application log, to document errors
        //*
        $this->logWriter->logError(' #### Start Errormessages from DBAccess ####');
        $this->logWriter->logError(' ANSI STATE: ' . $ansisqlstate);
        $this->logWriter->logError(' SQL ERROR: ' . $sqlerror);
        $this->logWriter->logError(' SQL ERROR MESSAGE: ' . $sqlerrormessage);
        $this->logWriter->logError(' DEBUG PARAMS: ' . $debugdumpparams);
        $this->logWriter->logError(' PHP CALL STACK: ' . $phpcallstack);
        $this->logWriter->logError(' PDO ERROR: ' . $PDOError);
        $this->logWriter->logError(' #### End Errormessages from DBAccess ####');
        //*/
        // Pass error description to catch block.
        // This error message is returned by the Database Exception thrown in the catch block of the method called,
        // and can be used to send it to the browser with echo in the catch block of a project using DBAccess
        $message = $sqlerrormessage ? $sqlerrormessage : $PDOError;
        $message .= "Severe Error: See onlineshop/Utilities/onlineshop.log for more information";
        return $message;
    }
}
