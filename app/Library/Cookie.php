<?php

/**
 * Simple classe to ensure security in the cookie world
 *
 * TODO: Change domain from "localhost" to proper domain when going online
 */

class Cookie
{
    public static function set($name, $value, $expires)
    {
        setcookie($name, $value, time() + $expires, '/', 'localhost', isset($_SERVER["HTTPS"]), true);
    }

    public static function delete($name)
    {
        unset($_COOKIE[$name]);
        setcookie($name, "", -1, '/', 'localhost', isset($_SERVER["HTTPS"]), true);
    }
}

//Cookie::set("YOLO", "SWAG", 31536000);
Cookie::delete("YOLO");
