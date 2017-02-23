<?php

/**
 * isAuthorized - Functions handling authorizations
 */
class isAuthorized
{
    /***** Global verifications *****/
    static public function isUser($userID)
    {
        if(Session::read("userID") === $userID) return true;

        return false;
    }
    
    static public function isModerator()
    {

    }

    static public function isAdmin($userAdmin)
    {
        if($userAdmin == true) return true;

        return false;
    }

    /***** Profiles verifications *****/
    static public function updateProfile()
    {
        return true;
    }

    static public function deleteProfile()
    {
        return true;
    }

    static public function getProfilePosts()
    {
        return true;
    }
}
