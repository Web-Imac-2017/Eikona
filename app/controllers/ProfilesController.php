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
        /**
         * Verify if user is connected
         */

        if(empty($_POST['profileName']))
            return;

        $name = $_POST['profileName'];
        $desc = isset($_POST['profileDesc']) ? $_POST['profileDesc'] : "";
        $isPrivate = isset($_POST['profilePrivate']) ? true : false;

        $uID = 1; //Get current user ID

        $pID = $this->model->create($uID, $name, $desc, $isPrivate);

        /**
         * Handle profile picture
         */

        return $pID;
    }

    /**
     * Return the profile sheet to display
     *
     * @param $profileID ID of the profile to display
     */
    public function display($profileID){ }

    /**
     * Return the description of the specified profile
     *
     * @param $profileID ID of the profile
     */
    public function name($profileID)
    {
        $this->model->setProfile($profileID);

        $name = $this->model->getName();

        return $name;
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

        return $desc;
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

        return $pic;
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

        return $views;
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

        return $isPrivate;
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

        $this->model->setProfile($profileID);

        switch($field)
        {
            case "name":
                echo $this->model->updateName($_POST['newValue']);
            break;
            case "description":
                echo $this->model->updateDesc($_POST['newValue']);
            break;
            case "setPrivate":
                echo $this->model->setPrivate();
            break;
            case "setPublic":
                echo $this->model->setPublic();
            break;
            default;
                throw new InvalidArgumentException ("The field '".$field."'Is invalid.");
        }
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
        $this->model->addView($nbr);
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
