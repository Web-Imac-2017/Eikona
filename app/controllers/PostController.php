<?php

class PostController
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

	/*
	 * Create a post
	 *
	 */
	public function create()
	{

		$rsp = new Response();

		$userID = Session::read("userID");
		$profileID = Session::read("profileID");


		if(!isAuthorized::isUser($userID)){
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

		if(!$profileID){
			$rsp->setFailure(401, "You don't have current profile selected")
			    ->send();
			return;
        }		
        
		if(empty($_FILES['img'])){
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

        //prevent format is wrong
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
        while (list(, $tag) = each($tags[1])) {
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

	public function setPost($postID)
	{
		$result = $this->model->setPost($postID);

		if($result != "success")
		{
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

		return true;
	}

	/*
	 * Delete a post and the image with it
	 *
	*/
	public function delete($postID)
	{
		$rsp = new Response();

		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		if(!$this->setPost($postID))
			return;

		if(!$profileID){
			$rsp->setFailure(401, "You do not have current profile selected")
			    ->send();
			return;
		}

		if(!isAuthorized::editPost($postID)){
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

	/*
	 * Display all the information of the post with the given ID
	 */
	public function display($postID, $silence = true)
	{
		if(!$this->setPost($postID))
			return;

		$rsp = new Response();

		if($silence){
			if(!isAuthorized::seeFullProfile($this->model->getProfileID())){
				$rsp->setFailure(401, "You can not see this post")
				    ->send();
				return;
			}
		}

		$data = $this->model->getFullPost();

        $images = $this->getImages($postID);
		
		$rsp->setSuccess(200, "get all post informations")
			->bindValue("postID", $postID)
			->bindValue("profileID", $data['profile_id'])
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

		if(!$profileID){
			$rsp->setFailure(401, "You do not have current profile selected")
			    ->send();
			return;
		}

		if(!isAuthorized::editPost($postID)){
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

		switch($field)
		{
			case "description" :
				if(!empty($_POST['desc']))
				{
					$desc = $this->model->updateDescription($_POST['desc']);

					if($desc === false)
					{
						$rsp->setFailure(400);
					} else {
						$rsp->setSuccess(200)
							->bindValue("postID", $postID)
							->bindValue("postDescription", $_POST['desc']);
					}
				} else {
					$rsp->setFailure(400, "Missing value. Edit aborted.");
				}
			break;

			case "geo" :
				if(!empty($_POST['post_geo_lat']) && !empty($_POST['post_geo_lng']) && !empty($_POST['post_geo_name']) )
				{
					$lat = $this->model->updateLatitude($_POST['post_geo_lat']);
					$lng = $this->model->updateLongitude($_POST['post_geo_lng']);
					$name = $this->model->updateGeoName($_POST['post_geo_name']);

					if($lat === false || $lng === false || $name === false){
						$rsp->setFailure(400);
					} else {
						$rsp->setSuccess(200)
							->bindValue("postID", $postID)
							->bindValue("postGeo", ["lat" => $lat,
								                    "lng" => $lng,
								                    "name" => $name
								                   ]);
					}
				} else {
					$rsp->setFailure(400, "Missing value. Edit aborted.");
				}
			break;

			case "allowComments" :
				$allowComments = $this->model->allowComments();

				if($allowComments === false ){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("postID", $postID)
						->bindValue("allowComments", $allowComments);
				}
			break;

			case "disableComments" :
				$disableComments = $this->model->disableComments();

				if($disableComments === false){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("postID", $postID)
						->bindValue("disableComments", $disableComments);
				}
			break;

			case "postApproved" :
				$postApproved = $this->model->updatePostApproved();

				if($postApproved === false){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("postID", $postID)
						->bindValue("postApproved", $postApproved);
				}
			break;

			case "state":
				if(isAuthorized::isModerator($userID) || isAuthorized::isAdmin($userID)){
					if(!empty($_POST['state'])){
						if($_POST['state'] == 1 || $_POST['state'] == 2){
							$newState = $this->model->updateState($_POST['state']);

							if($newState === false){
								$rsp->setFailure(400, "error during request");
							}else{
								$rsp->setSuccess(200)
									->bindValue("postID", $postID)
									->bindValue("state", $newState);
							}
						}else{
							$rsp->setFailure(400, "Wrong value for state");
						}
					}else{
						$rsp->setFailure(400, "Edit aborted. Missing value.");
					}
				}else{
					$rsp->setFailure(401, "You are not authorized to do this action.");
				}
			break;

			default;
				$rsp->setFailure(405);
		}

		$code = $rsp->getCode();
		if($code >= 200 && $code <= 210){
			$date = $this->model->updateTime($postID);
			$rsp->bindValue("updateTime", $date);
		}

		$rsp->send();
	}

    /**
     * Publish the given post
     * @param integer $postID POst to publish;
     */
    public function publish($postID)
    {
        $rsp = new Response();

		if(!isAuthorized::editPost($postID))
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

		if(!$this->setPost($postID))
			return;

        $images = $this->getImages($postID);

        if($this->model->publish() === false)
        {
            $rsp->setFailure(400, "error during request")
                ->send();
        }

        //clean up pictures
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

		if(!isAuthorized::editPost($postID))
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
			    ->send();
			return;
		}

        if(!in_array($filter, FiltR::$availableFilters))
        {
			$rsp->setFailure(400, "This filter does not exists")
			    ->send();
			return;
		}

		if(!$this->setPost($postID))
			return;

        if($this->model->getState() != 0)
        {
			$rsp->setFailure(401, "You cannot edit the picture of an already published post.")
			    ->send();
			return;
		}

        $folder = $this->model->getSaveFolder();

        $currentFilter = $this->model->getFilter();

        if($currentFilter === $filter)
        {
            $rsp->setSuccess(200, "Filter unchanged")
                ->bindValue("postID", $postID)
                ->bindValue("currentFilter", $filter)
                ->bindValue("postPicture", $folder.$postID."-".$filter.".jpg")
                ->send();

            return;
        }

        //Remove old one and create copy with filter applied
        if(file_exists($folder.$postID."-".$currentFilter.".jpg"))
        {
            unlink($folder.$postID."-".$currentFilter.".jpg");
        }

        FiltR::$filter($folder.$postID.".jpg", $folder.$postID."-".$filter.".jpg");

        $this->model->updateFilter($filter);

        $rsp->setSuccess(200)
            ->bindValue("postID", $postID)
            ->bindValue("currentFilter", $filter)
            ->bindValue("editedPicture", $folder.$postID."-".$filter.".jpg")
            ->send();

    }


	/************************************/
	/*************** LIKE ***************/
	/************************************/

	public function like($postID)
	{
		$postID = Sanitize::int($postID);
		if(!$this->setPost($postID))
			return;

		$resp = new Response();

		//get ID
		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		if(!isAuthorized::isUser($userID)){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();
			return;
		}

		if(!$profileID){
			$rsp->setFailure(401, "You don't have current profile selected")
			    ->send();
			return;
		}

		if ($this->likeModel->countLikeFromLastHour($profileID) > 200) {
			$rsp->setFailure(406, "You have already liked 200 posts during the last 60 minutes, Calm down Billy Boy !")
			    ->send();
			return;
		}

		//Si le post n'est pas encore like
		if(!$this->likeModel->isLiked($postID, $profileID)){
			//Si ce n'est pas son propre post
			if($this->model->getProfileID() != $profileID){
				if(isAuthorized::seeFullProfile($this->model->getProfileID())){
					$this->likeModel->like($postID, $profileID);
					$resp->setSuccess(200, "post liked")
				    	 ->bindValue("postID", $postID)
				     	 ->bindValue("profileID", $profileID);
					$notif = Response::read("notification", "create", "newLike", $profileID, $this->model->getProfileID(), $postID);
					if($notif['code'] == 200){
						$resp->setSuccess(200, "post liked and notification sent")
				    	     ->bindValue("postID", $postID)
				     	 	 ->bindValue("profileID", $profileID)
				     	 	 ->bindValue("notif", $notif['data']);
					}
					else{
						$resp->setFailure(409, "post not liked and notification not sent");
					}					
				}else{
					$resp->setFailure(401, "You can not see this post");
				}						
			}else{
				$resp->setFailure(400, "You can not like your own post");
			}
		}else{
			$resp->setFailure(400, "post already liked");
		}


		$resp->send();

	}

	public function unlike($postID)
	{
		$postID = Sanitize::int($postID);
		if(!$this->setPost($postID))
			return;

		$resp = new Response();

		//get ID
		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		if(!isAuthorized::isUser($userID)){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();
			return;
		}

		if($this->likeModel->isLiked($postID, $profileID)){
			$this->likeModel->unlike($postID, $profileID);
			$resp->setSuccess(200, "post unliked")
			     ->bindValue("postID", $postID)
			     ->bindValue("profileID", $profileID);
		}else{
			$resp->setFailure(400, "post not liked");
		}

		$resp->send();
	}

	public function likes($postID)
	{
		$postID = Sanitize::int($postID);
		if(!$this->setPost($postID))
			return;

		$resp = new Response();

		if(!isAuthorized::seeFullProfile($this->model->getProfileID())){
			$resp->setFailure(401, "You can not see this post")
			    ->send();
			return;
		}

		$likes = $this->likeModel->getAllLikes($postID);

		$resp->setSuccess(200, "likes returned")
		     ->bindValue("postID", $postID)
		     ->bindValue("nbOfLikes", count($likes))	
		     ->bindValue("like", $likes)
		     ->send();
	}

	/************************************/
	/*********** COMMENTAIRES ***********/
	/************************************/

	public function comments($postID)
	{
		if(!$this->setPost($postID))
			return;

		$resp = new Response();

		if(!isAuthorized::seeFullProfile($this->model->getProfileID())){
			$rsp->setFailure(401, "You can not see this post")
			    ->send();
			return;
		}

		$coms = $this->commentModel->getComments($postID);

		$resp->setSuccess(200, "comments returned")
			 ->bindValue("postID", $postID)
			 ->bindValue("nbOfComments", count($coms))
			 ->bindValue("comments", $coms)
		     ->send();
	}


	/************************************/
	/*********** TAGS *******************/
	/************************************/

	/*
	 * Research all the posts with this tagName
	 */
	public function tag($tagName)
	{
		//Get all the post where tag_name = $tagName;
		
		$rsp = new Response();
		
		if(!isAuthorized::seeFullProfile($this->model->getProfileID())){
			$rsp->setFailure(401, "You can not see this post")
			    ->send();
			return;
		}

		$tags = $this->tagModel->tag($tagName);

		if($tags == false){
			$rsp->setFailure(404);
		} else {
			$rsp->setSuccess(200, "tags returned")
				->bindValue("tagName", $tagName)
				->bindValue("nbOfTag", count($tags))
				->bindValue("tags", $tags);
		}

		$rsp->send();
	}



	/************************************/
	/*********** VIEW *******************/
	/************************************/

	public function view($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$resp = new Response();

		$profileID = Session::read("profileID");
        
        echo $profileID;

		if($this->postViewModel->view($profileID, $postID))
		{
			$resp->setSuccess(200, "post viewed");
		}
		else
		{
			$resp->setFailure(400, "post not set as viewed");
		}
		$resp->send();
	}

	public function nbView()
	{
		$resp = new Response();
		$rslt = $this->postViewModel->mostViewedPosts(10);
		$resp->setSuccess(200, "post viewed")
			 ->bindValue("rslt", $rslt);

		$resp->send();
	}
    
    
    /********* POPULAR ********/
    
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


