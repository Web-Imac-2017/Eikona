<?php
class Secure
{
    static public function string($string)
    {
        return htmlentities($string);
    }
}

