<?php
require_once "class.dbinterface.php";

class Profile extends DBInterface
{
    private $pID; //ID of the profile
    private $p;
    
    public function __construct($profileID)
    {
        parent::__construct();

        //confirm the id before doing anything
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
    
    //Retriving informations
    public function getID()         //Return the ID of the profile
    {
        return $this->pID;
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
        return $this->p['profile_name'];
    }
    
    public function getDesc() //Return the descrioption of the profile
    {
        return $this->p['profile_desc'];
    }    
    
    public function getViews() //Return the number of times the profile has been viewed
    {
        return $this->p['profile_views'];
    }
    
    public function isPrivate() //Return true if the profile is private, false otherwise
    {
        return $this->p['profile_private'];
    }
    
    public function getOwner($returnID = false)//return a class User of the owner of the profile, or juste the ID of the profile if
    {
        if($returnID)
        {
            return $this->p['user_id'];
        }
        
        return new Users($this->p['user_id']);
    }
    
    public function getPosts($limit = 30)//Return the last 30 posts (or the given number) for this profile. They are ordered by date desc.
    {
        $stmt = $this->cnx->prepare("SELECT post_id FROM posts WHERE profile_id = :pID LIMIT :limit ORDER BY post_publish_time DESC");
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
