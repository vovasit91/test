<?php

require '../vendor/autoload.php';
(Dotenv\Dotenv::createImmutable(dirname(__DIR__)))->load();
define('MAIN_DIR',  dirname(__DIR__));

$handler = new \App\EventHandler();