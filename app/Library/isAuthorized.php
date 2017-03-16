<?php

/**
 * isAuthorized - Methods handling authorizations
 */
class isAuthorized
{
    /***** Global verifications *****/

    /**
     * Confirm the given user is a real user, or tell if we are currently connected if the parameter is ommited
     * @param  integer $userID User ID to check with. Current user used if empty
     * @return boolean True if user exists, false otherwise
     */
    static public function isUser($userID = null)
    {
        if($userID === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($userID);
        }

        return Response::read("user", "exists", $checkWith)["data"]["exists"];
    }
    
    /**
     * Tell if the current user, or the given one, is a moderator
     * @param  integer $userID User ID to check with. Current user used if empty
     * @return boolean True if user is a moderator, false otherwise
     */
    static public function isModerator($userID = null)
    {
        if($userID === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($userID);
        }

        return Response::read("user", "isModerator", $checkWith)["data"]["isModerator"];
    }

    /**
     * Tell if the current user, or the given one, is an admin
     * @param  integer $userID User ID to check with. Current user used if empty
     * @return boolean True if user is an admin, false otherwise
     */
    static public function isAdmin($userID = null)
    {
        if($userID === null)
        {
            $checkWith = Session::read("userID");
        }
        else
        {
            $checkWith = Sanitize::int($userID);
        }

        return Response::read("user", "isAdmin", $checkWith)["data"]["isAdmin"];
    }

    /**
     * Confirm the given profile is a real profile, or tell if we have a current profile if the parameter is ommited
     * @param  integer $profileID Profile ID to check with
     * @return boolean True if profile is legit, false otherwise
     */
    static public function isProfile($profileID)
    {
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

        foreach ($userProfiles["profiles"] as $profile)
        {
            if ($profile["profileID"] == $profileID)
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
        $data = Response::read("post", "display", $postID, false)['data'];
        if(!empty($data))
            return true;

        return false;
    }

}
