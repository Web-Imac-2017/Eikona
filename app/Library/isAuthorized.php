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

        if($userID === null)
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

        if($userID === null)
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

        if($userID === null)
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

        if($profileID === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($profileID);
        }

        return Response::read("profile", "exists", $checkWith);
    }

    /***** Profiles verifications *****/

    //Confirm is current user owns the given profile
    static public function ownProfile($profileID)
    {
        if(!self::isUser())
            return false;
        
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

    static public function editProfile($profileID)
    {
        //Current user owns the profile?
        if(self::ownProfile($profileID))
            return true;

        if(self::isAdmin() == true)
            return true;

        return false;
    }


    /*
    Retourne si on peut éditer le post
    On vérifie si le post appartient bien au profil actif
     */

    //TODO
    //Confirm the current profile can view fully the given profile
    static public function seeFullProfile($profileID)
    {
        if(Session::read("profileID") == $profileID)
        {
            return true;
        }

        if(self::isPrivateProfile($profileID))
        {
            if(Response::read("profile", "isFollowing", $profileID)['data']['isConfirmed'] === 1)
            {
                return true;
            }

            return false;
        }

        return true;
    }



    /***** Posts verifications *****/

    static public function editPost($postID)
    {
        $profileID = Session::read("profileID");
        //profile_id du post = profileID
        $data = Response::read("post", "display", $postID)['data'];

        if(!empty($data) && $data['profileID'] == $profileID)
            return true;

        return false;
    }

    /*
    Retourne si le profil est privé
     */
    static public function isPrivateProfile($profileID)
    {
        $data = Response::read("profile", "isPrivate", $profileID)['data'];

        if(isset($data['profileIsPrivate']) && $data['profileIsPrivate'] == 1)
            return true;

        return false;
    }

    /*
    On vérifie si le post existe bien
     */
    static public function isPost($postID)
    {
        $data = Response::read("post", "display", $postID)['data'];
        if(!empty($data))
            return true;

        return false;
    }

}
