<?php

class PostModel extends DBInterface
{
    private $postID = -1;
    private $postDatas = null;

    public function __construct($postID = -1)
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

		/* If post is already define -> current post */
		if($postID == $this->postID)
		{
			return;
		}

        if($postID < 1)
        {
            $this->postDatas = NULL;
            $this->postID = -1;
            return "wrongFormat";
        }

        //Confirm the id before doing anything
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => $postID]);

        //Post ID not found
        if($stmt->fetchColumn() == 0)
        {
            $this->postDatas = NULL;
            $this->postID = -1;
            return "notFound";
        }

        //Post found
        $stmt = $this->cnx->prepare("SELECT post_id, profile_id, post_type, post_extension, post_description, post_publish_time, post_state, post_geo_lat, post_geo_lng, post_geo_name, post_allow_comments, post_approved FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => $postID]);

        $this->postID = $postID;
        $this->postDatas = $stmt->fetch();

		return "success";
    }

    /**
     * Create a new post
     *
     * @param $type Type of the post to be posted
     * @param $extension Extension of the picture/video of the post
     * @param $description Description of the post
    */
    public function create($type, $extension, $description)
    {
        //Wait for the upgrade of the Sanitize function
        //$this->extension = Sanitize::string($extension);
        //$this->type = Sanitize::string($type);

        $description = Sanitize::string($description);

		// To change when there will be profile
		$profile = Session::read("profileID");

        $stmt = $this->cnx->prepare("INSERT INTO posts(profile_id, post_type, post_extension, post_description, post_edit_time, post_publish_time) VALUES (:profile, :type, :extension, :description, :editTime, :publishTime)");
        $stmt->execute([ ":profile" => $profile,
						 ":type" => $type,
                         ":extension" => $extension,
                         ":description" => $description,
						 ":editTime" => time(),
						 ":publishTime" => time()
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
     * Get the profileID of the post
     *
     */
    public function getProfileID()
    {
        if($this->postID == 0)
        {
            return 0;
        }

        return $this->postDatas['profile_id'];
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
            return false;
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

        return $this->postDatas['post_description'];

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
        return $this->postDatas['post_publish_time'];
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
        return $this->postDatas['post_allow_comments'];
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
        return $this->postDatas['post_allow_comments'];
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
            return false;
        }

		$description = Sanitize::string($description);

        $stmt = $this->cnx->prepare("UPDATE posts SET post_description = :description WHERE post_id = :postID");
        $stmt->execute([
           ":description" => $description,
            ":postID" => $this->postID
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
            return false;
        }

        $stmt = $this->cnx->prepare("UPDATE posts SET post_state = :state WHERE post_id = :postID");
        $stmt->execute([":state" => $state,
                      ":postID" => $this->postID]);

        $this->postDatas['post_state'] = $state;

		return $state;
    }

    /*
     * Update the latitude of the post with the given $latitude
     *
     */
    public function updateLatitude($latitude)
    {
        if($this->postID == 0)
        {
            return false;
        }

		/* Sanitize à ajouter pour Latitude */

        $stmt = $this->cnx->prepare("UPDATE posts SET post_geo_lat = :latitude WHERE post_id = :postID");
        $stmt->execute([":latitude" => $latitude,
                         ":postID" => $this->postID]);

        $this->postDatas['post_geo_lat'] = $latitude;

		return $latitude;
    }

    /*
     * Update the longitude of the post with the given $longitude
     *
     */
    public function updateLongitude($longitude)
    {
        if($this->postID == 0)
        {
            return false;
        }

		/* Sanitize à ajouter pour Latitude */

        $stmt = $this->cnx->prepare("UPDATE posts SET post_geo_lng = :longitude WHERE post_id = :postID");
        $stmt->execute([":longitude" => $longitude,
                         ":postID" => $this->postID]);

        $this->postDatas['post_geo_lng'] = $longitude;

		return $longitude;
    }

    /*
     * Update the geoname of the post with the given $name
     *
     */
    public function updateGeoName($name)
    {
        if($this->postID == 0)
        {
            return false;
        }

		/* Sanitize String à ajouter pour Latitude */

        $stmt = $this->cnx->prepare("UPDATE posts SET post_geo_name = :name WHERE post_id = :postID");
        $stmt->execute([":name" => $name,
                         ":postID" => $this->postID]);

        $this->postDatas['post_geo_name'] = $name;

		return $name;
    }

    /*
     * Allow comments for the post
     *
     */
    public function allowComments()
    {
        if($this->postID == 0)
        {
            return false;
        }

        $stmt = $this->cnx->prepare("UPDATE posts SET post_allow_comments = 1 WHERE post_id = :postID");
        $stmt->execute([":postID" => $this->postID]);

        $this->postDatas['post_allow_comments'] = 1;

		return $this->postDatas['post_allow_comments'];
    }

    /*
     * Disable comments for the post
     *
     */
    public function disableComments()
    {
        if($this->postID == 0)
        {
            return false;
        }

        $stmt = $this->cnx->prepare("UPDATE posts SET post_allow_comments = 0 WHERE post_id = :postID");
        $stmt->execute([":postID" => $this->postID]);

        $this->postDatas['post_allow_comments'] = 0;

		return $this->postDatas['post_allow_comments'];
    }

    /*
     * Update the post, now it's approved
     *
     */
    public function updatePostApproved()
    {
        if($this->postID == 0)
        {
            return false;
        }

        $stmt = $this->cnx->prepare("UPDATE posts SET post_approved = 1 WHERE post_id = postID");
        $stmt->execute([":postID" => $this->postID]);

        $this->postDatas['post_approved'] = 1;

		return $this->postDatas['post_approved'];
    }

    /*
     * Delete the post
     *
     */
    public function delete()
    {
        if($this->postID == 0)
        {
            return false;
        }

        $stmt = $this->cnx->prepare("DELETE FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => $this->postID]);

		return true;
    }

    /*
     * Vérfie si le post a supprimé appartient à l'user courant
     * 
     */
    public function checkProfileBeforeDeletion($postID, $profileID)
    {
        
    }

}
