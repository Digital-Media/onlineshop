<?php
session_start();
/**
 * Einbinden der define-Angaben für den OnlineShop
 */
require_once 'src/defines.inc.php';
require_once UTILITIES;
/**
 * Einbinden der Klasse TNormform, die die Formularabläufe festlegt.
 */
require_once TNORMFORM;
/**
 * Einbinden der Datenbank-Klasse  DBAccess, die die Datenbankzugriffe implementiert
 */
require_once DBACCESS;
require_once 'src/excercises/shop.php';

/* --- This is the main call of the norm form process
 *
 * Datenbank-Exceptions werden erst hier abgefangen und eine formatierte DEBUG-Seite
 * mit den Fehlermeldungen mit echo ausgegeben @see DBAcess::debugSQL()
 * Bei PHP-Exception wird vorerst nur auf eine allgemeine Errorpage weitergeleitet
 */
try {
    // Defines a new view that specifies the template and the parameters that are passed to the template
    $view = new View("indexMain.tpl", [
        new PostParameter(Shop::SEARCH)
    ]);
    // Creates a new Shop object and triggers the NormForm process
    $shop = new Shop($view);
    $shop->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}
