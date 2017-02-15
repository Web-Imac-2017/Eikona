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


    /**
     * Return the description of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function name($profileID)
    {
        $this->model->setProfile($profileID);

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
        $this->model->setProfile($profileID);

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
        $this->model->setProfile($profileID);

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
        $this->model->setProfile($profileID);

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
        $this->model->setProfile($profileID);

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
        $this->model->setProfile($profileID);

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
        $this->model->setProfile($profileID);

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

        //Init JSON Response
        $rsp = new Response();
        $rsp->setFailure(400)
            ->bindValue("profileID", $profileID);

        $this->model->setProfile($profileID);

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
        $rsp->send();

    }
    /**
     * Increment by one (or more) the view counter of the specified profile
     *
     * @param $porfileID ID of the profile
     * @param $nbr Number of view to add.
     */
    public function addView($profileID, $nbr = 1)
    {
        $this->model->setProfile($profileID);

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

        $this->model->setProfile($profileID);
        $this->model->delete($profileID);

        if(file_exists("PATH/TO/PROFILE/PICTURE/".$profileID.".jpg"))
        {
            unlink("PATH/TO/PROFILE/PICTURE/".$profileID.".jpg");
        }
    }
}
?>
