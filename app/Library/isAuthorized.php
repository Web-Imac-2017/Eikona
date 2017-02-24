<?php

/**
 * isAuthorized - Functions handling authorizations
 */
class isAuthorized
{
    /***** Global verifications *****/
    static public function isUser($userID)
    {
        return (Session::read("userID") === $userID) ? true : false;
    }
    
    static public function isModerator($userModerator)
    {   
        return ($userModerator == true) ? true : false;
    }

    static public function isAdmin($userAdmin)
    {
        return ($userAdmin == true) ? true : false;
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
