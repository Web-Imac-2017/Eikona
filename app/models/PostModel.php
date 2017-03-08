<?php

class PostModel extends DBInterface
{
    private $postID = -1;
    private $postDatas = null;
	private $tags = null;

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
			return true;
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
        $stmt = $this->cnx->prepare("SELECT post_id, profile_id, post_type, post_extension, post_description, post_publish_time, post_edit_time, post_state, post_filter, post_geo_lat, post_geo_lng, post_geo_name, post_allow_comments, post_approved FROM posts WHERE post_id = :postID");
        $stmt->execute([":postID" => $postID]);

        $this->postID = $postID;
        $this->postDatas = $stmt->fetch();

		return "success";
    }

    /**
     * Create a new post as a draft
     *
     * @param $type Type of the post to be posted
     * @param $extension Extension of the picture/video of the post
     * @param $description Description of the post
    */
    public function create($type, $extension, $description, $comments)
    {
        //Wait for the upgrade of the Sanitize function
        //$this->extension = Sanitize::string($extension);
        //$this->type = Sanitize::string($type);

        $description = Sanitize::string($description);

		// To change when there will be profile
		$profile = Session::read("profileID");

        $stmt = $this->cnx->prepare("INSERT INTO posts(profile_id, post_type, post_extension, post_description, post_edit_time, post_state, post_publish_time, post_allow_comments) VALUES (:profile, :type, :extension, :description, :editTime, :state, :publishTime, :comments)");
        $stmt->execute([ ":profile"     => $profile,
						 ":type"        => $type,
                         ":extension"   => $extension,
                         ":description" => $description,
						 ":editTime"    => time(),
						 ":publishTime" => 0,
						 ":state"       => 0,
                         ":comments"    => $comments]);

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

    public function getFullPost()
    {
        return $this->postDatas;
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
     * Get post_allow_comments of the post
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
     * Get post_allow_comments of the post
     *
     */
    public function getFilter()
    {
        if($this->postID == 0)
        {
            return 0;
        }

        return $this->postDatas['post_filter'];
    }

     /*
     * Get post_allow_comments of the post
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

    public function getSaveFolder($profileID = 0)
    {
        if($profileID == 0 && $this->postID == 0)
        {
            return;
        }
        else if($profileID == 0)
        {
            $profileID = $this->postDatas['profile_id'];
        }

        $stmt = $this->cnx->prepare("SELECT profile_key FROM profiles WHERE profile_id = :profile");
        $stmt->execute([":profile" => $profileID]);

        $profileKey = $stmt->fetchColumn();

        return $_SERVER['DOCUMENT_ROOT']."/Eikona/app/medias/img/".$profileKey."/";
    }


    public function nbrPosts($profileID)
    {
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM posts WHERE profile_id = :profileID");
        $stmt->execute([":profileID" => Sanitize::int($profileID)]);

        return $stmt->fetchColumn();
    }

    /**
     * Return the last X posts published by the profile
     *
     * @param $limit int Number of posts to return. Defautl 30.
     */
    public function getPosts($profileID, $limit = 4096, $offset = 0, $after = 0, $before = 0, $order = "DESC")
    {
        $limit = Sanitize::int($limit);
        $profileID = Sanitize::int($profileID);

        if($profileID == -1 || $limit == 0)
            return;

        $where = "";
        $bindArray = [":pID" => $profileID];

        //Include only useful parameters for optimization
        if($after != 0)
        {
            $where .= " AND post_publish_time > :after";
            $bindArray[":after"] = Sanitize::int($after);
        }

        if($before != 0)
        {
            $where .= " AND post_publish_time < :before";
            $bindArray[":before"] = Sanitize::int($before);
        }

        $this->cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT post_id FROM posts WHERE profile_id = :pID ".$where." ORDER BY post_publish_time ".$order." LIMIT ".Sanitize::int($limit)." OFFSET ".Sanitize::int($offset);

        //Execute the query
        $stmt = $this->cnx->prepare($sql);
        $stmt->execute($bindArray);

        return $stmt->fetchAll(PDO::FETCH_COLUMN, "post_id");
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
     * Update the geoname of the post with the given $name
     *
     */
    public function updateFilter($filter)
    {
        if($this->postID == 0)
        {
            return false;
        }

		/* Sanitize String à ajouter pour Latitude */

        $stmt = $this->cnx->prepare("UPDATE posts SET post_filter = :filter WHERE post_id = :postID");
        $stmt->execute([":filter" => Sanitize::string($filter),
                         ":postID" => $this->postID]);

        $this->postDatas['post_filter'] = $filter;

		return $filter;
    }

    public function updateTime($postID)
    {
        $time = time();

        $stmt = $this->cnx->prepare("
            UPDATE posts SET post_edit_time = :time
            WHERE :postID = post_id");
        $stmt->execute([":time" => $time,
                        ":postID" => $postID]);
        return $time;
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

        $stmt = $this->cnx->prepare("UPDATE posts SET post_approved = 1 WHERE post_id = :postID");
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

}
