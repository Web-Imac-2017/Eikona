<?php

class LikeController{

	private $model;


	public function __construct()
	{
		$this->model = new LikeModel();
	}


	// do/like/add/<postID>
	public function add($id)
	{
		$resp = new Response();

		//get userID
		$userID = Session::read("userID");

		//User is authorized
		if(!isAuthorize::isUser($userID)){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();

			return;
		}

		if(!empty($_GET['id'])){
			if($this->model->)
		}		
		
	}

}