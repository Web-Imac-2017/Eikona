<?php

class UserController{
	
	private $model;


	public function __construct()
	{
		$this->model = new UserModel();
	}

	/**
     * Set the user to use with the model
     * @param  int $userID User ID to use with the model
     * @return boolean  true on success, false on failure
     */
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

	/**
	 * Return all elements of an user
	 * @return Response JSON
	 */
	public function get()
	{
		$userID = Session::read("userID");

		if(!$this->setUser($userID)){
			return;
		}

		$userInfos = $this->model->getFullUser();

		//Send JSON response
		$resp = new Response();
		$resp->setSuccess(200, "all elements returned")
		     ->bindValue("userID", $userID)
		     ->bindValue("userName", $userInfos['user_name'])	
		     ->bindValue("userEmail", $userInfos['user_email'])
		     ->bindValue("userRegisterTime", $userInfos['user_register_time'])
		     ->bindValue("userLastActivity", $userInfos['user_last_activity'])
		     ->bindValue("userModerator", $userInfos['user_moderator'])
		     ->bindValue("userAdmin", $userInfos['user_admin'])
		     ->bindValue("userActivated", $userInfos['user_activated'])
		     ->send();
	}

	/**
	 * Update the specified element of the user
	 * @param  text $field Field to be updated
	 * @return Response        JSON
	 */
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
					if($this->model->isUnique($_POST['email'])){
						if($this->model->updateEmail($_POST['email'])){
							$resp->setSuccess(200, "email changed")
						         ->bindValue("userID", $userID)
							     ->bindValue("userEmail", $this->model->getEmail());
						}else{
							$resp->setFailure(409, "incorrect email");
						}					
					}else{						
						$resp->setFailure(403, "user already exists");
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