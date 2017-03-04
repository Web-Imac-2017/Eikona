<?php

class CommentController
{
	private $model;
	private $likeModel;
	private $postModel;

	public function __construct()
	{
		$this->model     = new CommentModel();
		$this->likeModel = new CommentLikeModel();
		$this->postModel = new PostModel();
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
	
	
		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		$resp = new Response();

		//Si c'est bien un post
		if(isAuthorized::isPost($postID)){

			$this->postModel->setPost($postID);

			//Si les commentaires sont autorisés
			if($this->postModel->getAllowComments()){
				//Si le commentaire existe bien
				if(!empty($_POST['commentText'])){
					//Si l'user est connecté
					if(isAuthorized::isUser($userID)){
						//Si l'utilisateur à un profil d'actif
						if($profileID){
							if(isAuthorized::seeFullProfile($this->postModel->getProfileID())){
								$this->model->create($profileID, $postID, $_POST['commentText']);
								$resp->setSuccess(200, "Comment posted")
								     ->bindValue("userID", $userID)
								     ->bindValue("profileID", $profileID)
								     ->bindValue("postID", $postID)
								     ->bindValue("comment", $_POST['commentText']);
							}else{
								$resp->setFailure(401, "You can not comment this post");
							}			
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
				$resp->setFailure(400, "Comments are disabled for this post");
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
		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		if(!$this->setComment($commentID))
			return;

		$resp = new Response();
			
		//si l'user est connecté
		if(isAuthorized::isUser($userID)){
			//s'il a un profil courant
			if($profileID){
				//Si le profil a bien été commenté par le profil courant
				if($this->model->getProfileID() == $profileID){
					$this->model->delete($commentID);
					$resp->setSuccess(200, "comment deleted")
						 ->bindValue("commentID", $commentID);
				}else{
					$resp->setFailure(401, "You can not delete this comment. Not yours.");
				}
			}else{
				$resp->setFailure(401, "You don't have current profile selected");
			}
		}else{
			$resp->setFailure(401, "You are not authorized to do this action.");
		}

		$resp->send();
	}

	/*
	 * Like d'un commentaire
	 *
	 */
	public function like($commentID)
	{
		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		if(!$this->setComment($commentID))
			return;

		$resp = new Response();

		// TODO : LIKE QUE SI POST NE PROVIENT PAS D'UN PROFIL PRIVE

		//Si l'user est connecté
		if(isAuthorized::isUser($userID)){
			//S'il a un profil courant
			if($profileID){
				//S'il n'a pas encore aimé le post
				if(!$this->likeModel->isLiked($profileID, $commentID)){
					//Si ce n'est pas son propre comment
					if($this->model->getProfileID() != $profileID){
						if(isAuthorized::seeFullProfile($this->postModel->getProfileID())){
							$this->likeModel->like($profileID, $commentID);
							$resp->setSuccess(201, "comment liked")
						    	 ->bindValue("commentID", $commentID)
						     	 ->bindValue("profileID", $profileID);
						}else{
							$resp->setFailure(401, "You can not like this comment");
						}	
					}else{
						$resp->setFailure(400, "You can not like your own comment");
				   	}	
				}else{
					$resp->setFailure(400, "comment already liked");
				}				
			}else{
				$resp->setFailure(401, "You don't have current profile selected");
			}
		}else{
			$resp->setFailure(401, "You are not authorized to do this action.");
		}	

		$resp->send();

	}

	/*
	 * Unlike d'un commentaire
	 *
	 */
	public function unlike($commentID)
	{
		$userID = Session::read("userID");
		$profileID = Session::read("profileID");

		if(!$this->setComment($commentID))
			return;

		$resp = new Response();

		if(isAuthorized::isUser($userID)){
			if($this->likeModel->isLiked($profileID, $commentID)){
				$this->likeModel->unlike($profileID, $commentID);
				$resp->setSuccess(200, "comment unliked")
			         ->bindValue("commentID", $commentID)
			         ->bindValue("profileID", $profileID);
			}else{
				$resp->setFailure(400, "post not liked");
			}
		}else{
			$resp->setFailure(401, "You are not authorized to do this action.");
		}

		$resp->send();
	}

	public function likes($commentID)
	{
		if(!$this->setComment($commentID))
			return;
			
		$resp = new Response();

		if(!isAuthorized::seeFullProfile($this->model->getProfileID())){
			$rsp->setFailure(401, "You can not see likes on this comment");
			    ->send();
			return;
		}

		$likes = $this->likeModel->getLikes($commentID);

		$resp->setSuccess(200, "likes comment returnded")
		     ->bindValue("commentID", $commentID)
		     ->bindValue("nbOfLikes", count($likes))
		     ->bindValue("likes", $likes)
		     ->send();	
	}

}