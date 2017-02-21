<?php

class UserController{
	
	private $model;


	public function __construct()
	{
		$this->model = new UserModel();
	}

	private function setUser($userID)
	{
		$result = $this->model->setUser($userID);

		if($result != "success"){

			$resp = new Response();

			if($result == "wrongFormat"){
				$resp->setFailure(400, "Wrong format. This is not a user ID");
			}else if($result == "notFound"){
				$resp->setFailure(404, "Given user ID does not exist");
			}
			$resp->send();

			return false;
		}

		return true;
	}

	public function edit($field, $userID)
	{
		$resp = new Response();

		if(!isAuthorized::isUser()){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();

			return;
		}


		if(!$this->setUser($userID)){
			return;
		}

		switch($field)
		{
			case "name":

				if(!empty($_POST['name'])){
					if($this->model->updateName($_POST['name'])){
						$resp->setSuccess(200, "name changed")
						     ->bindValue("userName", $this->model->getName());
					}
				}else{
					$resp->setFailure(400, "Missing value. Edit aborted.");
				}
			break;

			default:
				$resp->setFailure(405);
		}

		$resp->bindValue("userID", $userID)
		     ->send();

	}

}