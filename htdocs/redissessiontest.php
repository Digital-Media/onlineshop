<?php
//simple counter to test sessions. should increment on each page reload.
ini_set("session.save_handler", "redis");
ini_set("session.save_path", "tcp://192.168.7.7:6379?auth=geheim");
echo ini_get("session.save_handler"). "<br>";
echo ini_get("session.save_path"). "<br>";
session_start();
$count = isset($_SESSION['count']) ? $_SESSION['count'] : 1;


$redis = new Redis();
$redis->pconnect('localhost', 6379);
$redis->auth('geheim');
$current_session = $redis->keys('PHPREDIS_SESSION*');
var_dump($redis->get($current_session[0]));


echo $count . "<br>";

$_SESSION['count'] = ++$count;
