<?php

class CommentController
{
	private $model;
	private $likeModel;

	public function __construct()
	{
		$this->model = new CommentModel();
		$this->likeModel = new CommentLikeModel();
	}



	private function setComment($commentID)
	{
		$result = $this->model->setComment($commentID);

		if($result != "success")
		{
			$rsp = new Response();

			if($result == "wrongFormat")
			{
				$rsp->setFailure(400, "Wrong format. This is not a comment ID.");
			}
			else if($result == "notFound")
			{
				$rsp->setFailure(404, "Given comment ID does not exist.");
			}

			$rsp->send();

			return false;
		}

		return true;
	}

	/*
	 * Création d'un post
	 *
	 */
	public function create($postID)
	{
		/*
		Commenter si seulement on est connecté
		Commenter si seulement on a un profil actif
		Commenter si seulement il y a du texte		
		Commenter si seulement postID renvoie à un post


		Commenter si seulement le post ne provient pas d'un profil privé
		Commenter si seulement les commentaires sont autorisés
		 */

		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		$resp = new Response();

		if(isAuthorized::isPost($postID)){
			if(!empty($_POST['commentText'])){
				if(isAuthorized::isUser($userID)){
					if($profileID){
						$this->model->create($profileID, $postID, $_POST['commentText']);
						$resp->setSuccess(200, "Comment posted")
						     ->bindValue("userID", $userID)
						     ->bindValue("profileID", $profileID)
						     ->bindValue("postID", $postID)
						     ->bindValue("comment", $_POST['commentText']);					
					}else{
						$resp->setFailure(401, "You don't have current profile selected");
					}
				}else{
					$resp->setFailure(401, "You are not authorized to do this action.");
				}
			}else{
				$resp->setFailure(400, "Missing value. Edit aborted.");
			}
		}else{
			$resp->setFailure(404, "Given comment ID does not exist.");
		}		

		$resp->send();
	}

	/*
	 * Suppression d'un commentaire
	 *
	 */
	public function delete($commentID)
	{
		$this->model->setPost($commentID);
		$this->model->delete();
	}

	/*
	 * Like d'un commentaire
	 *
	 */
	public function like($commentID)
	{
		$rsp = new Response();

		$profile = Session::read("profileID");
		
		if(!$profile)
		{
			$rsp->setFailure(401, "You must be connected to be able to like.")
				->send();

			return;
		}
		
		$rslt = $this->likeModel->like($profile, $commentID);

		if(!$rslt)
		{
			$rsp->setFailure(400, "Wrong given parameters")
				->send();

			return;
		}

		$rsp->setSuccess(201)
			->send();
	}

	/*
	 * Unlike d'un commentaire
	 *
	 */
	public function unlike($commentID)
	{
		$rsp = new Response();

		$profile = Session::read("profileID");
		
		if(!$profile)
		{
			$rsp->setFailure(401, "You must be connected to be able to unlike.")
				->send();

			return;
		}

		if(!$this->isLiking($profile, $commentID))
		{
			$rsp->setFailure(400, "You don't like this comment")
				->send();

			return;
		}

		$rslt = $this->likeModel->unlike($profile, $commentID);
		
		if(!$rslt)
		{
			$rsp->setFailure(400, "Wrong given parameters")
				->send();

			return;
		}

		$rsp->setSuccess(201)
			->send();
	}

	/*
	 * renvoie le nombre de likes d'un commentaire
	 *
	 */
	public function getLikes($commentID)
	{
		$nbLikes = $this->likeModel->getLikes($commentID);

		return $nbLikes;
	}

	/*
	 * renvoie true si le commentaire est déja aimé par le profil, false sinon
	 *
	 */
	public function isLiking($profileID, $commentID)
	{
		$isLiking = $this->likeModel->isLiking($profileID, $commentID);

		return $isLiking;
	}
}