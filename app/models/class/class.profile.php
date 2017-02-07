<?php
class Profile
{
    private $pID; //ID of the profile
    
    public function __construct($profileID)
    {
        $profileID;
    }
    
    //Reteiving informations
    public function getID() {}
    
    public function getPic() {}
    public function getName() {}
    public function getDesc() {}
    
    public function getViews() {}
    
    public function isPrivate() {}
    
    public function getOwner($returnID = false) {}
    public function getPosts($limit = 0) {}
    
    //updating informations
    public function updateName($newName) {}
    public function updateDesc($newDesc) {}
    public function updatePic($newPic) {}
    
    public function addView($nbr = 1) {}
    
    public function setPrivate() {}
    public function setPublic() {}
}
?>