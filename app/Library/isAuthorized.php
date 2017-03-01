<?php

/**
 * isAuthorized - Functions handling authorizations
 */
class isAuthorized extends DBInterface
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

    static public function isProfile($profileID)
    {
        $dbi = new DBInterface();

        $stmt = $dbi->cnx->prepare("SELECT COUNT(*) FROM profiles WHERE profile_id = :profileID");
        $stmt->execute([":profileID" => Sanitize::int($profileID)]);

        return $stmt->fetchColumn();
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

    static public function editProfile($profileID)
    {
        //Current user owns the profile?
        if(self::ownProfile($profileID))
            return true;

        if(self::isAdmin(Response::read("user", "get")['data']['userAdmin']) == true)
            return true;

        return false;
    }

    static public function seeFullProfile($profileID) //Always true if profile is public, Depends if profile is private.
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
<<<<<<< Updated upstream

    static public function isPrivateProfile($profileID)
    {
        $data = Response::read("profile", "isPrivate", $profileID)['data'];
        if($data['profileIsPrivate'] == 1)
            return true;

        return false;
    }

    static public function getProfilePosts()
    {
        return true;
    }
=======
>>>>>>> Stashed changes
}
