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

        if(file_exists("models/class.".$class.".php"))
            require_once "models/class.".$class.".php";
    }
}
