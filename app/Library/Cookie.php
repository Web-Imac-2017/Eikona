<?php

/**
 * Simple classe to ensure security in the cookie world
 *
 * TODO: Change domain from "localhost" to proper domain when going online
 */

interface CookieInterface
{
    public static function set($name, $value, $expires);

    public static function read($name);

    public static function delete($name);
}

class Cookie
{
    /**
     * Create or update a cookie
     * @param string  $name    Name to give to the cookie
     * @param mixed   $value   What to store in the cookie
     * @param integer $expires In how many seconds should the cookie expires ?
     */
    public static function set($name, $value, $expires)
    {
        setcookie($name, serialize($value), time() + $expires, '/', 'localhost', isset($_SERVER["HTTPS"]), true);
    }

    /**
     * Read the content of a cookie
     * @param  string $name Name of the cookie to retreive
     * @return mixed  The content of the cookie, or false if the cookie does not exist.
     */
    public static function read($name)
    {
        if(!isset($_COOKIE[$name]))
            return false;

        return @unserialize($_COOKIE[$name]);
    }

    /**
     * Remove a cookie
     * @param string $name Name of the cookie to remove
     */
    public static function delete($name)
    {
        unset($_COOKIE[$name]);
        setcookie($name, "", -1, '/', 'localhost', isset($_SERVER["HTTPS"]), true);
    }
}
