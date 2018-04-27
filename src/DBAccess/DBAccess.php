<?php
namespace DBAccess;

use \PDOException;
use \PDO;

/*
 * Einbinden der Klasse für die Datenbankexception
 */
require_once 'DatabaseException.php';

/**
 * Die objektorientierte Datenbankklasse beruht auf PDO/MariaDB und setzt prepared statements um.
 *
 * Der PDO-Treiber für MySQL und MariaDB ist vorerst indentisch. Man kann also MariaDB stattt MySQL installieren und
 * den gleichen Treiber verwenden.
 * Umgesetzt sind alle für die Übungen notwendigen Basisfunktionen des Datenbankzugriffs um CRUD-Operationen ausführen
 * zu können.
 * @author Martin Harrer <martin.harrer@fh-hagenberg.at>
 * @package onlineshop
 * @version 2016
 */
class DBAccess
{

    /**
     * Variablen für die Datenbankklasse DBAccess
     *
     * @var string $dbh DatenBankHandler
     * $var string $stmt Statementhandler für Datenbankanfragen
     * $var bool $alreadyset Hilfsvariable um die Mehrfachausgabe beim Debuggen von SQL-Statements zu verhindern
     * $var string $query contains the current query sent to prepareQuery
     */
    private $dbh;
    private $stmt;

