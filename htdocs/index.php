<?php

require "../vendor/autoload.php";

/**
 * Include global constants
 */
require_once '../src/defines.inc.php';

session_start();

use Exercises\OnlineShop;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;

/* --- This is the main call of the norm form process
 *
 * Database exceptions are caught only here. A DEBUG page formatted in DBAccess::debugSQL() will be displayed
 * PHP exception are redirected to a common error page
 */
try {
    // Defines a new view that specifies the template and the parameters that are passed to the template
    $view = new View(
        "indexMain.html.twig",
        "../templates",
        "../templates_c",
        [
        new PostParameter(OnlineShop::SEARCH)
        ]
    );
    // Creates a new Shop object and triggers the NormForm process
    $shop = new OnlineShop($view);
    $shop->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo "An error occured in file " . $e->getFile() . " on line " . $e->getLine() . ":" . $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}
