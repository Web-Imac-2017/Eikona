<?php

class CommentController
{
	private $model;
	private $view;

	public function __construct()
	{
		$this->model = new CommentModel();
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
	 * Suppression d'un post
	 *
	 */
	public function delete($commentID)
	{
		$this->model->setPost($commentID);
		$this->model->delete();
	}

}