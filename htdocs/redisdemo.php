<?php

require "../vendor/autoload.php";

/**
 * Include global constants
 */
require_once '../src/defines.inc.php';

ini_set("session.save_handler", "redis");
ini_set("session.save_path", "tcp://localhost:6379?auth=geheim");
session_start();

use DBAccess\RedisDemo;
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
        "redisdemoMain.html.twig",
        "../templates",
        "../templates_c",
        [
        new PostParameter(RedisDemo::REDIS_KEY),
        ]
    );
    // Creates a new Product object and triggers the NormForm process
    $redisdemo = new RedisDemo($view);
    $redisdemo->normForm();
} catch (Exception $e) {
    echo $e->getMessage() . " on line " . $e->getLine() . " in file " . $e->getFile();
    echo "<br> Type StartRedis.sh to start Redis. To stop Redis type StopRedis.sh";
}
