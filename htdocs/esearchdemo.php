<?php

require "../vendor/autoload.php";

/**
 * Include global constants
 */
require_once '../src/defines.inc.php';

session_start();

use DBAccess\ESearchDemo;
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
        "esearchdemoMain.html.twig",
        "../templates",
        "../templates_c",
        [
            new PostParameter(ESearchDemo::COPY_TO),
            new PostParameter(ESearchDemo::LIKE),
            new PostParameter(ESearchDemo::MATCH),
            new PostParameter(ESearchDemo::NO_CURSOR),
            new PostParameter(ESearchDemo::PAGING)
        ]
    );
    // Creates a new Product object and triggers the NormForm process
    $esdemo = new ESearchDemo($view);
    $esdemo->normForm();
} catch (Exception $e) {
    echo "<h2>Something went wrong</h2>";
    echo $e;
}
