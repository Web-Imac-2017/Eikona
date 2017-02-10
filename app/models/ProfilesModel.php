<?php
class Profile extends DBInterface
{
    /**
     * Internal varibales
     */
    private $pID;
    private $p;




    /**
     * Class constructor
     *
     * @param $profileID Profile to be used unique ID
     */
    public function __construct($profileID)
    {
        parent::__construct();

        //Confirm the id before doing anything
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM profiles WHERE profile_id = :pID");
        $stmt->execute([":pID" => $profileID]);

        //Profile ID not found
        if($stmt->fetchColumn() == 0)
            return null;

        //profile found
        $stmt = $this->cnx->prepare("SELECT user_id, profile_name, profile_desc, profile_create_time, profile_views, profile_private FROM profiles WHERE profile_id = :pID");
        $stmt->execute([":pID" => $profileID]);

        $this->pID = $profileID;
        $this->p = $stmt->fetch();
    }




    /**
     * Return the ID of the profile
     */
    public function getID()
    {
        return $this->pID;
    }

    /**
     * Return the path to the profile picture
     */
    public function getPic()
    {
        if(file_exists("PATH/TO/PROFILE/PICTURE/".$this->pID.".jpg"))
        {
            return "PATH/TO/PROFILE/PICTURE/".$this->pID.".jpg";
        }

        return "PATH/TO/DEFAULT/PROFILE/PICTURE";
    }

    /**
     * Return the name of the profile
     */
    public function getName()
    {
        return $this->p['profile_name'];
    }

    /**
     * Return the Description of the profile
     */
    public function getDesc()
    {
        return $this->p['profile_desc'];
    }

    /**
     * Return the number of times the profile has beed viewed
     */
    public function getViews()
    {
        return $this->p['profile_views'];
    }

    /**
     * Return true if the profile is private, false otherwise
     */
    public function isPrivate()
    {
        return $this->p['profile_private'];
    }

    /**
     * Return the ID of the owner of the profile
     */
    public function getOwner($returnID = false)
    {
        if($returnID)
        {
            return $this->p['user_id'];
        }

        return new Users($this->p['user_id']);
    }

    /**
     * Return the last X posts published by the profile
     *
     * @param $limit int Number of posts to return. Defautl 30.
     */
    public function getPosts($limit = 30)
    {
        $stmt = $this->cnx->prepare("SELECT post_id FROM posts WHERE profile_id = :pID LIMIT :limit ORDER BY post_publish_time DESC");
        $posts = $stmt->execute([":pID" => $this->pID, "limit" => $limit])->fetchAll();

        return $posts;
    }




    //updating informations
    public function updateName($newName) {}
    public function updateDesc($newDesc) {}
    public function updatePic($newPic) {}

    public function addView($nbr = 1) {}

    public function setPrivate() {}
    public function setPublic() {}
}
?>
