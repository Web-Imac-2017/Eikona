<?php
class ProfilesModel
{
    private $pID; //ID of the profile
    private $p;

    public function __construct($profileID)
    {
        //confirm the id before doing anything
        //


        $this->pID = profileID;
    }

    //Retriving informations
    public function getID()         //Return the ID of the profile
    {
        return $pID;
    }

    public function getPic()        //Return path to the profile picture
    {
        if(file_exists("PATH/TO/PROFILE/PICTURE/".$this->pID.".jpg"))
        {
            return "PATH/TO/PROFILE/PICTURE/".$this->pID.".jpg";
        }

        return "PATH/TO/DEFAULT/PROFILE/PICTURE";
    }

    public function getName()       //Return the name of the profile
    {
        $sql = "SELECT profile_name FROM profiles WHERE profile_id = :pID";
        $name = DBInterface::request($sql, [":pID" => $this->pID])->fetchColumn();

        return $name;
    }

    public function getDesc() //Return the descrioption of the profile
    {
        $sql = "SELECT profile_desc FROM profiles WHERE profile_id = :pID";
        $desc = DBInterface::request($sql, [":pID" => $this->pID])->fetchColumn();

        return $desc;
    }

    public function getViews() //Return the number of times the profile has been viewed
    {
        $sql = "SELECT profile_views FROM profiles WHERE profile_id = :pID";
        $views = DBInterface::request($sql, [":pID" => $this->pID])->fetchColumn();

        return $views;
    }

    public function isPrivate() //Return true if the profile is private, false otherwise
    {
        $sql = "SELECT profile_views FROM profiles WHERE profile_id = :pID";
        $isPrivate = DBInterface::request($sql, [":pID" => $this->pID])->fetchColumn();

        return $isPrivate;
    }

    public function getOwner($returnID = false)//return a class User of the owner of the profile, or juste the ID of the profile if
    {
        $sql = "SELECT user_id FROM profiles WHERE profile_id = :pID";
        $uID = DBInterface::request($sql, [":pID" => $this->pID])->fetchColumn();

        if($returnID)
        {
            return $uID;
        }

        return new Users($uID);
    }

    public function getPosts($limit = 30)//Return the last 30 posts (or the given number) for this profile. They are ordered by date desc.
    {
        $limit = $limit == 0 ?
        $sql = "SELECT post_id FROM posts WHERE profile_id = :pID LIMIT :limit ORDER BY post_publish_time DESC";
        $posts = DBInterface::request($sql, [":pID" => $this->pID, "limit" => $limit])->fetchAll();

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
