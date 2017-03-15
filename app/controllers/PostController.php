<?php

interface PostControllerInterface
{
    public function create();
    
	public function setPost($postID);
    
	public function delete($postID);
    
	public function display($postID, $silence = true);
    
	public function update($field, $postID);
    
    public function publish($postID);
    
    public function setFilter($postID, $filter);

	public function like($postID);

	public function unlike($postID);

	public function likes($postID);

	public function comments($postID);
    
	public function tag($tagName);

	public function view($postID);

	public function nbView();
    
    public function popular($limit = 30);
}

class PostController implements PostControllerInterface
{
	private $model;
	private $tagModel;
	private $likeModel;
	private $commentModel;
	private $postViewModel;


	public function __construct()
	{
		$this->model         = new PostModel();
		$this->tagModel      = new TagModel();
		$this->likeModel     = new LikeModel();
		$this->commentModel  = new CommentModel();
		$this->postViewModel = new PostViewModel();
	}

	/**
	 * Create a post
	 */
	public function create()
	{
		$rsp = new Response();

		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

        //Are we logged in ?
		if(!isAuthorized::isUser())
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			
            return;
		}

        //Do we have a current profile?
		if(!$profileID)
        {
			$rsp->setFailure(401, "You don't have current profile selected")
			    ->send();
		
            return;
        }		
        
        //DO we have an image?
		if(empty($_FILES['img']))
        {
			$rsp->setFailure(400, "no file selected")
			    ->send();
			return;
		}

		/*
		 * Management of the picture
		 * Management of the video is missing
		 */
		if(!is_uploaded_file($_FILES['img']['tmp_name']))
		{
			$rsp->setFailure(400, "file not uploaded")
                ->send();

            return;
        }
			
        $desc = !empty($_POST['postDescription']) ? $_POST['postDescription'] : "";
        preg_match_all('/#([^# ]+)/', $desc, $tags);
        $comments = Sanitize::booleanToInt(isset($_POST['disableComments']) ? false : true);
        $source = $_FILES['img']['tmp_name'];

        $format = getimagesize($source);

        //Prevent format is wrong
        if(!$format)
        {
            $rsp->setFailure(400, "File do not have good extension")
                ->send();

            return;
        }

        $type = "image";
        $postID = $this->model->create($type, "jpg", $desc, $comments);

        if(!$postID)
        {
            $rsp->setFailure(400, "echec lors de l'upload")
                ->send();

            Response::read("Post", "delete", $postID);

            return;
        }

        //Add the tags
        while (list(, $tag) = each($tags[1])) 
        {
            $this->tagModel->addTag($postID, $tag);
        }

        //Prepare destination folder
        $folder = $this->model->getSaveFolder();
        $savePath = $folder.$postID.".jpg";

        //Save picture
        if(!FiltR::saveTo($source, $savePath))
        {
            $rsp->setFailure(415, "An error occured with the image. Please try again")
                ->send();

            Response::read("Post", "delete", $postID);

            return;
        }

        //Create contact cheet for the picture
        FiltR::proof($savePath, $folder.$postID."-contact.jpg");

        $rsp->setSuccess(201, "post created")
            ->bindValue("userID", $userID)
            ->bindValue("profileID", $profileID)
            ->bindValue("postStatus", 0)
            ->bindValue("postID", $postID);

