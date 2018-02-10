<?php
use onlineshop\src\exercises\MyCart;
use onlineshop\src\Utilities;

session_start();
/**
 * Einbinden der define-Angaben für den OnlineShop
 */
require_once '../src/defines.inc.php';
require_once UTILITIES;
/**
 * Einbinden der Klasse TNormform, die die Formularabläufe festlegt.
 */
require_once SMARTY;
require_once TNORMFORM;
/**
 * Einbinden der Datenbank-Klasse DBAccess, die die Datenbankzugriffe implementiert
 */
require_once DBACCESS;
require_once '../src/exercises/mycart.php';
/* --- This is the main call of the norm form process
 *
 * Datenbank-Exceptions werden erst hier abgefangen und eine formatierte DEBUG-Seite mit den Fehlermeldungen
 *  mit echo ausgegeben @see DBAcess::debugSQL()
 * Bei PHP-Exception wird vorerst nur auf eine allgemeine Errorpage weitergeleitet
 */
try {
    // Store current page in SESSION array. login.php uses this entry to redirect back after successful login.
    $_SESSION[REDIRECT]=basename($_SERVER["SCRIPT_NAME"]);
    if (!isset($_SESSION[IS_LOGGED_IN]) || $_SESSION[IS_LOGGED_IN] !== Utilities::generateLoginHash()) {
        // Use this method call to enable login protection for this page
        View::redirectTo('login.php');
    }
    // Defines a new view that specifies the template and the parameters that are passed to the template
    $view = new View("mycartMain.tpl", [
    ]);
    // Creates a new MyCart object and triggers the NormForm process
    $myCart = new MyCart($view);
    $myCart->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo "An error occured in file " . $e->getFile() ." on line " . $e->getLine() .":" . $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}
