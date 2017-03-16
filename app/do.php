<?php
/**
 * Do.php recoit les appels aux controlleurs et les rediriges vers le controlleur concerné.
 * Il se charge aussi d'initialiser l'environnement de travail de ces derniers
 */

//Set encoding as UTF8 to prevent invalid characters
setlocale(LC_ALL, "fr_FR.utf8");

//Init the autoloader to prevent the need for including classes in the future
require_once "Library/Autoloader.php";
Autoloader::register();
Autoloader::staticLoads();

//Collect all parameters and make them accessible everywhere
$PARAMS = new ParamController();

//Open session using the session handler
Session::open();

//sleep for 1ms as a  protection against DDoS attack.
usleep(100000);

//Finally init the frontController use its run method to call the controller.
$frontController = new FrontController();
$frontController->run();
