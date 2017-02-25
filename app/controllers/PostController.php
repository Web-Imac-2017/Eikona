<?php

class PostController
{
	private $model;
	private $likeModel;

	public function __construct()
	{
		$this->model = new PostModel();
		$this->likeModel = new LikeModel();
	}

	/*
	 * Create a post
	 *
	 */
	public function create()
	{

		$type = $_POST['postType'];
		$desc = isset($_POST['postDescription']) ? $_POST['postDescription'] : "";

		/*
		 * Management of the picture
		 * Management of the video is missing
		 */
		if(is_uploaded_file($_FILES['img']['tmp_name']))
		{
			$source = $_FILES['img']['tmp_name'];
			$format = getimagesize($source);
			$tab;

			if(preg_match('#(png|gif|jpeg)$#i', $format['mime'], $tab))
			{
				$imSource = imagecreatefromjpeg($source);
				if($tab[1] == "jpeg")
					$tab[1] = "jpg";
				$extension = $tab[1];
			}

			if($format['mime'] == "image/png")
			{
				$extension = 'jpg';
			}

			/* Call to the postModel and creation of the JSON response */
			$postID = $this->model->create($type, $extension, $desc);
			$rsp = new Response();

			if($postID)
			{
				/* Storing of the picture*/
				imagejpeg($imSource, 'medias/img/' . $postID . '.' . $extension);

				$rsp->setSuccess(201)
					->bindValue("ProfileID", $postID);
			} else {
				$rsp->setFailure(400);
			}
		}else{
			$rsp->setFailure(400, "file not uploaded");
		}

		$rsp->send();
	}

	private function setPost($postID)
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
		$this->model->setPost($postID);
		$deleted = $this->model->delete();

		$rsp = new Response();

		if($deleted)
		{
			unlink("medias/img/".$postID.".jpg");
        	$rsp->setSuccess(200)
				->bindValue("postId", $postID);
		} else {
			$rsp->setFailure(404);
		}

