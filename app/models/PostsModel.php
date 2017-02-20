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
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM posts WHERE post_id = :postID");
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
        $stmt->execute([":postID" => $postID]);

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
        //Wait for the upgrade of the Sanitize function
        //$this->extension = Sanitize::string($extension);
        //$this->type = Sanitize::string($type);

        $description = Sanitize::string($description);
		$profile = 1;

        $stmt = $this->cnx->prepare("INSERT INTO posts(profile_id, post_type, post_extension, post_description, post_publish_time) VALUES (:profile, :type, :extension, :description, :time)");
        $stmt->execute([ ":profile" => $profile,
						 ":type" => $type,
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
        if($this->postID == 0)
        {
            return 0;
        }

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
        if($this->postID == 0)
        {
            return 0;
        }

        return $this->postDatas['post_state'];
    }

    /*
     * Get the Geo latitude, longitude and position of the post
     *
     */
    public function getGeo()
    {
        if($this->postID == 0)
        {
            return 0;
        }

        $tabGeo[0] = $this->postDatas['post_geo_lat'];
        $tabGeo[1] = $this->postDatas['post_geo_lng'];
        $tabGeo[2] = $this->postDatas['post_geo_name'];

        return $tabGeo;
    }

    /*
     * Get the description of the post
     *
     */
    public function getDescription()
    {
        if($this->postID == 0)
        {
            return 0;
        }
        return $postDatas['post_description'];
    }

    /*
     * Get the time when the post was published
     *
     */
    public function getPublishTime()
    {
        if($this->postID == 0)
        {
            return 0;
        }
        return $postDatas['post_publish_time'];
    }

    /*
     * Get the state of enableness of the comments
     *
     */
    public function getAllowComments()
    {
        if($this->postID == 0)
        {
            return 0;
        }
        return $postDatas['post_allow_comments'];
    }

    /*
     * Get the state of approvment of the post
     *
     */
    public function getApproved()
    {
        if($this->postID == 0)
        {
            return 0;
        }
        return $postDatas['post_allow_comments'];
    }

    /*
     * Get the time when the post was edited
     *
     */
    public function getUpdateTime()
    {
        if($this->postID == 0)
        {
            return 0;
        }

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
        if($this->postID == 0)
        {
            return 0;
        }

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
        if($this->postID == 0)
        {
            return 0;
        }

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
        if($this->postID == 0)
        {
            return 0;
        }

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
        if($this->postID == 0)
        {
            return 0;
        }

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
        if($this->postID == 0)
        {
            return 0;
        }

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
        if($this->postID == 0)
        {
            return 0;
        }


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
        if($this->postID == 0)
        {
            return 0;
        }


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
        if($this->postID == 0)
        {
            return 0;
        }


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
        if($this->postID == 0)
        {
            return 0;
        }
        $stmt = $this->cnx->prepare("DELETE FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => $this->postID]);
    }

    public function returnIfNull()
    {
        if($this->postID == 0)
        {
            return 0;
        }
    }
}
