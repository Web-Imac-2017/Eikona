<?php
/**
 * Debug Controller used for debugging purpose
 * */

class DebugController
{
    public function printSession()
    {
        header("Content-Type: application/json");
        print_r(json_encode($_SESSION));
    }
}
