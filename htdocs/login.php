<?php

require "../vendor/autoload.php";

/**
 * Include global constants
 */
require_once '../src/defines.inc.php';

session_start();

use Exercises\Login;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;

/* --- This is the main call of the norm form process
 *
 * Database exceptions are caught only here. A DEBUG page formatted in DBAccess::debugSQL() will be displayed
 * PHP exception are redirected to a common error page
 */try {
    // Defines a new view that specifies the template and the parameters that are passed to the template
    $view = new View(
        "loginMain.html.twig",
        "../templates",
        "../templates_c",
        [
        new PostParameter(Login::EMAIL),
        new PostParameter(Login::PASSWORD)
        ]
    );    // Creates a new Login object and triggers the NormForm process
    $login = new Login($view);
    $login->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo "An error occured in file " . $e->getFile() . " on line " . $e->getLine() . ":" . $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}
