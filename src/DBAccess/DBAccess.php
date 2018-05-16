<?php
namespace DBAccess;

use \PDOException;
use \PDO;

/*
 * Including class for Database Exceptions
 */
require_once 'DatabaseException.php';

/**
 * The class DBAccess uses object orientated PHP, PDO for access to MariaDB and implements prepared statements.
 *
 * The PDO driver for MySQL and MariaDB are identically at the moment.
 * You can use either MariaDB or MySQL for projects using this class.
 * All api functions for CRUD operations are implemented using prepared statements.
 * All PDOExceptions are enclosed by a Database Exception for easier debugging with a comprehensive error page.
 *
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
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
     * Constructor for DBAccess.
     *
     * The database connection $dbh is set up using the given credentials
     * The connection to the database is persistent.
     * PDO is configured for using the ErrorMode Exceptions instead of Errors.
     *
     * @param string $dsn Data Source Name including Database Type, Host, Port, Database Name
     * @param string $mysqlUser Database User to connect with
     * @param string $mysqlPwd Password for Database User
     * @param string $names Characterset for Database Connection, Default utf8
     * @param string $collate Collation for Characterset, Default utf8_general_ci
     *
     *
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */

    public function __construct(
        $dsn = null,
        $mysqlUser = null,
        $mysqlPwd = null,
        $names = "utf8",
        $collate = 'utf8_general_ci'
    ) {
        $charsetAttr="SET NAMES $names COLLATE $collate";
        $options = array(
            // A warning is given for persistent connections in case of a interrupted database connection.
            // This warning is shown on the web page if error_reporting=E_ALL is set in php.ini
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => $charsetAttr,
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
     * @param bool $debug If true the SQL statement is returned to the browser
     *
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function prepareQuery(string $query, bool $debug = false):void
    {
        try {
            if ($this->dbh) {
                $this->stmt = $this->dbh->prepare($query);
                if ($debug) {
                    echo $query . "<br><br>";
                }
            }
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
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
     * @param null $type type of the parameter: PDO::PARAM_STR, PDO::PARAM_INT, PDO::PARAM_BOOL, PDO::PARAM_NULL
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
     *         @method bindValueByType() before calling execute()
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
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
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
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Fetches a single row from the database.
     *
     * In case of an error the SQL statement is dumped with debugDumpParams()
     *
     * @return array Plain array with the result set of the SQL statement, if result is only a single row.
     * @throws DatabaseException passes a comprehensive error page built from PDOExeption $e to PHP exception handling
     */
    public function fetchSingle():array
    {
        try {
            return $this->stmt->fetch();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Counts the rows in a result set
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
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
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
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
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
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
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
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
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
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Building a formatted DEBUG Error Page using HTML
     *
     * @return string $formatedError Returns a formatted error page if DEBUG = TRUE
     *                               including the faulty SQL statement, the SQL error message,
     *                               the PHP Call Stack and some additional information useful for debugging.
     */
    public function debugSQL($PDOError = null):string
    {
        if ($this->stmt) {
            // Split the PDO SQL Error Array to multiple variables.
            // This makes formatting them in HTML easier.
            $err_info   = $this->stmt->errorInfo();
            $sqlerror =  $err_info[2];
            $sqlerrormessage = $err_info[1];
            $ansisqlstate = $err_info[0];
            // Write the PDO SQL Statement from the output buffer to a PHP variable and empty it afterwards.
            // No direct output to the browser occurs in this case.
            ob_start();
            $this->stmt->debugDumpParams();
            $out1 = ob_get_contents();
            ob_clean();
            // Do some formatting for easier reading
            $out1 = str_replace(']', ']<br><br>', $out1);
            $out1 = str_replace(PHP_EOL, '<br>', $out1);
            $debugdumpparams = str_replace('Params', '<br><br><b>SQL Prepared Statement Parameters</b><br><br>', $out1);
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
        $out2 = ob_get_contents();
        // Do some formatting for easier reading
        $phpcallstack = str_replace('#', '<br>#', $out2);
        ob_clean();
        // Create a static DEBUG Error Page, that is displayed in the browser instead of the HTML template
        $formatedError = <<<ERRORPAGE
                <!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <title>DEBUG Error Page</title>
                        </head>
                        <body>
                            <div>
                                <h2> DEBUG Error Page for $_SERVER[SCRIPT_NAME] </h2>
                                    <p><b> To hide error messages and redirect to an error page set DEBUG = FALSE </b></p>
                                    <b style='color: #FF0000;'> Please correct the following Database Error </b><br>
                                    <p style='color: #FF0000;'>$sqlerror$PDOError</p>
                                    <b>MariaDB ErrorCode: </b> $sqlerrormessage
                                    <b>ANSI SQLSTATE: </b> $ansisqlstate
                                    <br><b>For more Information see:</b>
                                    <a href='https://mariadb.com/kb/en/mariadb/mariadb-error-codes/' target='_blank'>MariaDB Error Codes</a> <b> or </b>
                                    <a href='http://dev.mysql.com/doc/refman/5.7/en/error-messages-client.html' target='_blank'>MySQL Client Error Codes</a>
                                    <p><b> SQL Statement </b><p>
                                    $debugdumpparams
                                    <br><br><b>PHP Call Stack:</b><br>
                                    $phpcallstack
                            </div>
                        </body>
                    </html>
ERRORPAGE;

        // Write to error_log, to document errors even if DEBUG = FALSE
        error_log($formatedError, 0);
        // Pass error description to catch block.
        // This error message is returned by the Database Exception thrown in the catch block of the method called,
        // and can be used to send it to the browser with echo in the catch block of a project using DBAccess
        return $formatedError;
    }
}
