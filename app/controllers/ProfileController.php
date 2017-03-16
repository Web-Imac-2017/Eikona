<?php

interface ProfileControllerInterface
{
    public function create();
    
    public function exists($profileID);
    
    public function setCurrent($profileID);
    
    public function get($profileID);
    
    public function name($profileID);
    
    public function description($profileID);
    
    public function picture($profileID);
    
    public function views($profileID);
    
    public function isPrivate($profileID);
    
    public function owner($profileID);
    
    public function nbrPosts($profileID);
    
    public function posts($profileID, ...$args);

    public function drafts();
    
    public function update($field, $profileID);
    
    public function setPicture($profileID);
    
    public function addView($profileID, $nbr = 1);
    
    public function delete($profileID);
    
    public function nbrFollowers($profileID);
    
    public function nbrFollowings($profileID);
    
    public function follow($profileID, $subscribe = 0);
    
    public function unfollow($profileID);
    
    public function followers($profileID);
    
    public function followings($profileID);
    
    public function subscribe($profileID);
    
    public function unsubscribe($profileID);
    
    public function isFollowing($followed, $follower = -1);
    
    public function confirmFollow($follower);

    public function notifications();

    public function feed($limit = 30, $before = 0);
}

class ProfileController implements ProfileControllerInterface
{
    private $model;
    private $postModel;
    private $followModel;
    private $notifModel;

    /**
     * Init the constructor and link the model
     * @private
     */
    public function __construct()
    {
        $this->model       = new ProfileModel();
        $this->postModel   = new PostModel();
        $this->followModel = new FollowModel();
        $this->notifModel  = new NotificationModel();
    }

    /**
     * Create a new profile for the current user
     *
     * @return ID of the newly created profile, 0 if it fails.
     */
    public function create()
    {
        $rsp = new Response();

        $uID = Session::read("userID"); //Get current user ID
        
        //Are we logged in
        if(!isAuthorized::isUser())
        {
            $rsp->setFailure(401, "You must be connected to do this")
                ->send();
            
            return;
        }
        
        //Have we reached the profile number limit
        if($this->model->tooMuchProfiles($uID))
        {
            $rsp->setFailure(400, "Too Much profiles")
                ->send();
            
            return; 
        }

        //Do we have all we need
        if (empty($_POST['profileName']))
        {
            $rsp->setFailure(400);
            $rsp->send();
            return;
        }

        $name = $_POST['profileName'];
        $desc = isset($_POST['profileDesc']) ? $_POST['profileDesc'] : "";
        $isPrivate = isset($_POST['profilePrivate']) ? true : false;

        $result = $this->model->create($uID, $name, $desc, $isPrivate);

        $rsp = new Response();

        //Handle insert errors
        if($result == "badUserID")
        {
            $rsp->setFailure(400, "Given user ID is not valid.")
                ->send();

            return;
        }

        if($result == "userNameAlreadyExists")
        {
            $rsp->setFailure(409, "The profile name is already taken.")
                ->send();

            return;
        }

        $rsp->setSuccess(201, "profile created")
            ->bindValue("profileID", $result)
            ->send();

        //Create profile folder
        $profileKey = $this->model->getKey();

        $root = $_SERVER['DOCUMENT_ROOT']."/Eikona/app/medias/img/";
        mkdir($root."/".$profileKey);
    }

