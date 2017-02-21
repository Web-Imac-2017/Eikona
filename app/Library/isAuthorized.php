<?php

/**
 * isAuthorized - Functions handling authorizations
 */
class isAuthorized
{
    /***** Global verifications *****/
    static public function isUser()
    {
        return true;
    }
    static public function isModerator() {}
    static public function isAdmin() {}

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