		$rsp->send();
	}
    
	/**
	 * Set post to work with
	 * @param  integer $postID Post ID to use
	 * @return boolean true on success, false otherwise
	 */
	public function setPost($postID)
	{
		$result = $this->model->setPost($postID);

		if($result == "success")
		{
            return true;
        }
            
        $rsp = new Response();

        if($result == "wrongFormat")
        {
            $rsp->setFailure(400, "Wrong format. This is not a post ID.");
        }
        else if($result == "notFound")
        {
            $rsp->setFailure(404, "Given post ID does not exist.");
        }

        $rsp->send();

        return false;
	}

	/**
	 * Delete a post and the image with it
	 * @param integer $postID Post to delete
	 */
	public function delete($postID)
	{
		$rsp = new Response();

		$profileID = Session::read("profileID");

		if(!$this->setPost($postID))
			return;

		if(!$profileID)
        {
			$rsp->setFailure(401, "You do not have current profile selected")
			    ->send();
			return;
		}

		if(!isAuthorized::editPost($postID))
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

        //Deleting associated files
        $images = $this->getImages($postID);

        if(file_exists($images["originalPicture"]))
        {
            unlink($images["originalPicture"]);
        }

        if(file_exists($images["editedPicture"]))
        {
            unlink($images["editedPicture"]);
        }

        if(file_exists($images["contactPicture"]))
        {
            unlink($images["contactPicture"]);
        }

        //Deleting the post
		if(!$this->model->delete())
		{
			$rsp->setFailure(400, "An error occured while deleting the post. Please try again")
                ->send();

            return;
        }

        $rsp->setSuccess(200, "post deleted")
            ->bindValue("postId", $postID);

		$rsp->send();
	}

	/**
	 * Return all the information of the post with the given ID
	 * @param integer $postID Post to retreive information from
	 */
	public function display($postID)
	{
		if(!$this->setPost($postID))
			return;

		$rsp = new Response();
        
        //Can the current profule see this post ?
        if(!isAuthorized::seeFullProfile($this->model->getProfileID()))
        {
            $rsp->setFailure(401, "You can not see this post")
                ->send();

            return;
        }

		$data = $this->model->getFullPost();

        $images = $this->getImages($postID);
		
		$rsp->setSuccess(200, "get all post informations")
			->bindValue("postID", $postID)
			->bindValue("profileID", $data['profile_id'])
			->bindValue("profileData", Response::read("profile", "get", $data['profile_id'])["data"])
			->bindValue("desc", $data['post_description'])
			->bindValue("publishTime", $data['post_publish_time'])
			->bindValue("updateTime", $data['post_edit_time'])
			->bindValue("allowComments", $data['post_allow_comments'])
			->bindValue("approved", $data['post_approved'])
			->bindValue("state", $data['post_state'])
			->bindValue("geo", ['lat' => $data['post_geo_lat'],
				                'lng' => $data['post_geo_lng'],
							    'name' => $data['post_geo_name']
								])
            ->bindValue("originalPicture", $images["originalPicture"])
            ->bindValue("editedPicture", $images["editedPicture"])
            ->bindValue("contactPicture", $images["contactPicture"])
			->send();
	}


    /**
     * Return links to the images of the post
     * @param  integer $postID Post to use
     * @return array   URLs to the images
     */
    private function getImages($postID)
    {
		if(!$this->setPost($postID))
			return;

        $state = $this->model->getState();
        $images = [];
        $folder = $this->model->getSaveFolder();

        $images["originalPicture"] = $folder.$postID.".jpg";

        if($state != 0)
        {
            //Post is published
            $images["editedPicture"] = null;
            $images["contactPicture"] = null;

            return $images;
        }

        //post not published yet

        $filter = $this->model->getFilter();

        if($filter == null)
        {
            $images["editedPicture"] = null;
        }
        else
        {
            $images["editedPicture"] = $folder.$postID."-".$filter.".jpg";
        }

        $images["contactPicture"] = $folder.$postID."-contact.jpg";

        return $images;
    }



	/*
	 * Update a post, with the field to update given
	 * @param $field Field to update
	 * @param $postID ID of the post to be update
	 * Only for this fields : description, geo, allowComments, disableComments
	 */
	public function update($field, $postID)
	{
		$rsp = new Response();

		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		if(!$this->setPost($postID))
			return;
        
        //Do we have a current profile?
		if(!$profileID)
        {
			$rsp->setFailure(401, "You do not have current profile selected")
			    ->send();
			
            return;
		}
        
        //Can this profile edit this post?
		if(!isAuthorized::editPost($postID))
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			
            return;
		}

		switch($field)
		{
			case "description" :
				
                //Do we have all we need ?
                if(empty($_POST['desc']))
				{
					$rsp->setFailure(400, "Missing value. Edit aborted.")
                        ->send();
                    
                    return;
                }
                
                $desc = $this->model->updateDescription($_POST['desc']);

                if($desc === false)
                {
                    $rsp->setFailure(400)
                        ->send();
                    
                    return;
                }
                
                $rsp->setSuccess(200)
                    ->bindValue("postID", $postID)
                    ->bindValue("updateTime", time())
                    ->bindValue("updateTime", $desc)
                    ->send();
                
			break;
			case "geo" :
				
                //Do we have all we need ?
                if(empty($_POST['post_geo_lat']) || empty($_POST['post_geo_lng']) || empty($_POST['post_geo_name']))
				{
					$rsp->setFailure(400, "Missing value. Edit aborted.")
                        ->send();
                    
                    return;
                }
                
                $lat = $this->model->updateLatitude($_POST['post_geo_lat']);
                $lng = $this->model->updateLongitude($_POST['post_geo_lng']);
                $name = $this->model->updateGeoName($_POST['post_geo_name']);

                //Was there an error updating geo
                if($lat === false || $lng === false || $name === false)
                {
                    $rsp->setFailure(400)
                        ->send();
                    
                    return;
                }
                
                $rsp->setSuccess(200)
                    ->bindValue("postID", $postID)
                    ->bindValue("updateTime", time())
                    ->bindValue("postGeo", ["lat" => $lat,
                                            "lng" => $lng,
                                            "name" => $name
                                           ])
                    ->send();
                
			break;
			case "allowComments" :
				
                $allowComments = $this->model->allowComments();
                
                //Was there an error ?
				if($allowComments === false)
                {
					$rsp->setFailure(400)
                        ->send();
                    
                    return;
				} 
				
                $rsp->setSuccess(200)
				    ->bindValue("postID", $postID)
				    ->bindValue("allowComments", $allowComments)
                    ->send();
                
			break;
			case "disableComments" :
				$disableComments = $this->model->disableComments();

                //Was there an error ?
				if($disableComments === false)
                {
					$rsp->setFailure(400)
                        ->send();
                    
                    return;
                }
                
                $rsp->setSuccess(200)
                    ->bindValue("postID", $postID)
                    ->bindValue("disableComments", $disableComments)
                    ->send();
                
			break;
			case "postApproved" :
				
                $postApproved = $this->model->updatePostApproved();
                
                //Was there an error ?
				if($postApproved === false)
                {
					$rsp->setFailure(400)
                        ->send();
                    
                    return;
                }
                
                $rsp->setSuccess(200)
                    ->bindValue("postID", $postID)
                    ->bindValue("postApproved", $postApproved)
                    ->bindValue("updateTime", time())
                    ->send();
                
			break;
            case "state":
                
                //Do we have all we need?
				if(empty($_POST['state']))
                {
                    $rsp->setFailure(400, "Edit aborted. Missing value.")
                        ->send();
                    
                    return;
                }
                
                //Are we authorized to update the state this way?
                if(!isAuthorized::isModerator($userID) && !isAuthorized::isAdmin($userID))
                {
					$rsp->setFailure(401, "You are not authorized to do this action.")
                        ->send();
                    
                    return;
                }
                
                //Is this a valid state value
				if($_POST['state'] == 1 || $_POST['state'] == 2)
                {
                    $rsp->setFailure(400, "Wrong value for state")
                        ->send();
                    
                    return;
                }
                
                $newState = $this->model->updateState($_POST['state']);

                //Was there an error?
                if($newState === false)
                {
                    $rsp->setFailure(400, "error during request")
                        ->send();
                    
                    return;
				}
                
                $rsp->setSuccess(200)
                    ->bindValue("postID", $postID)
                    ->bindValue("state", $newState)
                    ->bindValue("updateTime", time())
                    ->send();
                
			break;
			default;
				$rsp->setFailure(405);
		}
	}

    /**
     * Publish the given post
     * @param integer $postID POst to publish;
     */
    public function publish($postID)
    {
        $rsp = new Response();

        //Is current profile allowed to edit post?
		if(!isAuthorized::editPost($postID))
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

		if(!$this->setPost($postID))
			return;

        $images = $this->getImages($postID);
        
        //Was the publish done correctly ?
        if($this->model->publish($postID) === false)
        {
            $rsp->setFailure(400, "error during request")
                ->send();
        }

        //Clean up pictures
        if(!empty($images["editedPicture"]))
        {
            //Remove original picture and replace it with the edited one
            unlink($images["originalPicture"]);
            rename($images["editedPicture"], $images["originalPicture"]);
        }

        //Remove contact sheet
        unlink($images["contactPicture"]);

        $rsp->setSuccess(200)
            ->bindValue("postID", $postID)
            ->bindValue("state", 1)
            ->send();

    }


    /**
     * Apply the desired filter to the given post
     * @param integer $postID Post to update
     * @param string  $filter Name of the filter to apply
     */
    public function setFilter($postID, $filter)
    {
        $rsp = new Response();
        
        //Can this profile edit this post
		if(!isAuthorized::editPost($postID))
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}
        
        //Is given filter a supported one
        if(!in_array($filter, FiltR::$availableFilters) && $filter != "none")
        {
			$rsp->setFailure(400, "This filter does not exists")
			    ->send();
			return;
		}

		if(!$this->setPost($postID))
			return;
        
        //Can we change this post's filter
        if($this->model->getState() != 0)
        {
			$rsp->setFailure(401, "You cannot edit the picture of an already published post.")
			    ->send();
			return;
		}

        $folder = $this->model->getSaveFolder();

        $currentFilter = $this->model->getFilter();

        //Are we using the same filter as before?
        if($currentFilter === $filter)
        {
            $rsp->setSuccess(200, "Filter unchanged")
                ->bindValue("postID", $postID)
                ->bindValue("currentFilter", $filter);

                if($filter == null)
                    $rsp->bindValue("postPicture", $folder.$postID.".jpg");
                else
                    $rsp->bindValue("postPicture", $folder.$postID."-".$filter.".jpg");

            $rsp->send();

            return;
        }

        //Remove old one and create copy with filter applied
        if(file_exists($folder.$postID."-".$currentFilter.".jpg"))
        {
            unlink($folder.$postID."-".$currentFilter.".jpg");
        }

        $this->model->updateFilter($filter);

        //Are we removing the filter ?
        if($filter == "none")
        {
            $rsp->setSuccess(200)
                ->bindValue("postID", $postID)
                ->bindValue("currentFilter", null)
                ->bindValue("originalPicture", $folder.$postID.".jpg")
                ->send();

            return;
        }

        FiltR::$filter($folder.$postID.".jpg", $folder.$postID."-".$filter.".jpg");

        $rsp->setSuccess(200)
            ->bindValue("postID", $postID)
            ->bindValue("currentFilter", $filter)
            ->bindValue("editedPicture", $folder.$postID."-".$filter.".jpg")
            ->send();

    }


	/************************************/
	/*************** LIKE ***************/
	/************************************/

	/**
	 * Like the post
	 * @param integer $postID Post to like
	 */
	public function like($postID)
	{
		$postID = Sanitize::int($postID);
		if(!$this->setPost($postID))
			return;

		$rsp = new Response();

		//get ID
		$profileID = Session::read("profileID");

        //Are we logged in ?
		if(!isAuthorized::isUser())
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();
		
            return;
		}
        
        //Do we have a current profile
		if(!$profileID)
        {
			$rsp->setFailure(401, "You don't have current profile selected")
			    ->send();
		
            return;
		}

        //Have we reached the like per hours limit
		if($this->likeModel->countLikeFromLastHour($profileID) > 200) 
        {
			$rsp->setFailure(406, "You have already liked 200 posts during the last 60 minutes, Calm down Billy Boy !")
			    ->send();
	
            return;
		}

		//Ar we already liking the post ?
		if($this->likeModel->isLiked($postID, $profileID))
        {
			$rsp->setFailure(400, "post already liked")
                ->send();
            
            return;
        }

        //Are we trying to like our own post ?
        if($this->model->getProfileID() == $profileID)
        {
            $rsp->setFailure(400, "You can not like your own post")
                ->send();
            
            return;
        }
        
        //Are we allowed to like this post?
        if(!isAuthorized::seeFullProfile($this->model->getProfileID()))
        {
            $rsp->setFailure(401, "You can not see this post")
                ->send();
            
            return;
        }
        
        $this->likeModel->like($postID, $profileID);
        Response::read("notification", "create", "newLike", $profileID, $this->model->getProfileID(), $postID);
        
        $rsp->setSuccess(200, "Post liked")
            ->bindValue("postID", $postID)
            ->bindValue("profileID", $profileID)
            ->send();

	}

	/**
	 * Unlike the post
	 * @param integer $postID Post to unlink
	 */
	public function unlike($postID)
	{
		$postID = Sanitize::int($postID);
		if(!$this->setPost($postID))
			return;

		$rsp = new Response();

		//get ID
		$profileID = Session::read("profileID");
        
        //Are we logged in ?
		if(!isAuthorized::isUser())
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			     ->send();
            
			return;
		}
        
        //Are we liking this post?
		if(!$this->likeModel->isLiked($postID, $profileID))
        {
			$rsp->setFailure(400, "post not liked")
                ->send();
            
            return;
        }
        
        $this->likeModel->unlike($postID, $profileID);
        
        $rsp->setSuccess(200, "post unliked")
            ->bindValue("postID", $postID)
            ->bindValue("profileID", $profileID)
            ->send();
	}

	/**
	 * Get all the likes of a post
	 * @param integer $postID Post to use
	 */
	public function likes($postID)
	{
		$postID = Sanitize::int($postID);
        
		if(!$this->setPost($postID))
			return;

		$rsp = new Response();

        //Are we authorized to see this profile
		if(!isAuthorized::seeFullProfile($this->model->getProfileID()))
        {
			$rsp->setFailure(401, "You can not see this post")
			    ->send();
            
			return;
		}

		$likes = $this->likeModel->getAllLikes($postID);

		$rsp->setSuccess(200, "likes returned")
            ->bindValue("postID", $postID)
		    ->bindValue("nbOfLikes", count($likes))	
            ->bindValue("like", $likes)
            ->send();
	}

	/************************************/
	/*********** COMMENTAIRES ***********/
	/************************************/
    
	/**
	 * Retrieve all comments on a post
	 * @param integer $postID Post to use
	 */
	public function comments($postID)
	{
		if(!$this->setPost($postID))
			return;

		$rsp = new Response();

        //Are we authorized to see this post ?
		if(!isAuthorized::seeFullProfile($this->model->getProfileID()))
        {
			$rsp->setFailure(401, "You can not see this post")
			    ->send();
            
			return;
		}

		$coms = $this->commentModel->getComments($postID);

		$rsp->setSuccess(200, "comments returned")
			->bindValue("postID", $postID)
            ->bindValue("nbOfComments", count($coms))
            ->bindValue("comments", $coms)
            ->send();
	}


	/************************************/
	/*********** TAGS *******************/
	/************************************/

	/**
	 * Research all the rags with this postID
	 * @param string $tagName Tag to use
	 */
	public function tags($postID)
	{		
		$rsp = new Response();

		if(!$this->setPost($postID))
			return;
		
		if(!isAuthorized::seeFullProfile($this->model->getProfileID()))
        {
			$rsp->setFailure(401, "You can not see this post")
			    ->send();
            
			return;
		}

		$tags = $this->tagModel->postTags($postID);

		if($tags == false)
        {
			$rsp->setFailure(404, "no tag for this post")
                ->send();
            
            return;
		}
        
        $rsp->setSuccess(200, "tags returned")
			->bindValue("postID", $postID)
			->bindValue("nbOfTag", count($tags))
			->bindValue("tags", $tags)
            ->send();
	}



	/************************************/
	/*********** VIEW *******************/
	/************************************/

	/**
	 * Add a view to this profile
	 * @param integer $postID Post to view
	 */
	public function view($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$rsp = new Response();

		$profileID = Session::read("profileID");
        
		if(!$this->postViewModel->view($profileID, $postID))
		{
			$rsp->setFailure(400, "post not set as viewed")
                ->send();
            
            return;
        }
			
        $rsp->setSuccess(200, "post viewed")
            ->send();
	}

	/**
	 * Return the number of times a profile has been viewed by a user
	 */
	public function nbView()
	{
		$resp = new Response();
        
		$rslt = $this->postViewModel->mostViewedPosts(10);
		
        $resp->setSuccess(200, "post viewed")
			 ->bindValue("rslt", $rslt);

		$resp->send();
	}
    
    
    /********* POPULAR ********/
    
    /**
     * Get popular posts
     * @param integer [$limit       = 30] Number of posts to return
     */
    public function popular($limit = 30)
    {
        $exclude = [];
        
        if(isset($_POST['exclude']))
        {
            $exclude = explode(",", $_POST['exclude']);
        }
        
        $postsBasics = $this->model->popular($exclude, $limit);
        
        $posts = array();

        foreach($postsBasics as $postBasics)
        {
            $postInfos = Response::read("post", "display", $postBasics['post_id']);
            
            //remove posts the user cannot see.
            if($postBasics ['profile_private'] == 1)
            {
                if(!isAuthorized::seeFullProfile($postBasics['profile_private']))
                {
                    continue;
                }
            }
            
            array_push($posts, $postInfos);
        }
        
        $rsp = new Response();

        $rsp->setSuccess(200)
            ->bindValue("posts", $posts)
            ->bindValue("nbrPosts", count($posts))
            ->send();
    }
}