    /**
     * Erzeugt ein neues Datenbankobjekt.
     *
     * Die Verbindung zur Datenbank wird mit den übergebenen Parametern hergestellt.
     * Die Datenbankverbindung ist persistent
     * PDO wird in den ErrorMode mit Exceptions statt Errors gesetzt
     *
     * @param string $dsn Data Source Name inluding Databasetype, Host, Port, Databasename
     * @param string $mysqlUser Database User to connect with
     * @param string $mysqlPwd Password for Database User
     * @param string $names Characterset for Database Connection, Default utf8
     * @param string $collate Collation for Characterset, Default utf8_general_ci
     *
     * @return handle $dbh Gibt den Datenbankhandler für die Datenbankzugriffe zurück
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
            // Für persistente Verbindungen wird bei Abbruch der Datenbankverbindung ein Warning ausgegeben.
            // Dieses Warning wird, wenn in der php.ini error_reporting=E_ALL gesetzt ist, auf der Webseite angezeigt
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
     * Prepare Aufruf für SQL-Statements
     * Diese Phase umfasst parsen auf Syntax-Schlüsselwörter (SELECT, INSERT, WHERE, ...), Prüfung
     * auf Schemakonformität (Tabellennamen, Spaltennamenm, ...), Erstellung und Optimierung des
     * Ausführungsplanes (Verwendung von Indizes, ...)
     *
     * @param string $query beinhaltet das SQL-Statement, das von der Datenbank aufbereitet (prepare) werden soll.
     *
     * @return mixed $stmt Gibt den Statementhandler für die weitere Ausführung zurück
     * @throws DatabaseException $e reicht eine aufbereitete PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function prepareQuery(string $query, bool $debug = false)
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
     * Implementiert die Bind-Phase bei der Abarbeitung eines SQL-Statements
     *
     * bindValueByType() benötigt man nur, wenn man die Parameter nicht als Array direkt an execute() geben möchte
     * Man kann die Parameter in dieser Methode einzeln einen Typ zuweisen
     * Es gibt nur Zuweisungen für die unten aufgeführten Datentypen.
     * Es fehlen unter anderem FLOAT, DECIMAL, LOBs, DATE, ...
     * Als Array direkt an execute() übergeben werden alle Datentypen als PDO::PARAM_STR (string) behandelt.
     *
     * @param string $param Name des SQL named parameter
     * @param string $value Value des SQL named parameter, der zugewiesen wird
     * @param null $type Typ des Parameters: PDO::PARAM_STR, PDO::PARAM_INT, PDO::PARAM_BOOL, PDO::PARAM_NULL
     *
     * @return mixed $stmt Statementhandler für die weitere Verarbeitung des SQL-Statements
     */
    public function bindValueByType($param, $value, $type = null)
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
        // bindValue() wird statt bindParam() verwendet,
        // weil bindParam nur notwendig ist bei INPUT/OUTPUT-Parametern z.B. bei Stored Procedures
        // Für bindParam() können Werte zwischen bind() und der execute() noch überschrieben werden.
        // Wollen wir hier nicht zulassen.
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * Führt die execute Phase für die Abarbeitung des SQL-Statements aus
     *
     * Es sind zwei Varianten implementiert.
     *      1. Mit Übergabe der Parameter in einem Array
     *      2. Ohne Übergabe der Parameter in einem Array, das den Aufruf von
     *         @method bindValueByType() im Vorfeld erfordert
     * Im Fehlerfall wird das SQL-Statement mit debugDumpParams() ausgegeben
     *
     * @param array $params Array mit den named Parametern des SQL-Statements
     *
     * @return bool Gibt true zurück, wenn das Statement ausgeführt werden konnte
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function executeStmt($params = null)
    {
        try {
            if (isset($params)) {
                // Wenn execute() mit Parametern aufgerufen wird, werden alle Werte als PDO::PARAM_STR behandelt.
                return $this->stmt->execute($params);
            } else {
                // Es gibt kein PDO::PARAM_FLOAT/DECIMAL/DATE, PDO::PARAM_INT ist nur relevant für PK/FK.
                // Man kann also bindValue weglassen, weil der default Type in $stmt->execute() PDO::PARAM_STR ist
                // siehe executeStmt($params) im then-Zweig
                // Dieser Zweig ist hier zu Demonstrattionszwecken implementiert
                return $this->stmt->execute();
            }
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Holt alle Treffer des mit @method executeStmt() ausgeführten SQL-Statements ab und gibt sie zurück
     *
     * Im Fehlerfall wird das SQL-Statement mit debugDumpParams() ausgegeben
     *
     * @return mixed Resultset des SQL-Statements
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function fetchResultset()
    {
        try {
            return $this->stmt->fetchAll();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Holt einen einzelnen Datensatz aus der Datenbank ab.
     *
     * Im Fehlerfall wird das SQL-Statement mit debugDumpParams() ausgegeben
     *
     * @return mixed Gibt
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function fetchSingle()
    {
        try {
            return $this->stmt->fetch();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Zählt die Treffer in einem Resultset
     *
     * Im Fehlerfall wird das SQL-Statement mit debugDumpParams() ausgegeben
     *
     * @return mixed Anzahl der Treffer
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function rowCount()
    {
        try {
            return $this->stmt->rowCount();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Gibt den vom Autoincrement vergebenen Primärschlüssel des zuletzt in dieser Session
     * ausgeführten insert-Statements zurück
     *
     * Im Fehlerfall wird das SQL-Statement mit debugDumpParams() ausgegeben
     *
     * @return string AutoincrementID des letzten Insert
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function lastInsertId()
    {
        try {
            return $this->dbh->lastInsertId();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Startet eine Datenbanktransaktion
     *
     * Im Fehlerfall wird das SQL-Statement mit debugDumpParams() ausgegeben
     *
     * @return bool TRUE im Gutfall, FALSE im Fehlerfall
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function beginTransaction()
    {
        try {
            return $this->dbh->beginTransaction();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Beendet eine Datenbanktransaktion mit commit. Die Ergebnisse werden in der Datenbank gespeichert
     *
     * Im Fehlerfall wird das SQL-Statement mit debugDumpParams() ausgegeben
     *
     * @return mixed
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function commitTransaction()
    {
        try {
            return $this->dbh->commit();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Beendet eine Datenbanktransaktion mit rollback.
     * Die Datenbank wird in den Zustand zu Beginn der Transaktion (@method beginTransaction) zurückgesetzt.
     *
     * Im Fehlerfall wird das SQL-Statement mit debugSQL() ausgegeben
     *
     * @return bool
     * @throws DatabaseException $e reicht eine PDOExeption an das PHP-Exceptionhandling weiter
     */
    public function rollbackTransaction()
    {
        try {
            return $this->dbh->rollBack();
        } catch (PDOException $e) {
            $formatedError = $this->debugSQL();
            throw new DatabaseException($formatedError);
        }
    }

    /**
     * Für die MySQL/MariaDB-Fehlerausgabe in einer statischen DEBUG Error Page formatieren
     * wird die MySQL/MariaDB Fehler Meldung etwas schöner,
     * vergeben Namen für die Werte und ergänzen sie um das SQL-Statement und den PHP Call Stack
     *
     *
     * @return string $formatedError Gibt bei DEBUG = TRUE eine formatierte Errorpage
     *                               mit dem fehlerhaften SQL-Statement, der SQL-Fehlermeldung
     *                               und dem PHP Call Stack zurück.
     */
    public function debugSQL($PDOError = null)
    {
        if ($this->stmt) {
            // PDO SQL Error Array auf mehrere Variablen aufteilen,
            // damit sie später in HTML besser formatiert werden können
            $err_info   = $this->stmt->errorInfo();
            $sqlerror =  $err_info[2];
            $sqlerrormessage = $err_info[1];
            $ansisqlstate = $err_info[0];
            // PDO SQL Statement vom Ausgabepuffer in eine Zwischenvariable schreiben und diesen leeren,
            // sodass nichts mehr direkt in den Browser ausgegeben wird
            ob_start();
            $this->stmt->debugDumpParams();
            $out1 = ob_get_contents();
            ob_clean();
            // Das Ganze noch etwas lesbarer formatieren
            $out1 = str_replace(']', ']<br><br>', $out1);
            $out1 = str_replace(PHP_EOL, '<br>', $out1);
            $debugdumpparams = str_replace('Params', '<br><br><b>SQL Prepared Statement Parameters</b><br><br>', $out1);
        } else {
            // Variablen initialisieren, die nicht belegt sind,
            // wenn der Fehler schon bei der Verbindung zur Datenbank auftritt
            $sqlerror = null;
            $sqlerrormessage = null;
            $ansisqlstate = null;
            $debugdumpparams = null;
        }
        // PHP Call Stack vom Ausgabepuffer in eine Zwischenvariable schreiben und leeren,
        // sodass nichts mehr direkt in den Browser ausgegeben wird
        ob_start();
        debug_print_backtrace();
        $out2 = ob_get_contents();
        // Das Ganze noch etwas lesbarer formatieren
        $phpcallstack = str_replace('#', '<br>#', $out2);
        ob_clean();
        // Statische DEBUG Error Page erstellen, die statt des Smarty Templates ausgegeben wird
        /*        $formatedError = <<<ERRORPAGE
                <!DOCTYPE html>
                    <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <title>DEBUG Error Page</title>
                        </head>
                        <body>
                            <div>
                                <h2> DEBUG Error Page for $_SERVER[SCRIPT_NAME] </h2>
                                    <p><b> To hide error messages and redirect to errorpage.html set DEBUG = FALSE in normform/define.inc.php </b></p>
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
//*/
        $formatedError = <<<ERRORPAGE
                    <div>
                        <h2> DEBUG Error Page for $_SERVER[SCRIPT_NAME] </h2>
                            <p><b> To hide error messages and redirect to errorpage.html set DEBUG = FALSE in normform/define.inc.php </b></p>
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
ERRORPAGE;

        // In error_log schreiben, um Fehler nicht im Browser anzuzeigen
        error_log($formatedError, 0);
        // Fehlerbeschreibung an catch-Block weiterreichen.
        // Wird mit throw im catch-Block nochmals als DatabaseException weitergereicht und dann mit echo ausgegeben
        return $formatedError;
    }
}
