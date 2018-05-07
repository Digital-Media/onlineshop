<?php

require "../vendor/autoload.php";

/**
 * Einbinden der define-Angaben für den OnlineShop
 */
require_once '../src/defines.inc.php';

session_start();

use Exercises\Register;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;


/* --- This is the main call of the norm form process
 *
 * Datenbank-Exceptions werden erst hier abgefangen und eine formatierte DEBUG-Seite
 *  mit den Fehlermeldungen mit echo ausgegeben @see DBAcess::dbugSQL()
 * Bei PHP-Exception wird vorerst nur auf eine allgemeine Errorpage weitergeleitet
 */
try {
    // Creates a new Product object and triggers the NormForm process
    $view = new View(
        "registerMain.html.twig",
        "../templates",
        "../templates_c",
        [
        new PostParameter(Register::FIRSTNAME),
        new PostParameter(Register::LASTNAME),
        new PostParameter(Register::NICKNAME),
        new PostParameter(Register::PHONE),
        new PostParameter(Register::MOBILE),
        new PostParameter(Register::FAX),
        new PostParameter(Register::EMAIL),
        new PostParameter(Register::PASSWORD),
        new PostParameter(Register::PASSWORDREPEAT)
    ]);

// Creates a new IMAR object and triggers the NormForm process
    $register = new Register($view);
    $register->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo "An error occured in file " . $e->getFile() ." on line " . $e->getLine() .":" . $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}