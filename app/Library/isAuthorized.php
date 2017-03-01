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

    /*
        Retourne si on peut éditer le profil
        On vérifie si le profile appartient bien à l'user actif 
        ou bien à un admin
     */
    static public function editProfile($profileID)
    {
        //Current user owns the profile?
        if(self::ownProfile($profileID))
            return true;

        if(self::isAdmin(Response::read("user", "get")['data']['userAdmin']) == true)
            return true;

        return false;
    }

    /*
    Retourne si on peut éditer le post
    On vérifie si le post appartient bien au profil actif
     */
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
        if($data['profileIsPrivate'] == 1)
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

    static public function getProfilePosts()
    {
        return true;
    }
}
