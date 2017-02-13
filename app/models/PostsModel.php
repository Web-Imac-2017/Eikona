<?php

class PostsModel extends DBInterface
{
    private $postID = 0;
    private $postDatas = null;

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
            $this->postID = null;
            return;
        }

        //Confirm the id before doing anything
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM posts WHERE post_id = :pID");
        $stmt->execute([":postID" => $postID]);

        //Post ID not found
        if($stmt->fetchColumn() == 0)
        {
            $this->postDatas = 0;
            $this->postID = null;
            return;
        }

        //Post found
        $stmt = $this->cnx->prepare("SELECT post_id, post_type, post_extension, post_description, post_publish_time, post_state, post_geo_lat, post_geo_lng, post_geo_name, post_allow_comments, post_approved FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => postID]);

        $this->postID = $postID;
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
        $this->returnIfNull();

        //Wait for the upgrade of the Sanitize function
        //$this->extension = Sanitize::string($extension);
        //$this->type = Sanitize::string($type);

        $description = Sanitize::string($description);

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

    /**
     * Set the geo datas
     *
     * @param $latitude Latitude of the location of the post
     * @param $longitude Longitude of the location of the post
     * @param $name Name of the location of the post
     *
    */
    public function setGeo($latitude, $longitude, $name)
    {
        $this->returnIfNull();

        //$latitude = Sanitize::latitude($latitude);
        //$longitude = Sanitize::longitude($longitude);
        $name = Sanitize::string($name);

        $stmt = $this->cnx->prepare("INSERT INTO posts(post_geo_lat, post_geo_lnt, post_geo_name) VALUES (:latitude, :longitude, :name) WHERE post_id = :postID");

        $stmt->execute([ ":latitude" => $latitude,
                         ":longitude" => $longitude,
                         ":name" => $name
                       ]);

        $this->postDatas['post_geo_lat'] = $latitude;
        $this->postDatas['post_geo_lng'] = $longitude;
        $this->postDatas['post_geo_lng'] = $name;
    }


    /*
     * Get the state of the post
     *
     */
    public function getState()
    {
        $this->returnIfNull();

        return $this->postDatas['post_state'];
    }

    /*
     * Get the Geo latitude of the post
     *
     */
    public function getLatitude()
    {
        $this->returnIfNull();

        return $this->postDatas['post_geo_lat'];
    }

    /*
     * Get the longitude of the post
     *
     */
    public function getLongitude()
    {
        $this->returnIfNull();

        return $this->postDatas['post_geo_lng'];
    }

    /*
     * Get the Geo name of the post
     *
     */
    public function getGeoName()
    {
        $this->returnIfNull();

        return $this->postDatas['post_geo_name'];
    }

    /*
     * Get the time when the post was published
     *
     */
    public function getTime()
    {
        $this->returnIfNull();

        $stmt = $this->cnx->prepare("SELECT post_publish_time FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => $this->postID]);
    }

    /*
     * Get the time when the post was edited
     *
     */
    public function getUpdateTime()
    {
        $this->returnIfNull();

        $stmt = $this->cnx->prepare("SELECT post_edit_time FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => $this->postID]);
    }

    /**
     * Update the description of the post
     *
     * @param $description Of the given post
     *
    */
    public function updateDescription($description)
    {
        $this->returnIfNull();

        $description = Sanitize::string($description);

        $stmt = $this->cnx->prepare("UPDATE posts SET post_description = :description WHERE post_id = :postID");
        $stmt->execute([
           ":description" => $description,
            ":pID" => $this->postID
        ]);

        $this->postDatas['description'] = $description;

        return $description;
    }

    /*
     * Update the state of the post
     *
     */
    public function updateState($state)
    {
        $this->returnIfNull();

        $stmt = $this->cnx->prepare("UPDATE posts SET post_state = :state WHERE post_id = :postID");
        $stmt->execute([":state" => $state,
                      ":postID" => $this->postID]);

        $this->postDatas['post_state'] = $state;
    }

    /*
     * Update the latitude of the post with the given $latitude
     *
     */
    public function updateLatitude($latitude)
    {
        $this->returnIfNull();

        $stmt = $this->cnx->prepare("UPDATE posts SET post_geo_lat = :latitude WHERE post_id = :postID");
        $stmt->execute([":latitude" => $latitude,
                         ":postID" => $this->postID]);

        $this->postDatas['post_geo_lat'] = $latitude;
    }

    /*
     * Update the longitude of the post with the given $longitude
     *
     */
    public function updateLongitude($longitude)
    {
        $this->returnIfNull();

        $stmt = $this->cnx->prepare("UPDATE posts SET post_geo_lng = :longitude WHERE post_id = :postID");
        $stmt->execute([":longitude" => $longitude,
                         ":postID" => $this->postID]);

        $this->postDatas['post_geo_lng'] = $longitude;
    }

    /*
     * Update the geoname of the post with the given $name
     *
     */
    public function updateGeoName($name)
    {
        $this->returnIfNull();

        $stmt = $this->cnx->prepare("UPDATE posts SET post_geo_lat = :name WHERE post_id = :postID");
        $stmt->execute([":name" => $name,
                         ":postID" => $this->postID]);

        $this->postDatas['post_geo_name'] = $name;
    }

    /*
     * Allow comments for the post
     *
     */
    public function allowComments()
    {
        $this->returnIfNull();


        $stmt = $this->cnx->prepare("UPDATE posts SET post_allow_comments = 1 WHERE post_id = :pID");
        $stmt->execute([":postID" => $this->postID]);

        $this->postDatas['post_state'] = 1;
    }

    /*
     * Disable comments for the post
     *
     */
    public function disableComments()
    {
        $this->returnIfNull();


        $stmt = $this->cnx->prepare("UPDATE posts SET post_allow_comments = 0 WHERE post_id = :postID");
        $stmt->execute([":postID" => $this->postID]);

        $this->postDatas['post_state'] = 0;
    }

    /*
     * Update the post, now it's approved
     *
     */
    public function updatePostApproved()
    {
        $this->returnIfNull();


        $stmt = $this->cnx->prepare("UPDATE posts SET post_approved = 1 WHERE post_id = postID");
        $stmt->execute([":postID" => $this->postID]);

        $this->postDatas['post_approved'] = 1;
    }

    /*
     * Delete the post
     *
     */
    public function delete()
    {
        $this->returnIfNull();

        $stmt = $this->cnx->prepare("DELETE FROM posts WHERE post_id = :id");
        $stmt->execute([":postID" => $this->postID]);
    }

    public function returnIfNull()
    {
        if($this->pID == 0)
        {
            return 0;
        }
    }
}
