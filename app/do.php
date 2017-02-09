<?php
session_start();
/**
 * Do.php recoit les appels aux controlleurs et les rediriges vers le controlleur concerné.
 */

require_once "Library/Autoloader.php";
Autoloader::register();
Autoloader::staticLoads();

$frontController = new FrontController();
$frontController->run();
