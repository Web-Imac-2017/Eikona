<?php

class UserController{

	private $model;
	private $profileModel;
	private $authModel;

	public function __construct()
	{
		$this->model = new UserModel();
		$this->profileModel = new ProfileModel();
		$this->authModel = new AuthModel();
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


    /*********************/
    /**** Confirm user methods */
    /*********************/


    /**
     * Tell if the specified user exists or not
     * @param integer $userID User ID to verify
     */
    public function exists($userID)
    {
        $rsp = new Response();

        $rsp->setSuccess(200)
            ->bindValue("exists", $this->model->exists($userID))
            ->send();
    }



    /**
     * Tell if the specified user is a moderator
     * @param integer $userID User ID to verify
     */
    public function isModerator($userID)
    {
        $rsp = new Response();

        $rsp->setSuccess(200)
            ->bindValue("isModerator", $this->model->isModerator($userID))
            ->send();
    }



    /**
     * Tell if the specified user is an administrator
     * @param integer $userID User ID to verify
     */
    public function isAdmin($userID)
    {
        $rsp = new Response();

        $rsp->setSuccess(200)
            ->bindValue("isAdmin", $this->model->isAdmin($userID))
            ->send();
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
	 * Return all profiles of an user
	 * @return Response    JSON
	 */
	public function profiles()
	{
		$userID = Session::read("userID");

		if(!$this->setUser($userID)){
			return;
		}

		$profiles = $this->profileModel->getUserProfiles($userID);

		$resp = new Response();

		if($profiles){
			$resp->setSuccess(200, "user profiles returned")
			     ->bindValue("userID", $userID)
			     ->bindValue("nbOfProfiles", count($profiles))
			     ->bindValue("profiles", $profiles);
		}else{
			$resp->setFailure(404, "user profiles not found");
		}

		//envoi de la réponse
		$resp->send();
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
							$ban = new BanController;
							if (!$ban->model->isEmailBan($_POST['user_email'])){
								$resp->setSuccess(200, "email changed")
								->bindValue("userID", $userID)
								->bindValue("userEmail", $this->model->getEmail());
								Session::renewKey();
							}else{
								$resp->setFailure(406, "Email banned");
							}
						}else{
							$resp->setFailure(409, "Incorrect email");
						}
					}else{
						$resp->setFailure(403, "User already exists");
					}
				}else{
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
							Session::renewKey();
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

	public function rules($field)
	{
		$resp = new Response();

		//get userID
		$userID = Session::read("userID");

		// If userID is wrong
		if(!$this->setUser($userID)){
			return;
		}

		//User is authorized ?
		if(!isAuthorized::isAdmin($this->model->getAdmin())){
			$resp->setFailure(401, "You are not authorized to do this action.")
			     ->send();

			return;
		}

		switch($field)
		{
			/*---------- USER_MODERATOR ----------*/
			case "setModerator":

				if(!empty($_POST['id'])){
					if($this->model->userExists($_POST['id'])){
						if($this->model->setModerator($_POST['id'])){
							$resp->setSuccess(200, "user is now moderator")
							 	 ->bindValue("userModeratorID", $_POST['id'])
							 	 ->bindValue("userModerator", 1);
							Session::renewKey();
						}else{
							$resp->setFailure(409, "incorrect id");
						}
					}else{
						$resp->setFailure(404, "Given user ID does not exist");
					}
				}else{
					$resp->setFailure(400, "Missing value. Edit aborted.");
				}
				break;

			/*---------- USER_ADMIN ----------*/
			case "setAdmin":

				if(!empty($_POST['id'])){
					if($this->model->userExists($_POST['id'])){
						if($this->model->setAdmin($_POST['id'])){
							$resp->setSuccess(200, "user is now admin")
							 	 ->bindValue("userAdminID", $_POST['id'])
							 	 ->bindValue("userModerator", 1)
							 	 ->bindValue("userAdmin", 1);
							Session::renewKey();
						}else{
							$resp->setFailure(409, "incorrect ID");
						}
					}else{
						$resp->setFailure(404, "Given user admin ID does not exist");
					}
				}else{
					$resp->setFailure(400, "Missing value. Edit aborted");
				}
				break;

			/*---------- USER ----------*/
			case "setUser":

				if(!empty($_POST['id'])){
					if($this->model->userExists($_POST['id'])){
						if($this->model->setToUser($_POST['id'])){
							$resp->setSuccess(200, "user is now just user")
							 	 ->bindValue("oldModeratorAdminID", $_POST['id'])
							 	 ->bindValue("userModerator", 0)
							 	 ->bindValue("userAdmin", 0);
							Session::renewKey();
						}else{
							$resp->setFailure(409, "incorrect ID");
						}
					}else{
						$resp->setFailure(404, "Given user admin ID does not exist");
					}
				}else{
					$resp->setFailure(400, "Missing value. Edit aborted");
				}
				break;

			default:
				$resp->setFailure(405);
		}

		$resp->bindValue("userID", $userID)
		     ->send();
	}

	/**
	 * Supprime le compt ede l'user
	 * @return [type] [description]
	 */

	/* TODO : SUPPRIMER LES PROFILS LORS DE LA SUPPRESSION DU COMPTE */
	public function delete()
	{
		$resp = new Response();

		$userID = Session::read("userID");


		if($userID){
			if(!empty($_POST['user_passwd'])){
				if($this->authModel->checkDelete($userID, $_POST['user_passwd'])){
					if($this->authModel->delete($userID)){
						$resp->setSuccess(200, "account deleted")
						     ->bindValue("userID", $userID)
						     ->send();
						Response::read("auth", "signOut", true);
						return;
					}else{
						$resp->setFailure(403, "fail to delete");
					}
				}else{
					$resp->setFailure(409, "incorrect password");
				}
			}else{
				$resp->setFailure(400, "tous les champs ne sont pas remplis");
			}
		}else{
			$resp->setFailure(401, "You are not authorized to do this action.");
		}

		$resp->send();
	}

}
