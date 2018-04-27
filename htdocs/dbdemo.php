<?php

require "../vendor/autoload.php";

/**
 * Einbinden der define-Angaben für den OnlineShop
 */
require_once '../src/defines.inc.php';

session_start();

use DBAccess\DBDemo;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;

/* --- This is the main call of the norm form process
 *
 * Datenbank-Exceptions werden erst hier abgefangen und eine formatierte DEBUG-Seite
 * mit den Fehlermeldungen mit echo ausgegeben @see DBAcess::debugSQL()
 * Bei PHP-Exception wird vorerst nur auf eine allgemeine Errorpage weitergeleitet
 */
try {
    // Defines a new view that specifies the template and the parameters that are passed to the template
    $view = new View(
        "dbdemoMain.html.twig",
        "../templates",
        "../templates_c",
        [
        new PostParameter(DBDemo::PTYPE)
        ]
    );
    // Creates a new Product object and triggers the NormForm process
    $dbdemo = new DBDemo($view);
    $dbdemo->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo "An error occured in file " . $e->getFile() ." on line " . $e->getLine() .":" . $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}
