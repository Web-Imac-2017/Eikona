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
			$postId = $this->model->create($type, $extension, $desc, $time);
			
			/*enregistrement de l'image*/
			imagejpeg($imSource, 'medias/img/' . $postId . '.' . $extension);
		}
	}

	/*
	 * Suppression d'un post
	 *
	 */
	public function delete($postID)
	{
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

	public function display($profileId)
	{

	}

	public function getGeo()
	{
		$geo = $this->model->getGeo();
	}

	public function getDescription()
	{
		$desc = $this->model->getDescription();
	}

	public function getPublishTime()
	{
		$publishTime = $this->model->getPublishTime();
	}

	public function getState()
	{
		$state = $this->model->getState();
	}
	
	public function getAllowComments()
	{
		$allowComments = $this->model->getAllowComments();
	}

	public function getApproved()
	{
		$approved = $this->model->getApproved();
	}



}
















