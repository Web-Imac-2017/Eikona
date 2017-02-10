<?php
class ProfilesModel extends DBInterface
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

        if(!ctype_digit(strval($profileID)) || $profileID < 1)
            return null;

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
     * Create a new profile
     *
     * @param $userID ID of the user
     * @param $name Name of the new profile
     * @param $desc Description of the new profile
     * @param $private bool Privacy setting for the new profile
     */
    public function create($userID, $name, $desc, $private)
    {

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

    /**
     * Update the name of the profile
     *
     * @param $newName New name of the new profile
     */
    public function updateName($newName)
    {
        $name = Secure::string($newName);

        $stmt = $this->cnx->prepare("UPDATE profiles SET profile_name = :name WHERE profile_id = :pID");
        $stmt->execute([":name" => $name,
                        ":pID" => $this->pID]);

        echo $name."<br>".$this->pID."<br>";

        print_r($this->cnx->errorInfo());

        $this->p['profile_name'] = $name;
    }

    /**
     * Update the description of the profile
     *
     * @param $newDesc New description of the new profile
     */
    public function updateDesc($newDesc)
    {
        $desc = Secure::string($newDesc);

        $stmt = $this->cnx->prepare("UPDATE profiles SET profile_desc = :desc WHERE profile_id = :pID");
        $stmt->execute([":desc" => $desc,
                        ":pID" => $this->pID]);

        $this->p['profile_desc'] = $desc;
    }

    /**
     * Add views to the profile
     *
     * @param $nbr Number of views to add
     */
    public function addView($nbr = 1)
    {
        if(!ctype_digit(strval($nbr)) || $nbr < 1)
            $nbr = 1;

        $stmt = $this->cnx->prepare("UPDATE profiles SET profile_views = profile_views + :nbr WHERE profile_id = :pID");
        $stmt->execute([":nbr" => $nbr,
                        ":pID" => $this->pID]);

        $this->p['profile_views'] += $nbr;
    }


    /**
     * Update privacy setting to private
     */
    public function setPrivate()
    {
        $stmt = $this->cnx->prepare("UPDATE profiles SET profile_private = 1 WHERE profile_id = :pID");
        $stmt->execute([":pID" => $this->pID]);

        $this->p['profile_private'] = 1;
    }

    /**
     * Update privacy setting to public
     */
    public function setPublic()
    {
        $stmt = $this->cnx->prepare("UPDATE profiles SET profile_private = 0 WHERE profile_id = :pID");
        $stmt->execute([":pID" => $this->pID]);

        $this->p['profile_private'] = 0;
    }


    /**
     * Delete the profile
     */
    public function deleteProfile() {}
}
?>