    /**
     * Set the profile to use with the model
     * @param  integer $profileID Profile ID to use with the model
     * @return boolean  true on success, false on failure
     */
    private function setProfile($profileID)
    {
        $result = $this->model->setProfile($profileID);
        
        if($result == "success")
        {
            return true;
        }
        
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





    /**
     * Tell if the specified profile exists or not
     * @param integer $userID User ID to verify
     */
    public function exists($profileID)
    {
        $rsp = new Response();

        $rsp->setSuccess(200)
            ->bindValue("exists", $this->model->exists($profileID))
            ->send();
    }

    /**
     * Set the profile to use with the model
     * @param  integer $profileID Profile ID to use with the model
     * @return boolean  true on success, false on failure
     */
    public function setCurrent($profileID)
    {
        if(!$this->model->setProfile($profileID))
           return;
           
        $userID = Session::read("userID");

        $rsp = new Response();
        
        //Are we logged in ?
        if(!$userID)
        {
            $rsp->setFailure(400, "You must be connected to do this action.")
                ->send();

            return;
        }
        
        //Do we own this profile ?
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
     * @param $profileID ID of the profile
     */
    public function picture($profileID)
    {
        if(!$this->setProfile($profileID))
            return;

        $pic = $this->model->getPict();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profilePicture", $pic)
            ->send();
    }

    /**
     * Return the number of views of the specified profile
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
            $rsp->setFailure(401, "You cannot see this profile.")
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

        $postsID = $this->postModel->getPosts($profileID, $limit, $offset, $after, $before, $order);

        $posts = array();

        foreach($postsID as $postID)
        {
            array_push($posts, Response::read("post", "display", $postID)["data"]);
        }

        $rsp->setSuccess(200)
            ->bindValue("posts", $posts)
            ->bindValue("nbrPosts", count($posts))
            ->send();
    }



    /**
     * Get all drafts for the current profile
     */
    public function drafts()
    {
        $rsp = new Response();

        $profileID = Session::read("profileID");

        if(!isAuthorized::editProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to access .")
                ->send();

            return;
        }

        //ghet all drafts
        $postsID = $this->postModel->getDraftsID($profileID);

        $posts = array();

        foreach($postsID as $postID)
        {
            array_push($posts, Response::read("post", "display", $postID)["data"]);
        }

        $rsp->setSuccess(200)
            ->bindValue("posts", $posts)
            ->bindValue("nbrPosts", count($posts))
            ->send();
    }



    /**
     * Update the specified element of the profile
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

        //Can we edit this profile
        if(!isAuthorized::editProfile($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }
        
        //Do we have all we need
        if(!is_uploaded_file($_FILES['profilePicture']['tmp_name']))
		{
            $rsp->setFailure(400, "Missing profilePicture file")
                ->send();

            return;
        }

        /*enregistrement de l'image*/
        saveTo($_FILES['profilePicture']['tmp_name'], 'medias/profilesPictures/'.$profileID.'.jpg');

        //Update DB
        $this->model->updatePict($newPictName);

        $rsp->setSuccess(200)
            ->bindValue("ProfilePicture", "/app/medias/profilesPictures/".$newPictName)
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
            $rsp->setFailure(400)
                ->send();
            
            return;
        }
        
        $rsp->setSuccess(200)
            ->bindValue("profileViews", $this->model->getViews())
            ->send();

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
            $rsp->setFailure(400, "The given parameter is not a profile ID")
                ->send();
            
            return;
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
            $rsp->setFailure(400, "The given parameter is not a profile ID")
                ->send();
            
            return;
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
        
        //Are we logged in ?
        if(!$currentUser)
        {
            $rsp->setFailure(401, "You must be connected to do this.")
                ->send();

            return;
        }
        
        //Can we use this profile ?
        if(!isAuthorized::editProfile($currentUser))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }
        
        //Are we trying to follow ourself ?
        if($profileID === $currentUser)
        {
            $rsp->setFailure(401, "You cannot follow yourself.")
                ->send();

            return;
        }

        //Do the request
        $result = $this->followModel->follow($profileID, $subscribe);
        
        //Handle errors
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

        //If we are following a private account
        if($result === 0)
        {
            $code = "newFollowAsk";
        }

        //If we are following a public profile
        if($result === 1){
            $code = "newFollowing";
        }

        $notif = Response::read("notification", "create", $code, $currentUser, $profileID, $profileID);

        if($notif['code'] != 200)
        {
            $rsp->setFailure(400, "error during following")
                ->send();
            
            return;
        }

        $rsp->setSuccess(200, "follow and notification sent")
            ->bindValue("userProfile", $currentUser)
            ->bindValue("profileFollowed", $profileID)
            ->bindValue("notif", $notif['data'])
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
        
        //Do we have a current profile
        if(!$currentUser)
        {
            $rsp->setFailure(401, "You must be connected to do this.")
                ->send();

            return;
        }
        
        //Can we use this profile ?
        if(!isAuthorized::editProfile($currentUser))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        //Are we trying to unfollow ourselves?
        if($profileID === $currentUser)
        {
            $rsp->setFailure(401, "You cannot unfollow yourself.")
                ->send();

            return;
        }

        $result = $this->followModel->unfollow($profileID);
        
        //Handle errors
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
            $rsp->setFailure(401, "You cannot see this profile.")
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
            $rsp->setFailure(401, "You cannot see this profile.")
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
    public function confirmFollow($follower)
    {
        $followed = Session::read("profileID");
        $rsp = new Response();
        
        //Are we logged in ?
        if(!$followed)
        {
            $rsp->setFailure(401, "You must be connected to do this.")
                ->send();

            return;
        }
        
        //Can we edit current profile
        if(!isAuthorized::editProfile($followed))
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }
        
        //Are we trying to confirm ourself?
        if($followed === $follower)
        {
            $rsp->setFailure(401, "You cannot do this.")
                ->send();

            return;
        }

        $result = $this->followModel->confirmFollow($follower, $followed);

