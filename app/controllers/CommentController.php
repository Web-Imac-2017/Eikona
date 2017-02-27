<?php

class CommentController
{
	private $model;
	private $likeModel;
	private $view;

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
	public function create($profile, $post)
	{
		$txt = $_POST['commentText'];
		$time = $_POST['commentTime'];

		$this->model->create($profile, $post, $txt, $time);
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
			/*$rsp->setFailure(401, "You must be connected to be able to like.")
				->send();

			return;*/
			$profile = 1;
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
	public function unlike($comment)
	{
		$rsp = new Response();

		$profile = Session::read("profileID");
		
		if(!$profile)
		{
			$rsp->setFailure(401, "You must be connected to be able to like.")
				->send();

			return;
		}

		if(isLiking($profile, $comment) == false)
		{
			$rsp->setFailure(400, "You have already liked this comment")
				->send();

			return;
		}

		$this->likeModel->unlike($profile, $comment);
		$rsp->setSuccess(201)
			->send();
	}

	/*
	 * renvoie le nombre de likes d'un commentaire
	 *
	 */
	public function getLikes($comment)
	{
		$nbLikes = $this->likeModel->getLikes($comment);

		return $nbLikes;
	}

	public function isLiking($profile, $comment)
	{
		$isLiking = $this->likeModel->isLiking($profile, $comment);

		return $isLiking;
	}
}