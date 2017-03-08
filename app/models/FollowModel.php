<?php

class FollowModel extends DBInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Tell is the current user, and the profile to follow, are corrects
     * @param  integer        $followed profile to follow
     * @param  integer        $follower profile following
     * @return string|boolean string on failure, true on success
     */
    private function authorizeAction($followed, $follower)
    {
        $currentUser = Session::read("userID");

        if(Sanitize::int($follower) < 1 || !isAuthorized::isProfile($followed))
        {
            return "notAProfile";
        }

        return "ok";
    }

    /**
     * Tell is the follower profile is already following the followed profile
     * @param  integer        $profileID profile followed
     * @param  integer        $profileID profile following
     * @return boolean string on failure, true on success
     */
    public function isFollowing($follower, $followed)
    {
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM followings WHERE followed_id = :followed AND follower_id = :follower");
        $stmt->execute([":followed" => Sanitize::int($followed),
                        ":follower" => Sanitize::int($follower)]);

        return intval($stmt->fetchColumn());
    }
    

    /**
     * Tell is the follower profile is subscribed to the followed profile
     * @param  integer        $profileID profile followed
     * @param  integer        $profileID profile following
     * @return boolean string on failure, true on success
     */
    public function isSubscribed($follower, $followed)
    {
        if(!$this->isFollowing($follower, $followed))
            return 0;
            
        $stmt = $this->cnx->prepare("SELECT follower_subscribed FROM followings WHERE followed_id = :followed AND follower_id = :follower");
        $stmt->execute([":followed" => Sanitize::int($followed),
                        ":follower" => Sanitize::int($follower)]);

        return intval($stmt->fetchColumn());
    }


    /**
     * Tell is the follower profile is subscribed to the followed profile
     * @param  integer        $profileID profile followed
     * @param  integer        $profileID profile following
     * @return boolean string on failure, true on success
     */
    public function isConfirmed($follower, $followed)
    {
        if(!$this->isFollowing($follower, $followed))
            return 0;

        $stmt = $this->cnx->prepare("SELECT follow_confirmed FROM followings WHERE followed_id = :followed AND follower_id = :follower");
        $stmt->execute([":followed" => Sanitize::int($followed),
                        ":follower" => Sanitize::int($follower)]);

        return intval($stmt->fetchColumn());
    }

    /**
     * Follow a profile 
     * @param  integer        $profileID Profile to follow
     * @param  boolean        $subscribe Does the user want to subscribe
     * @return string|boolean true on success, string otherwise
     */
    public function follow($profileID, $subscribe)
    {
        $currentProfile = Session::read("profileID");

        $allow = $this->authorizeAction($profileID, $currentProfile);

        if($allow !== "ok")
        {
            return $allow;
        }

        if($this->isFollowing($currentProfile, $profileID))
        {
            return "alreadyFollowing";
        }

        $confirmed = 1;

        if(isAuthorized::isPrivateProfile($profileID))
            $confirmed = 0;

        $subscribe = Sanitize::booleanToInt($subscribe);

        $stmt = $this->cnx->prepare("INSERT INTO followings(follower_id, followed_id, following_time, follower_subscribed, follow_confirmed) VALUES(:follower, :followed, :time, :subscribe, :confirmed)");
        $stmt->execute([":follower" => $currentProfile,
                        ":followed" => $profileID,
                        ":time" => time(),
                        ":subscribe" => $subscribe,
                        ":confirmed" => $confirmed]);

        return $confirmed;
    }


    /**
     * Unsuscribe from a profile
     * @param  integer        $profileID Profile to unfollow
     * @return string|boolean true on success, a string on failure
     */
    public function unfollow($profileID)
    {
        $currentProfile = Session::read("profileID");

        $allow = $this->authorizeAction($profileID, $currentProfile);

        if($allow !== "ok")
        {
            return $allow;
        }

        if(!$this->isFollowing($currentProfile, $profileID))
        {
            return "alreadyNotFollowing";
        }

        $stmt = $this->cnx->prepare("DELETE FROM followings WHERE follower_id = :follower AND followed_id = :followed");
        $stmt->execute([":follower" => $currentProfile,
                        ":followed" => $profileID]);

        return true;
    }




    /**
     * Return the number of followers of the given account
     * @param  integer $profileID Profile ID
     * @return integer Number of follower
     */
    public function nbrFollowers($profileID)
    {
        $profileID = Sanitize::int($profileID);

        if($profileID < 1)
        {
            return 0;
        }

        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM followings WHERE followed_id = :profileID");
        $stmt->execute([":profileID" => $profileID]);

        return $stmt->fetchColumn();
    }



    /**
     * Number of following a profile has
     * @param  integer $profileID Profile ID
     * @return integer Number of followin gs
     */
    public function nbrFollowings($profileID)
    {
        $profileID = Sanitize::int($profileID);

        if($profileID < 1)
        {
            return 0;
        }

        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM followings WHERE follower_id = :profileID");
        $stmt->execute([":profileID" => $profileID]);

        return $stmt->fetchColumn();
    }




    /**
     * Update subscription to receive notifications
     * @param  integer $profileID
     * @return boolean success or failure
     */
    public function subscribe($profileID)
    {
        return $this->updateSubscription($profileID, 1);
    }


    /**
     * Update subscription with given setting
     * @param  integer $profileID
     * @return boolean success or failure
     */
    public function unsubscribe($profileID)
    {
        return $this->updateSubscription($profileID, 0);
    }


    /**
     * Update subscription with given setting
     * @param  integer $profileID
     * @return boolean success or failure
     */
    private function updateSubscription($profileID, $newValue)
    {
        $newValue = Sanitize::int($newValue);
        $followed = Sanitize::int($profileID);
        $follower = Session::read("profileID");

        if(!$this->isFollowing($follower, $followed))
        {
            return false;
        }

        $stmt = $this->cnx->prepare("UPDATE followings SET follower_subscribed = :newValue WHERE follower_id = :follower AND followed_id = :followed");
        $stmt->execute([":newValue" => $newValue,
                        ":follower" => $follower,
                        ":followed" => $followed]);

        return true;
    }


    /**
     * Tell is the follower profile is subscribed to the followed profile
     * @param  integer        $followed profile followed
     * @param  integer        $follower profile following
     * @return boolean string on failure, true on success
     */
    public function confirmFollow($follower, $followed)
    {
        $follower = Sanitize::int($follower);
        $followed = Sanitize::int($followed);

        $allow = $this->authorizeAction($followed, $follower);

        if($allow !== "ok")
        {
            return $allow;
        }

        if(!$this->isFollowing($follower, $followed))
        {
            return false;
        }

        $stmt = $this->cnx->prepare("UPDATE followings SET follower_confirmed = 1 WHERE follower_id = :follower AND followed_id = :followed");
        $stmt->execute([":follower" => $follower,
                        ":followed" => $followed]);

        return true;
    }
}

