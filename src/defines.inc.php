<?php
/**
 * In define.inc.php werden Konstanten festgelegt, die in der gesamten Webapplikation gültig sind
 */

/**
 * @var string IS_LOGGED_IN Key for the session field which remembers that a user is currently logged in.
 */
define("IS_LOGGED_IN", "isloggedin");
define("REDIRECT", "redirect");

/*
 * Connection und andere Parameter für Datenbankzugriffe im ### OnlineShop ### DAB3UE/DBA2UE
 * hier werden die Verbindungsparameter für die Datenbank festgelegt
 *
 * @var string DB_DRIVER Typ der Datenbank, zu der man sich verbindet. Wird von PDO benötigt,
 *  das verschiedene Datenbanktreiber zu verfügung stellt.
 * @var string DB_NAME Name der Datenbank, zu der man sich verbindet
 * @var string DB_HOST Name des Hosts, auf dem die Datenbank läuft
 * @var int DB_PORT Port auf dem die Datenbank erreichbar ist. Bei MySQL ist der default-Wert 3306.
 *                  Wird daher meist nicht angegeben.
 * @var string DSN Data Source Name, setzt sich aus Treibername, Host, Port und Datenbankname zusammen
 * @var string DB_USER Name des Users mit dem sich der OnlineShop mit der MySQL-Datenbank verbindet
 * @var string DB_PWD Passwort für den DB_USER onlineshop.
 * @var string NAMES Characterset der Datenbanke
 * @var string COLLATION Collation der Datenbank, basiert auf NAMES
 */
define("DB_DRIVER", "mysql");
define("DB_NAME", "onlineshop");
define("DB_HOST", "localhost");
define("DB_PORT", 3306);
define("DSN", DB_DRIVER . ":host=". DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME);
define("DB_USER", "onlineshop");
define("DB_PWD", "geheim");
define("DB_NAMES", "utf8");
define("DB_COLLATION", "utf8_general_ci");
