<?php

class PostsController
{
	private $model;
	private $view;

	public function __construct()
	{
		$this->model = new PostsModel();
	}

	/*
	 * Création d'un post
	 *
	 */
	public function create()
	{
		$type = $_POST['postType'];
		$desc = isset($_POST['postDescription']) ? $_POST['postDescription'] : "";
		$time = $_POST['postTime'];

		/*
		 * Gestion de l'image
		 * Manque la gestion de la vidéo à faire
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

			/*appel du model*/
			$postID = $this->model->create($type, $extension, $desc, $time);
			
			/*enregistrement de l'image*/
			imagejpeg($imSource, 'medias/img/' . $postID . '.' . $extension);
		}
	}

	/*
	 * Suppression d'un post
	 *
	 */
	public function delete($postID)
	{
		unlink("medias/img/".$postID.".jpg");
		$this->model->setPost($postID);
		$this->model->delete();
	}

	public function updateDescription()
	{
		$newDesc = $_POST['desc'];
		$this->model->updateDescrption($newDesc);
	}

	public function updateState()
	{
		$newState = $_POST['state'];
		$this->model->updateState($newState);
	}

	public function display($postID)
	{

	}

	public function getGeo($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$geo = $this->model->getGeo();

		$rsp = new Response();
		$rsp->setSuccess(200)
			->bindValue("postID", $postID)
			->bindValue("geo", $geo)
			->send();
	}

	public function getDescription($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$desc = $this->model->getDescription();

		$rsp = new Response();
		$rsp->setSuccess(200)
			->bindValue("postID", $postID)
			->bindValue("desc", $desc)
			->send();
	}

	public function getPublishTime($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$publishTime = $this->model->getPublishTime();

		$rsp = new Response();
		$rsp->setSuccess(200)
			->bindValue("postID", $postID)
			->bindValue("publishTime", $publishTime)
			->send();
	}

	public function getState($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$state = $this->model->getState();

		$rsp = new Response();
		$rsp->setSuccess(200)
			->bindValue("postID", $postID)
			->bindValue("state", $state)
			->send();
	}
	
	public function getAllowComments($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$allowComments = $this->model->getAllowComments();

		$rsp = new Response();
		$rsp->setSuccess(200)
			->bindValue("postID", $postID)
			->bindValue("allowComments", $allowComments)
			->send();
	}

	public function getApproved($postID)
	{
		if(!$this->setPost($postID))
		{
			return;
		}

		$approved = $this->model->getApproved();

		$rsp = new Response();
		$rsp->setSuccess(200)
			->bindValue("postID", $postID)
			->bindValue("approved", $approved)
			->send();
	}



}
















