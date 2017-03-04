<?php

class ProfileController
{
    private $model;
    private $postModel;
    private $followModel;

    /**
     * Init the constructor and link the model
     * @private
     */
    public function __construct()
    {
        $this->model = new ProfileModel();
        $this->postModel = new PostModel();
        $this->followModel = new FollowModel();
    }

    /**
     * Create a new profile for the current user
     *
     * @return ID of the newly created profile, 0 if it fails.
     */
    public function create()
    {
        $rsp = new Response();

        /**
         * TODO: Verify if user is connected and can add a new profile
         */

        if (empty($_POST['profileName']))
        {
            $rsp->setFailure(400);
            $rsp->send();
            return;
        }
        $name = $_POST['profileName'];
        $desc = isset($_POST['profileDesc']) ? $_POST['profileDesc'] : "";
        $isPrivate = isset($_POST['profilePrivate']) ? true : false;

        $uID = Session::read("userID"); //Get current user ID

        $result = $this->model->create($uID, $name, $desc, $isPrivate);

        $rsp = new Response();

        if($result == "badUserID")
        {
            $rsp->setFailure(400, "Given user ID is not valid.");
        }
        else if($result == "userNameAlreadyExists")
        {
            $rsp->setFailure(409, "The profile name is already taken.");
        }
        else
        {
            $rsp->setSuccess(201, "profile created")
                ->bindValue("profileID", $result);
        }

        /**
         * Handle profile picture
         */

        //Send JSON response
        $rsp->send();
    }

    /**
     * Set the profile to use with the model
     * @param  integer $profileID Profile ID to use with the model
     * @return boolean  true on success, false on failure
     */
    private function setProfile($profileID)
    {
        $result = $this->model->setProfile($profileID);

        if($result != "success")
        {
            $rsp = new Response();

            if($result == "wrongFormat")
            {
                $rsp->setFailure(400, "Wrong format. This is not a profile ID.");
            }
            else if($result == "notFound")
            {
                $rsp->setFailure(404, "Given profile ID does not exist.");
            }

            $rsp->send();

            return false;
        }

        return true;
    }

    /**
     * Set the profile to use with the model
     * @param  integer $profileID Profile ID to use with the model
     * @return boolean  true on success, false on failure
     */
    public function setCurrent($profileID)
    {
        $result = $this->model->setProfile($profileID);

        $userID = Session::read("userID");

        $rsp = new Response();

        if(!$userID)
        {
            $rsp->setFailure(400, "You must be connected to do this action.")
                ->send();

            return;
        }

        if(!isAuthorized::ownProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to use this profile.")
                ->send();

            return;
        }

        Session::write("profileID", $profileID);

        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->send();
    }


    /**
     * Return the description of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function get($profileID)
    {
        if(!$this->setProfile($profileID))
            return;

        $profileInfos = $this->model->getFullProfile();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("ownerID", $profileInfos['user_id'])
            ->bindValue("profileName", $profileInfos['profile_name'])
            ->bindValue("profileDesc", $profileInfos['profile_desc'])
            ->bindValue("profilePict", Response::read("Profile", "picture", $profileID)['data']['profilePicture'])
            ->bindValue("profileCreateTime", $profileInfos['profile_create_time'])
            ->bindValue("profileViews", $profileInfos['profile_views'])
            ->bindValue("profileIsPrivate", $profileInfos['profile_views'] == 1)
            ->bindValue("nbrPosts", Response::read("Profile", "nbrPosts", $profileID)['data']['nbrPosts'])
            ->bindValue("nbrFollowers", Response::read("Profile", "nbrFollowers", $profileID)['data']['nbrFollowers'])
            ->bindValue("nbrFollowings", Response::read("Profile", "nbrFollowings", $profileID)['data']['nbrFollowings'])
            ->send();
    }


    /**
     * Return the description of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function name($profileID)
    {
        if(!$this->setProfile($profileID))
            return;

        $name = $this->model->getName();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profileName", $name)
            ->send();
    }

    /**
     * Return the description of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function description($profileID)
    {
        if(!$this->setProfile($profileID))
            return;

        $desc = $this->model->getDesc();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profiledesc", $desc)
            ->send();
    }

    /**
     * Return the link to the profile picture of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function picture($profileID)
    {
        $profileID = Sanitize::int($profileID);

        if(file_exists("/app/medias/profilesPictures/".$profileID.".jpg"))
        {
            $pic = "/app/medias/profilesPictures/".$profileID.".jpg";
        }
        else
        {
            $pic = "/app/medias/profilesPictures/default.jpg";
        }

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profilePicture", $pic)
            ->send();
    }

    /**
     * Return the number of views of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function views($profileID)
    {
        if(!$this->setProfile($profileID))
            return;

        $views = $this->model->getViews();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profileViews", $views)
            ->send();
    }

    /**
     * Return the number of views of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function isPrivate($profileID)
    {
        if(!$this->setProfile($profileID))
            return;

        $isPrivate = $this->model->isPrivate();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profileIsPrivate", $isPrivate)
            ->send();
    }

    /**
     * Return the number of views of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function owner($profileID)
    {
        if(!$this->setProfile($profileID))
            return;

        /**
         * TODO: confirm current user is moderator or profile owner
         */

