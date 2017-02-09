<?php
/**
 * Class Autoloader
 */
class Autoloader
{
    /**
     * Register Autoloader handler
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    /**
     * Include the required class file
     * @param $class string Name of the class to load
     */
    static function autoload($class)
    {
        $class = strtolower($class);

        if(file_exists("models/.".$class.".php"))
            require_once "models/.".$class.".php";
        else if(file_exists("views/class.".$class.".php"))
            require_once "models/views.".$class.".php";
        else if(file_exists("controllers/".$class.".php"))
            require_once "controllers/".$class.".php";
        else return;
    }

    /**
     * Include all others files that may be needed
     */
    static function staticLoads()
    {
        require_once "Library/DBInterface.php";
    }
}
