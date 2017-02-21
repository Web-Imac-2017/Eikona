<?php

class ProfilesController
{
    private $model;

    /**
     * Init the constructor and link the model
     * @private
     */
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

        $uID = 1; //Get current user ID

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
            $rsp->setSuccess(201)
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
     * Return the posts of the specified profile
     * @param  integer $profileID ID of the profile
     * @param  integer $limit     Number of posts to return
     * @return void
     */
    public function posts($profileID, $limit = 30)
    {
        if(!$this->setProfile($profileID) || !isAuthorized::getProfilePosts())
            return;

        $posts = $this->model->getPosts($limit);

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
        //Init JSON Response
        $rsp = new Response();


        //Exclude all failure possibilities
        if(!isAuthorized::updateProfile())
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if(!$this->setProfile($profileID))
        {
            return;
        }

        if(!isset($_POST['newValue']))
        {
            $rsp->setFailure(400, "Missing newValue POST variable. Update aborted.");
            $rsp->send();
            return;
        }

        //Now, do the update
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



    public function setPicture($profileID)
    {
        $rsp = new Response();

        if(!isAuthorized::updateProfile())
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
        if(!isAuthorized::updateProfile())
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
}
?>