        $owner = $this->model->getOwner();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profileOwner", $owner)
            ->send();
    }


    /**
     * Return the number of posts from the profile
     * @param integer $profileID ID of the profile
     */
    public function nbrPosts($profileID)
    {
        $rsp = new Response();

        if(!isAuthorized::seeFullProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action")
                ->send();

            return;
        }

        $nbrPosts = $this->postModel->nbrPosts($profileID);

        $rsp->setSuccess(200)
            ->bindValue("nbrPosts", $nbrPosts)
            ->send();
    }


    /**
     * Return the posts of the specified profile
     * @param  integer $profileID ID of the profile
     * @param  integer $limit     Number of posts to return
     * @return void
     */
    public function posts($profileID, ...$args)
    {
        $rsp = new Response();

        if(!isAuthorized::seeFullProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action")
                ->send();

            return;
        }

        $limit = 4096;
        $offset = 0;
        $after = 0;
        $before = 0;
        $order = "DESC";

        $waitFor = false;

        foreach($args as $arg)
        {
            if($arg === "desc" || $arg === "asc")
            {
                $order = strtoupper($arg);
                continue;
            }

            if(is_numeric($arg))
            {
                if($waitFor == "after")
                {
                    $after = Sanitize::int($arg);
                }
                else if($waitFor == "before")
                {
                    $before = Sanitize::int($arg);
                }
                else if($limit == 4096)
                {
                    $limit = Sanitize::int($arg);
                }
                else
                {
                    $offset = Sanitize::int($arg);
                }

                $waitFor = false;
                continue;
            }

            $waitFor = $arg;
        }

        $posts = $this->postModel->getPosts($profileID, $limit, $offset, $after, $before, $order);

        $rsp->setSuccess(200)
            ->bindValue("posts", $posts)
            ->bindValue("nbrPosts", count($posts))
            ->send();
    }

    // /profile/posts/<profileid>[/after/<timestamp>][/before/<timestamp>][/<lim>[/<offset>]][<desc|asc>]



    /**
     * Update the specified element of the profile
     *
     * @param $field Field to be updated
     * @param $profileID ID of the profile
     */
    public function update($field, $profileID)
    {
        //Init JSON Response
        $rsp = new Response();


        //Exclude all failure possibilities
        if(!isAuthorized::editProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!$this->setProfile($profileID))
        {
            return;
        }

        //Now, do the update
        switch($field)
        {
            case "name":

                if(!isset($_POST['newValue']))
                {
                    $rsp->setFailure(400, "Missing newValue POST variable. Update aborted.");
                    $rsp->send();
                    return;
                }

                if($this->model->updateName($_POST['newValue']))
                {
                    $rsp->setSuccess(200)
                        ->bindValue("profileName", $this->model->getName());
                }

            break;
            case "description":

                if(!isset($_POST['newValue']))
                {
                    $rsp->setFailure(400, "Missing newValue POST variable. Update aborted.");
                    $rsp->send();
                    return;
                }

                if($this->model->updateDesc($_POST['newValue']))
                {
                    $rsp->setSuccess(200)
                        ->bindValue("profileDesc", $this->model->getDesc());
                }

            break;
            case "setPrivate":

                if($this->model->setPrivate())
                {
                    $rsp->setSuccess(200)
                        ->bindValue("profileIsPrivate", 1);
                }

            break;
            case "setPublic":

                if($this->model->setPublic())
                {
                    $rsp->setSuccess(200)
                        ->bindValue("profileIsPrivate", 0);
                }

            break;
            default;
                $rsp->setFailure(405);
        }

        //Send JSON response
        $rsp->bindValue("profileID", $profileID)
            ->send();

    }


    /**
     * Set the pricture for the given picture
     * @param integer $profileID Profile to change the picture
     */
    public function setPicture($profileID)
    {
        $rsp = new Response();

        if(!isAuthorized::editProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!is_uploaded_file($_FILES['profilePicture']['tmp_name']))
		{
            $rsp->setFailure(400, "Missing profilePicture file")
                ->send();

            return;
        }

        $source = $_FILES['profilePicture']['tmp_name'];
        $format = getimagesize($source);
        $tab;

        if(preg_match('#(png|gif|jpeg)$#i', $format['mime'], $tab))
        {
            $imSource = imagecreatefromjpeg($source);
            if($tab[1] == "jpeg")
                $tab[1] = "jpg";
            $extension = $tab[1];
        }
        else
        {
            $rsp->setFailure(406, "Picture format (".$tab.") is not supported.")
                ->send();

            return;
        }

        if($format['mime'] == "image/png")
        {
            $extension = 'jpg';
        }

        /*enregistrement de l'image*/
        imagejpeg($imSource, 'medias/profilesPictures/' . $profileID . '.' . $extension);

        $rsp->setSuccess(200)
            ->bindValue("ProfilePicture", "/app/medias/profilesPictures/".$profileID.".jpg'")
            ->send();
    }



    /**
     * Increment by one (or more) the view counter of the specified profile
     *
     * @param $porfileID ID of the profile
     * @param $nbr Number of view to add.
     */
    public function addView($profileID, $nbr = 1)
    {
        if(!$this->setProfile($profileID))
            return;

        //Init JSON Response
        $rsp = new Response();
        $rsp->bindValue("profileID", $profileID);

        if($this->model->addView($nbr))
        {
            $rsp->setSuccess(200)
                ->bindValue("profileViews", $this->model->getViews());
        }
        else
        {
            $rsp->setFailure(400);
        }

        $rsp->send();
    }

    /**
     * Delete the specified profile and all its dependecies
     *
     * @param $profileToDelete
     * @return void
     */
    public function delete($profileID)
    {
        if(!isAuthorized::editProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        /*
         * TODO: Remove dependants data like posts, comments, likes, etc...
         */

        if(!$this->setProfile($profileID))
            return;

        $this->model->delete($profileID);

        if(file_exists("PATH/TO/PROFILE/PICTURE/".$profileID.".jpg"))
        {
            unlink("PATH/TO/PROFILE/PICTURE/".$profileID.".jpg");
        }

        $rsp = new Response();
        $rsp->setSuccess(200)
            ->send();
    }



    /************* FOLLOW *************/

    /**
     * Return the number of followers of the asked profile
     * @param integer $profileID Profile to get the followers
     */
    public function nbrFollowers($profileID)
    {
        $rsp = new Response();

        $profileID = Sanitize::int($profileID);

        if($profileID < 1)
        {
            $rsp->setFailure(400, "The given parameter is not a profile ID");
        }

        $nbrFollower = $this->followModel->nbrFollowers($profileID);

        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("nbrFollowers", $nbrFollower)
            ->send();
    }


    /**
     * Return the number of profile the given profile if following
     * @param integer $profileID profile ID
     */
    public function nbrFollowings($profileID)
    {
        $rsp = new Response();

        $profileID = Sanitize::int($profileID);

        if($profileID < 1)
        {
            $rsp->setFailure(400, "The given parameter is not a profile ID");
        }

        $nbrFollowing = $this->followModel->nbrFollowings($profileID);

        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("nbrFollowings", $nbrFollowing)
            ->send();
    }


    /**
     * Follow the given profile with the current user
     * @param integer $profileID       Profile to follow
     * @param boolean [$subscribe      = 0] Shall we also subscribe?
     */
    public function follow($profileID, $subscribe = 0)
    {
        $rsp = new Response();

        $currentUser = Session::read("profileID");

        if(!isAuthorized::editProfile($currentUser))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!$currentUser)
        {
            echo "Hi mom!";
            $rsp->setFailure(401, "You must be connected to do this.")
                ->send();

            return;
        }

        if($profileID === $currentUser)
        {
            $rsp->setFailure(401, "You cannot follow yourself.")
                ->send();

            return;
        }

        //Do the request
        $result = $this->followModel->follow($profileID, $subscribe);

        if($result === "notAProfile")
        {
            $rsp->setFailure(400, "The given parameter is not a valid profile ID.")
                ->send();

            return;
        }

        if($result === "alreadyFollowing")
        {
            $rsp->setFailure(409, "You are already following this profile.")
                ->send();

            return;
        }

        $rsp->setSuccess(200)
            ->send();
    }



    /**
     * Unfollow the given profile with the current profile
     * @param integer $profileID profile to unfollow
     */
    public function unfollow($profileID)
    {
        $rsp = new Response();

        $currentUser = Session::read("profileID");

        if(!isAuthorized::editProfile($currentUser))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!$currentUser)
        {
            echo "Hi mom!";
            $rsp->setFailure(401, "You must be connected to do this.")
                ->send();

            return;
        }

        if($profileID === $currentUser)
        {
            $rsp->setFailure(401, "You cannot unfollow yourself.")
                ->send();

            return;
        }

        $result = $this->followModel->unfollow($profileID);

        if($result === "notAProfile")
        {
            $rsp->setFailure(400, "The given parameter is not a valid profile ID.")
                ->send();

            return;
        }

        if($result === "alreadyNotFollowing")
        {
            $rsp->setFailure(409, "You are already following this profile.")
                ->send();

            return;
        }

        $rsp->setSuccess(200)
            ->send();
    }


    /**
     * Return the list of followers of the given profile
     * @param integer $profileID ProfileID
     */
    public function followers($profileID)
    {
        $rsp = new Response();

        if(!isAuthorized::seeFullProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action")
                ->send();

            return;
        }

        $followers = $this->model->getFollowers($profileID);

        $rsp->setSuccess(200)
            ->bindValue("profileID", Sanitize::int($profileID))
            ->bindValue("followers", $followers)
            ->bindValue("nbrFollowers", count($followers))
            ->send();
    }

    /**
     * Return the followings of the given profile
     * @param integer $profileID profile ID
     */
    public function followings($profileID)
    {
        $rsp = new Response();

        if(!isAuthorized::seeFullProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action")
                ->send();

            return;
        }

        $followings = $this->model->getFollowings($profileID);

        $rsp->setSuccess(200)
            ->bindValue("profileID", Sanitize::int($profileID))
            ->bindValue("followings", $followings)
            ->bindValue("nbrFollowings", count($followings))
            ->send();
    }



    /**
     * Update subscription with given setting
     * @param  integer $profileID
     * @return boolean success or failure
     */
    public function subscribe($profileID)
    {
        $rsp = new Response();

        $currentUser = Session::read("profileID");

        if(!isAuthorized::editProfile($currentUser))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!$currentUser)
        {
            echo "Hi mom!";
            $rsp->setFailure(401, "You must be connected to do this.")
                ->send();

            return;
        }

        if(!$this->followModel->subscribe($profileID))
        {
            $rsp->setFailure(400, "You cannot subscribed to a profile you are not following.")
                ->send();

            return;
        }

        $rsp->setSuccess(200)
            ->send();
    }



    /**
     * Update subscription with given setting
     * @param  integer $profileID
     * @return boolean success or failure
     */
    public function unsubscribe($profileID)
    {
        $rsp = new Response();

        $currentUser = Session::read("profileID");

        if(!isAuthorized::editProfile($currentUser))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!$currentUser)
        {
            echo "Hi mom!";
            $rsp->setFailure(401, "You must be connected to do this.")
                ->send();

            return;
        }

        if(!$this->followModel->unsubscribe($profileID))
        {
            $rsp->setFailure(400, "You cannot unsubscribed from a profile you are not following.")
                ->send();

            return;
        }

        $rsp->setSuccess(200)
            ->send();
    }

    /**
     * Tell if the follower is following the followed
     * @param integer $follower Follower ID
     * @param integer $followed ID of profile followed
     */
    public function isFollowing($followed, $follower = -1)
    {
        $follower = $follower == -1 ? Session::read("profileID") : $follower;

        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("isFollowing", $this->followModel->isFollowing($follower, $followed))
            ->bindValue("isSubscribed", $this->followModel->isSubscribed($follower, $followed))
            ->bindValue("isConfirmed", $this->followModel->isConfirmed($follower, $followed))
            ->send();
    }

    /**
     * Confirm the follow request
     * @param integer $follower Follower ID
     * @param integer $followed ID of profile followed
     */
    public function confirmFollow($follower, $followed = -1)
    {
        $follower = $follower == -1 ? Session::read("profileID") : $follower;
        $rsp = new Response();

        if($this->followModel->confirmFollow($follower, $followed))
        {
            $rsp->setSuccess(200)
                ->send;

            return;
        }

        $rsp->setFailure(400, "This following does not exist")
            ->send();
    }
}
?>
