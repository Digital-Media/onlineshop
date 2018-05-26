<?php

require "../vendor/autoload.php";

/**
 * Include global constants
 */
require_once '../src/defines.inc.php';

session_start();

use Exercises\Product;
use Fhooe\NormForm\Parameter\PostParameter;
use Fhooe\NormForm\View\View;
use Utilities\Utilities;

/* --- This is the main call of the norm form process
 *
 * Database exceptions are caught only here. A DEBUG page formatted in DBAccess::debugSQL() will be displayed
 * PHP exception are redirected to a common error page
 */
try {
    // Store current page in SESSION array. login.php uses this entry to redirect back after successful login.
    $_SESSION[REDIRECT]=basename($_SERVER["SCRIPT_NAME"]);
    if (!isset($_SESSION[IS_LOGGED_IN]) || $_SESSION[IS_LOGGED_IN] !== Utilities::generateLoginHash()) {
        // Use this method call to enable login protection for this page
        View::redirectTo('login.php');
    }
    // Defines a new view that specifies the template and the parameters that are passed to the template
    $view = new View(
        "productMain.html.twig",
        "../templates",
        "../templates_c",
        [
        new PostParameter(Product::PNAME),
        new PostParameter(Product::PTYPE),
        new PostParameter(Product::PRICE),
        new PostParameter(Product::ACTIVE),
        new PostParameter(Product::SHORTDESC),
        new PostParameter(Product::LONGDESC)
        ]
    );
    // Creates a new Product object and triggers the NormForm process
    $product = new Product($view);
    $product->normForm();
} catch (Exception $e) {
    if (DEBUG) {
        echo "An error occured in file " . $e->getFile() ." on line " . $e->getLine() .":" . $e->getMessage();
    } else {
        echo "<h2>Something went wrong</h2>";
    }
}
