<?php

class ProfilesController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new ProfilesModel();
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
         * Verify if user is connected
         */

        if(empty($_POST['profileName']))
        {
            $rsp->setFailure(400);
            $rsp->send();
            return;
        }
        $name = $_POST['profileName'];
        $desc = isset($_POST['profileDesc']) ? $_POST['profileDesc'] : "";
        $isPrivate = isset($_POST['profilePrivate']) ? true : false;

        $uID = 1; //Get current user ID

        $pID = $this->model->create($uID, $name, $desc, $isPrivate);

        /**
         * Handle profile picture
         */

        //Send JSON response
        $rsp->setSuccess(201)
            ->bindValue("profileID", $pID)
            ->send();
    }

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
        if(!$this->setProfile($profileID))
            return;

        $pic = $this->model->getPic();

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

        $owner = $this->model->getOwner();

        //Send JSON response
        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("profileID", $profileID)
            ->bindValue("profileOwner", $owner)
            ->send();
    }

    /**
     * Return the number of views of the specified profile
     *
     * @param $profileID ID of the profile
     * @param $limit number of posts to return
     */
    public function posts($profileID, $limit = 30)
    {
        if(!$this->setProfile($profileID))
            return;

        $posts = $this->model->getPosts();

        return $posts;
    }

    /**
     * Update the specified element of the profile
     *
     * @param $field Field to be updated
     * @param $profileID ID of the profile
     */
    public function update($field, $profileID)
    {
        /*
         * Only allow users who have authority on this profile to update
         */

        if(!$this->setProfile($profileID))
            return;

        //Init JSON Response
        $rsp = new Response();

        if(!isset($_POST['newValue']))
        {
            $rsp->setFailure(400, "Missing newValue POST variable. Update aborted.");
            $rsp->send();
            return;
        }

        switch($field)
        {
            case "name":

                if($this->model->updateName($_POST['newValue']))
                {
                    $rsp->setSuccess(200)
                        ->bindValue("profileName", $this->model->getName());
                }

            break;
            case "description":

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
     */
    public function delete($profileID)
    {
        /*
         * Only allow users who have authority on this profile to delete
         */

        /*
         * Remove dependants data like posts, comments, likes, etc...
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
}
?>
