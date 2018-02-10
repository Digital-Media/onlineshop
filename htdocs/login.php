<?php
use onlineshop\src\exercises\Login;

session_start();
/**
 * Einbinden der define-Angaben für den OnlineShop
 */
require_once '../src/defines.inc.php';
require_once UTILITIES;
/**
 * Einbinden der Klasse TNormforme, die die Formularabläufe festlegt.
 */
require_once SMARTY;
require_once TNORMFORM;
/**
 * Einbinden der Datenbank-Klasse  DBAccess, die die Datenbankzugriffe implementiert
 */
require_once DBACCESS;
require_once '../src/exercises/login.php';
/* --- This is the main call of the norm form process
 *
 * Datenbank-Exceptions werden erst hier abgefangen und eine formatierte DEBUG-Seite mit den Fehlermeldungen
 * mit echo ausgegeben @see DBAcess::debugSQL()
 * Bei PHP-Exception wird vorerst nur auf eine allgemeine Errorpage weitergeleitet
 */
try {
    // Defines a new view that specifies the template and the parameters that are passed to the template
    $view = new View(
        "loginMain.tpl",
        [
        new PostParameter(Login::EMAIL),
        new PostParameter(Login::PASSWORD)
        ]
    );    // Creates a new Login object and triggers the NormForm process
    $login = new Login($view);
    $login->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo "An error occured in file " . $e->getFile() ." on line " . $e->getLine() .":" . $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}
