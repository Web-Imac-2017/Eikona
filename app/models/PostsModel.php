<?php

// Manque les commentaires + des fonctions + prendre en compte la variable avec les infos de l'instance actuel

class PostsModel extends DBInterface
{
    private $postID = 0;
    private $postDatas = NULL;

    public function __construct($postID = 0)
    {
        parent::__construct();

        $this->setPost($postID);
    }


    /**
     * Class initializer. Tries to load informations on the given post
     *
     * @param $postID Post to be used unique ID
     */
    public function setPost($postID)
    {
        $postID = Sanitize::int($postID);

        if($postID < 1 || $postID == $this->postID)
        {
            $this->postDatas = 0;
            $this->postID = NULL;
            return;
        }

        //Confirm the id before doing anything
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM posts WHERE post_id = :pID");
        $stmt->execute([":postID" => $postID]);

        //Profile ID not found
        if($stmt->fetchColumn() == 0)
        {
            $this->p = 0;
            $this->pID = NULL;
            return;
        }

        //Post found
        $stmt = $this->cnx->prepare("SELECT user_id, profile_name, profile_desc, profile_create_time, profile_views, profile_private FROM profiles WHERE profile_id = :pID");
        $stmt->execute([":pID" => $profileID]);

        $this->postID = $profileID;
        $this->postDatas = $stmt->fetch();
    }

    /**
     * Create a new post
     *
     * @param $type Type of the post to be posted
     * @param $extension Extension of the picture/video of the post
     * @param $description Description of the post
     * @param $time Time the post was created
     *
    */
    public function create($type, $extension, $description, $time)
    {
        //Wait for the upgarde of the Sanitize function
        //$this->extension = Sanitize::string($extension);
        //$this->type = Sanitize::string($type);

        $this->description = Sanitize::string($description);

        $stmt = $this->cnx->prepare("INSERT INTO posts(post_type, post_extension, post_description, post_publish_time) VALUES (:type, :extension, :description, :time)");
        $stmt->execute([ ":type" => $type,
                         ":extension" => $extension,
                         ":description" => $description,
                         ":time" => $time
        ]);

        $postID = $this->cnx->lastInsertId();

        $this->setPost($postID);

        return $postID;
    }

    public function setGeo($latitude, $longitude, $name)
    {
        //latitude = Sanitize::string($latitude);
        //longitude = Sanitize::string($longitude);
        name = Sanitize::string($name);

        $stmt = $this->cnx->prepare("INSERT INTO posts(post_geo_lat, post_geo_lnt) VALUES (:latitude, :longitude, :name)");

    }


//    public function update($description, $time)
//    {
//        $this->description = Sanitize::string($description);
//        $stmt = $this->cnx->prepare("UPDATE posts SET post_description = :description, post_edit_time = :time");
//        $stmt->execute([
//            ":description" => $description,
//            ":time" => $time
//        ]);
//    }

    public function updateDescription()
    {

    }

    public function updateTime()
    {

    }

    public function updateState()
    {

    }

    public function updateLatitude()
    {

    }

    public function updateLongitude()
    {

    }

    public function updateGeo()
    {

    }

    public function allowComments()
    {

    }

    public function updatePostApproved()
    {

    }

    public function getPublishTime()
    {

    }

    public function getState()
    {

    }

    public function getLatitude()
    {

    }

    public function getLongitude()
    {

    }

    public function getGeoName()
    {

    }

    public function delete($id){
        $stmt = $this->cnx->prepare("DELETE FROM posts WHERE post_id = :id");
        $stmt->execute([
            ":id" => $id
        ]);
    }
}
