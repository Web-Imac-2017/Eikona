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

	public function edit($field)
	{
		$resp = new Response();

		//get userID
		$userID = Session::read("userID");

		// If userID is wrong		
		if(!$this->setUser($userID)){
			return;
		}

		//User is authorized ?
		if(!isAuthorized::isUser($userID)){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();

			return;
		}

		switch($field)
		{
			/*---------- USER_NAME ----------*/
			case "name":

				if(!empty($_POST['name'])){
					if($this->model->updateName($_POST['name'])){
						$resp->setSuccess(200, "name changed")
							 ->bindValue("userID", $userID)
						     ->bindValue("userName", $this->model->getName());
					}else{
						$resp->setFailure(409, "incorrect value");
					}
				}else{
					$resp->setFailure(400, "Missing value. Edit aborted.");
				}
				break;

			/*---------- USER_EMAIL ----------*/
			case "email":

				if(!empty($_POST['email'])){
					if($this->model->updateEmail($_POST['email'])){
						$resp->setSuccess(200, "email changed")
						     ->bindValue("userID", $userID)
							 ->bindValue("userEmail", $this->model->getEmail());
					}else{
						$resp->setFailure(409, "incorrect email");
					}
				}
				else{
					$resp->setFailure(400, "Missing value. Edit aborted.");
				}
				break;

			/*---------- USER_PASSWORD ----------*/
			case "password":

				if(!empty($_POST['passwd']) && !empty($_POST['passwd_confirm'])){
					if($_POST['passwd'] == $_POST['passwd_confirm']){
						if($this->model->updatePassword($_POST['passwd'])){
							$resp->setSuccess(200, "password changed");
							//Pour la sécurité, pas de bind value du passwd
						}else{
							$resp->setFailure(409, "incorrect password");
						}
					}else{
						$resp->setFailure(409, "password and confirmation do not correspond");
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