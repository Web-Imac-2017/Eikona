<?php

/**
 * isAuthorized - Functions handling authorizations
 */
class isAuthorized
{
    /***** Global verifications *****/

    //Confirm the given user is a real user, or tell if we are currently connected if the parameter is ommited
    static public function isUser($userID = null)
    {
        $checkUser = 0;

        if($user === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($userID);
        }

        return Response::read("user", "exists", $checkWith);
    }
    
    //Tell if the current user, or the given one, is a moderator
    static public function isModerator($userID = null)
    {
        $checkUser = 0;

        if($user === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($userID);
        }

        return Response::read("user", "isModerator", $checkWith);
    }

    //Tell if the current user, or the given one, is an admin
    static public function isAdmin($userID = null)
    {
        $checkUser = 0;

        if($user === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($userID);
        }

        return Response::read("user", "isAdmin", $checkWith);
    }

    //Confirm the given profile is a real profile, or tell if we have a current profile if the parameter is ommited
    static public function isProfile($profileID)
    {
        $checkUser = 0;

        if($user === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($userID);
        }

        return Response::read("profile", "exists", $checkWith);
    }








    /***** Profiles verifications *****/

    //Confirm is current user owns the given profile
    static public function ownProfile($profileID)
    {
        $userID = Session::read("userID");

        $userProfiles = Response::read("user", "profiles")['data'];

        if(empty($userProfiles))
            return false;

        if($userProfiles["nbOfProfiles"] == 0)
            return false;

        //prevent error until user->profiles gets updated
        if(empty($userProfiles["profiles"]))
            return false;

        foreach ($userProfiles["profiles"] as $profile)
        {
            if (isset($profile["user_id"]) && $profile["profile_id"] == $profileID)
                return true;
        }

        return false;
    }

    //Confirm the current user can edit the given profile
    static public function editProfile($profileID)
    {
        //Current user owns the profile?
        if(self::ownProfile($profileID))
            return true;

        if(self::isAdmin(Response::read("user", "get")['data']['userAdmin']) == true)
            return true;

        return false;
    }

    //TODO
    //Confirm the current user can view fully the given profile
    static public function seeFullProfile($profileID)
    {
        return true;
    }







    /***** Posts verifications *****/


    static public function editPost($postID)
    {
        $profileID = Session::read("profileID");
        //profile_id du post = profileID
        $data = Response::read("post", "display", $postID)['data'];

        if($data['profileID'] == $profileID)
            return true;

        return false;
    }

    static public function isPrivateProfile($profileID)
    {
        $data = Response::read("profile", "isPrivate", $profileID)['data'];
        if($data['profileIsPrivate'] == 1)
            return true;

        return false;
    }
}
