<?php

/**
 * isAuthorized - Functions handling authorizations
 */
class isAuthorized
{
    /***** Global verifications *****/
    static public function isUser($userID)
    {
        return (Session::read("userID") === $userID && $userID != null) ? true : false;
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
    static public function editProfile($profileID)
    {
        $userID = Session::read("userID");
        return true;
        return true;
    }

    static public function getProfilePosts()
    {
        return true;
    }
}