		$rsp->send();
	}

	/*
	 * Display all the information of the post with the given ID
	 */
	public function display($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$rsp = new Response();
		$rsp->setSuccess(200)
			->bindValue("postId", $postID)
			->bindValue("profileID", $this->model->getProfileID())
			->bindValue("desc", $this->model->getDescription())
			->bindValue("publishTime", $this->model->getPublishTime())
			->bindValue("allowComments", $this->model->getAllowComments())
			->bindValue("approved", $this->model->getApproved())
			->bindValue("getUpdateTime", $this->model->getUpdateTime())
			->bindValue("state", $this->model->getState())
			->bindValue("geo", $this->model->getGeo())
			->send();
	}

	/*
	 * Update a post, with the field to update given
	 * @param $field Field to update
	 * @param $postID ID of the post to be update
	 * Only for this fields : description, geo, allowComments, disableComments
	 */
	public function update($field, $postID)
	{
		$this->model->setPost($postID);

		$rsp = new Response();

		switch($field)
		{
			case "description" :
				if(isset($_POST['desc']))
				{
					$desc = $this->model->updateDescription($_POST['desc']);

					if($desc === false)
					{
						$rsp->setFailure(400);
					} else {
						$rsp->setSuccess(200)
							->bindValue("postDescription", $this->model->getDescription());
					}
				}
			break;

			case "geo" :
				if(isset($_POST['post_geo_lat']) && isset($_POST['post_geo_lng']) && isset($_POST['post_geo_name']) )
				{
					$lat = $this->model->updateLatitude($_POST['post_geo_lat']);
					$lng = $this->model->updateLongitude($_POST['post_geo_lng']);
					$name = $this->model->updateGeoName($_POST['post_geo_name']);

					if($lat === false || $lng === false || $name === false){
						$rsp->setFailure(400);
					} else {
						$rsp->setSuccess(200)
							->bindValue("postGeo", $this->model->getGeo());
					}
				}
			break;

			case "allowComments" :
				$allowComments = $this->model->allowComments();

				if($allowComments === false ){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("allowComments", $this->model->allowComments());
				}
			break;

			case "disableComments" :
				$disableComments = $this->model->disableComments();

				if($disableComments === false){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("disableComments", $this->model->disableComments());
				}
			break;

			case "postApproved" :
				$postApproved = $this->model->updatePostApproved();

				if($postApproved === false){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("postApproved", $this->model->updatePostApproved());
				}
			break;

			default;
				$rsp->setFailure(405);
		}

		$rsp->send();
	}

	/*
	 * Update the state of a post
	 */
	public function updateState($postID)
	{
		$this->model->setPost($postID);

		$newState = $this->model->updateState($_POST['state']);

		$rsp = new Response();

		if($newState === false){
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("state", $this->model->getState());
		}

		$rsp->send();
	}

	/*
	 * Get the geo of the post with the given ID
	 */
	public function getGeo($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$geo = $this->model->getGeo();
		$rsp = new Response();

		if($geo === false)
		{
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("postID", $postID)
				->bindValue("geo", $geo);
		}

		$rsp->send();
	}

	/*
	 * Get the description of the post with the given ID
	 */
	public function getDescription($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$desc = $this->model->getDescription();
		$rsp = new Response();

		if($desc === false)
		{
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("postID", $postID)
				->bindValue("desc", $desc);
		}

		$rsp->send();
	}

	/*
	 * Get the time the post was publish with the given ID
	 */
	public function getPublishTime($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$publishTime = $this->model->getPublishTime();
		$rsp = new Response();

		if($publishTime === false)
		{
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("postID", $postID)
				->bindValue("publishTime", $publishTime);
		}

		$rsp->send();
	}

	/*
	 * Get the state of the post with the given ID
	 * 1 if publish, 2 is moderation, not visible
	 */
	public function getState($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$state = $this->model->getState();
		$rsp = new Response();

		if($state === false)
		{
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("postID", $postID)
				->bindValue("state", $state);
		}

		$rsp->send();
	}

	/*
	 * Get if the comments are allowed of the post with the given ID
	 * 1 is allowed, 0 isn't allowed
	 */
	public function getAllowComments($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$allowComments = $this->model->getAllowComments();
		$rsp = new Response();

		if($allowComments === false)
		{
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("postID", $postID)
				->bindValue("allowComments", $allowComments);
		}

		$rsp->send();
	}

	/*
	 * Get if the post with the given ID is approved
	 * 1 is approved, 0 isn't approved yet
	 */
	public function getApproved($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$approved = $this->model->getApproved();
		$rsp = new Response();

		if($approved === false)
		{
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("postID", $postID)
				->bindValue("Approved", $approved);
		}

		$rsp->send();
	}

	/*
	 * Get the time the post with the given ID is update
	 */
	public function getUpdateTime($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$updateTime = $this->model->getUpdateTime();
		$rsp = new Response();

		if($updateTime === false)
		{
			$rsp->setFailure(400);
		} else {
			$rsp->setSuccess(200)
				->bindValue("postID", $postID)
				->bindValue("UpdateTime", $updateTime);
		}

		$rsp->send();
	}

	/************************************/
	/*************** LIKE ***************/
	/************************************/

	public function like($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$resp = new Response();

		//get ID
		$userID = Session::read("userID");

		if(!isAuthorized::isUser($userID)){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();

			return;
		}

		$profileID = 1;

		if($this->likeModel->like($postID, $profileID)){
			$resp->setSuccess(200, "post liked");
		}else{
			$resp->setFailure(400, "post is not liked");
		}
		$resp->send();

	}

	public function unlike($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$resp = new Response();

		$userID = Session::read("userID");

		if(!isAuthorized::isUser($userID)){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();

			return;
		}
			
		$profileID = 1;

		if($this->likeModel->unlike($postID, $profileID)){
			$resp->setSuccess(200, "post unliked");
		}else{
			$resp->setFailure(400, "post not unliked");
		}
		$resp->send();

	}

}