        //Handle errors
        if($result === "notAProfile")
        {
            $rsp->setFailure(400, "The given parameter is not a valid profile ID.")
                ->send();

            return;
        }

        if($result === false)
        {

            $rsp->setFailure(400, "This following does not exist")
                ->send();

            return;
        }

        $notif = Response::read("notification", "create", "followAccepted", $followed, $follower, $followed);

        if($notif['code'] != 200){
            $rsp->setFailure(400, "followed not confirmed")
                ->send();
            return;
        }

        $rsp->setSuccess(200, "follower confirmed")
            ->bindValue("follower", $follower)
            ->bindValue("followed", $followed)
            ->bindValue("notif", $notif['data'])
            ->send();
    }

    public function notifications()
    {
        $profileID = Session::read("profileID");

        $rsp = new Response();

        if(!$profileID)
        {
            $rsp->setFailure(401, "You must have profile to do this.")
                ->send();
            
            return;
        }

        $notif = $this->notifModel->getProfileNotifications($profileID);
        
        if($notif == null)
        {
            $rsp->setFailure(404, "You do not have notifications.")
                ->send();
            
            return;
        }

        $rsp->setSuccess(200, "notifications returned")
            ->bindValue("profileID", $profileID)
            ->bindValue("notif", $notif)
            ->send();
   }

    /********* FEED ***********/
    
    /**
     * Return the feed of the user
     * @param intefer [$limit       = 30] Number of activities to return
     * @param integer [$before      = 0] Retrive activity up to
     */
    public function feed($limit = 30, $before = 0)
    {
        $profileID = Session::read("profileID");

        $rsp = new Response();
        
        //Make sure we are logged in
        if(empty($profileID))
        {
            $rsp->setFailure(401, "You are not authorized to access this.")
                ->send();

            return;
        }

        //Retrieve aditionnal model:
        $commentModel = new CommentModel();
        
        $events = $this->model->feed($profileID, $limit, $before);
        $nbrEvents = count($events);

        $feed = array();
        
        //Build the return array
        for($i = 0; $i < $nbrEvents; $i++)
        {
            $event = $events[$i];

            $eventBlock = array();
            
            if($event['type'] == "post")
            {
                $eventBlock["type"] = "post";
                $eventBlock["time"] = $event["time"];
                $eventBlock["postData"] = Response::read("post", "display", $event["dest"])['data'];

                array_push($feed, $eventBlock);
            }

            if($event['type'] == "comment")
            {
                $eventBlock["type"] = "comment";
                $eventBlock["time"] = $event["time"];
                $eventBlock["postData"] = Response::read("post", "display", $event["dest"])['data'];

                $commentData = $commentModel->getComment($event["source"]);

                $eventBlock["commentData"] = $commentData;
                $eventBlock["profileData"] = Response::read("profile", "get", $commentData["profile_id"]);

                array_push($feed, $eventBlock);
            }

            if($event['type'] == "like")
            {
                $eventBlock["type"] = "like";
                $eventBlock["time"] = $event["time"];
                $eventBlock["profileData"] = Response::read("profile", "get", $event["source"])['data'];

                $posts = array();
                
                //If a same user as consecutive likes, merge them is a single group
                for($j = $i; $j < $nbrEvents; $j++)
                {
                    if($events[$j]["type"] == "like" && $events[$j]["source"] == $events[$i]["source"])
                    {
                        array_push($posts, Response::read("post", "display", $events[$j]["dest"])['data']);
                        continue;
                    }
                    else
                    {
                        $i = $j;
                        break;
                    }
                }

                $i = $j;

                $eventBlock["nbrPosts"] = count($posts);
                $eventBlock["postsData"] = $posts;
            }

            if($event['type'] == "follow")
            {
                $eventBlock["type"] = "follow";
                $eventBlock["time"] = $event["time"];
                $eventBlock["profileData"] = Response::read("profile", "get", $event["source"])['data'];

                $followed = array();

                //If a same user as consecutive follow, merge them is a single group
                for($j = $i; $i < $nbrEvents; $j++)
                {
                    if($events[$j]["type"] == "follow" && $events[$j]["source"] == $events[$i]["source"])
                    {
                        array_push($followed, Response::read("profile", "get", $events[$j]["dest"])['data']);
                        continue;
                    }
                    else
                    {
                        $i = $j;
                        break;
                    }
                }

                $i = $j;

                $eventBlock["nbrFollowed"] = count($followed);
                $eventBlock["followedData"] = $followed;
            }

            array_push($feed, $eventBlock);

            unset($eventBlock);
        }

        $rsp->setSuccess(200)
            ->bindValue("nbrEvents", count($feed))
            ->bindValue("feed", $feed)
            ->send();
    }
}
?>
