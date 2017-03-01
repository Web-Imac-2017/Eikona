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
	
		//TODO ==> VERIFIER SI LE POST PROVIENT D'UN PROFIL PRIVE OU PUBLIC
		//         SI PROFIL PUBLIC -> COMMENTAIRES ALLOWED SAUF S'ILS SONT DESACTIVES
		//         SI PROFIL PRIVE  -> IL FAUT LE FOLLOW POUR COMMENTER / LIKE 
		//                             VERIFIER SI LES COMMENTAIRES SONT ACTIVES
		//         
		//         VERIFICATION COMMENTS ALLOWED EN PREMIER
	
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
				if(!$this->likeModel->isLiked($commentID, $profileID)){
					//Si ce n'est pas son propre comment
					if($this->model->getProfileID() != $profileID){
						$this->likeModel->like($profileID, $commentID);
						$resp->setSuccess(201, "comment liked")
					    	 ->bindValue("commentID", $commentID)
					     	 ->bindValue("profileID", $profileID);
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

	}
}