<?php

class PostController
{
	private $model;
	private $likeModel;
	private $commentModel;


	public function __construct()
	{
		$this->model        = new PostModel();
		$this->likeModel    = new LikeModel();
		$this->commentModel = new CommentModel();
	}

	private function createFolder($userID, $profileID)
	{
		$root = $_SERVER['DOCUMENT_ROOT']."/Eikona/app/medias/img/";

		if(!is_dir($root.$userID)){
			mkdir($root.$userID);
		}

		if(!is_dir($root.$userID."/".$profileID)){
			mkdir($root.$userID."/".$profileID);
		}
	}

	private function uploadImg($extension, $source, $savePath)
	{
		$quality = 100;

		switch($extension){

			case "jpeg":
			case "jpg":
				$imgSource = imagecreatefromjpeg($source);
				imagejpeg($imgSource, $savePath, $quality);
				break;

			case "png":
				$image = imagecreatefrompng($source);
				$bg = imagecreatetruecolor(imagesx($image), imagesy($image));
				imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
				imagealphablending($bg, TRUE);
				imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
				imagedestroy($image);
				imagejpeg($bg, $savePath, $quality);
				imagedestroy($bg);
				break;
		}
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

		$validFormat = array("jpg", "jpeg", "png");

		/*
		 * Management of the picture
		 * Management of the video is missing
		 */
		if(is_uploaded_file($_FILES['img']['tmp_name']))
		{
			
			$desc = !empty($_POST['postDescription']) ? $_POST['postDescription'] : "";
			$comments = Sanitize::booleanToInt(isset($_POST['disableComments']) ? false : true);
			$source = $_FILES['img']['tmp_name'];

			$format = getimagesize($source);

			//prevent format is wrong
			if(!$format){
				$rsp->setFailure(400, "File do not have good extension")
					->send();
				return;
			}

			$extension = explode("/", $format['mime'])[1];			

			//Création des dossiers
			$this->createFolder($userID, $profileID);
			

			//detect if extension is allowed
			if(in_array($extension, $validFormat))
			{
				$type = "image";
				$postID = $this->model->create($type, "jpg", $desc, $comments);

				$root = $_SERVER['DOCUMENT_ROOT']."/Eikona/app/medias/img/";
				$savePath = $root.$userID."/".$profileID."/".$postID.".jpg";

				if(!$postID){
					$rsp->setFailure(400, "echec lors de l'upload")
					    ->send();
					return;
				}				

				$this->uploadImg($extension, $source, $savePath);				

				$rsp->setSuccess(201, "post created")
					->bindValue("userID", $userID)
					->bindValue("profileID", $profileID)
					->bindValue("postID", $postID);		
			}else{
				$rsp->setFailure(400, "File do not have good extension");
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

		if($this->model->delete())
		{
			//Suppression sans connaitre l'extension
			$root = $_SERVER['DOCUMENT_ROOT']."/Eikona/app/medias/img/";
			$pattern = $root.$userID."/".$profileID."/".$postID.".*";
			array_map("unlink", glob($pattern));

	       	$rsp->setSuccess(200, "post deleted")
				->bindValue("postId", $postID);
		} else {
			$rsp->setFailure(404, "post not deleted");
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
		$rsp->setSuccess(200, "get all post informations")
			->bindValue("postID", $postID)
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
							->bindValue("postGeo", $this->model->getGeo());
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
						->bindValue("allowComments", $this->model->allowComments());
				}
			break;

			case "disableComments" :
				$disableComments = $this->model->disableComments();

				if($disableComments === false){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("postID", $postID)
						->bindValue("disableComments", $this->model->disableComments());
				}
			break;

			case "postApproved" :
				$postApproved = $this->model->updatePostApproved();

				if($postApproved === false){
					$rsp->setFailure(400);
				} else {
					$rsp->setSuccess(200)
						->bindValue("postID", $postID)
						->bindValue("postApproved", $this->model->updatePostApproved());
				}
			break;

			case "state":
				if(isAuthorized::isModerator($userID)['data']['isModerator'] == true ||
				   isAuthorized::isAdmin($userID)['data']['isAdmin'] == true){
					if(!empty($_POST['state'])){
						if($_POST['state'] == 1 || $_POST['state'] == 2){
							$newState = $this->model->updateState($_POST['state']);

							if($newState === false){
								$rsp->setFailure(400);
							}else{
								$rsp->setSuccess(200)
									->bindValue("postID", $postID)
									->bindValue("state", $this->model->getState());
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
				->bindValue("postID", $postID)
				->bindValue("state", $this->model->getState());
		}

		$rsp->send();
	}

	/*****************************************************************************/
	/*
		LES METHODES QUI SUIVENT SONT DES GETTER ET N'ONT PAS LEIU D'ETRE DANS LE 
		CONTROLLER. POUR LES UTILISER DEPUIS UN AUTRE CONTROLLER, IL FAUT INCLURE 
		LE MODELE POST.
    */
	/*****************************************************************************/

	/*
	 * Get the geo of the post with the given ID
	 */
	/*public function geo($postID)
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
	}*/

	/*
	 * Get the description of the post with the given ID
	 */
	/*public function description($postID)
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
	}*/

	/*
	 * Get the time the post was publish with the given ID
	 */
	/*public function publishTime($postID)
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
	}*/

	/*
	 * Get the state of the post with the given ID
	 * 1 if publish, 2 is moderation, not visible
	 */
	/*public function state($postID)
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
*/
	/*
	 * Get if the comments are allowed of the post with the given ID
	 * 1 is allowed, 0 isn't allowed
	 */
	/*public function allowComments($postID)
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
	}*/

	/*
	 * Get if the post with the given ID is approved
	 * 1 is approved, 0 isn't approved yet
	 */
	/*public function approved($postID)
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
	}*/

	/*
	 * Get the time the post with the given ID is update
	 */
	public function updateTime($postID)
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

		// TODO ==> VERIFIER SI LE PROFIL EST PUBLIC / PRIVE 
		//                                    FOLLOWED / PAS FOLLOWED
		//        VOIR COMMENTCONTROLLER

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

		//Si le post n'est pas encore like
		if(!$this->likeModel->isLiked($postID, $profileID)){
			//Si ce n'est pas son propre post
			if($this->model->getProfileID() != $profileID){
				//TODO => FOLLOW OU PAS FOLLOW
				$this->likeModel->like($postID, $profileID);
				$resp->setSuccess(200, "post liked")
				     ->bindValue("postID", $postID)
				     ->bindValue("profileID", $profileID);			
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
		if(!$this->setPost($postID))
			return;

		$resp = new Response();

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

		$coms = $this->commentModel->getComments($postID);

		$resp->setSuccess(200, "comments returned")
			 ->bindValue("postID", $postID)
			 ->bindValue("nbOfComments", count($coms))
			 ->bindValue("comments", $coms)
		     ->send();
	}


}
