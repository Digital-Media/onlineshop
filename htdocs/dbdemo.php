<?php

require "../vendor/autoload.php";

/**
 * Include global constants
 */
require_once '../src/defines.inc.php';

session_start();
use DBAccess\DBDemo;
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
    echo "<h2>Something went wrong</h2>" . $e->getFile() . " on line " . $e->getLine() .":" . $e->getMessage();
}
