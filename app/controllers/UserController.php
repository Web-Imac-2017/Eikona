<?php

interface UserControllerInterface
{
    public function exists($userID);

    public function isModerator($userID);

    public function isAdmin($userID);

	public function get();

	public function profiles();

	public function edit($field);

	public function rules($field);

	public function delete();

	public function notifications();
}

class UserController
{

	private $model;
	private $profileModel;
	private $authModel;
	private $notifModel;

	public function __construct()
	{
		$this->model        = new UserModel();
		$this->profileModel = new ProfileModel();
		$this->authModel    = new AuthModel();
		$this->notifModel   = new NotificationModel();
	}

	/**
     * Set the user to use with the model
     * @param  int $userID User ID to use with the model
     * @return boolean  true on success, false on failure
     */
	private function setUser($userID)
	{
		$result = $this->model->setUser($userID);

        if($result == "wrongFormat")
        {
            $rsp = new Response();
            $rsp->setFailure(400, "Wrong format. This is not a user ID")
                ->send();

            return false;
        }

        if($result == "notFound")
        {
            $rsp = new Response();
            $rsp->setFailure(404, "Given user ID does not exist")
                ->send();

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

		$profilesID = $this->profileModel->getUserProfiles($userID);

        $profiles = array();

        foreach($profilesID as $profileID)
        {
            array_push($profiles, Response::read("profile", "get", $profileID)["data"]);
        }

		$resp = new Response();

		if(count($profiles) > 0)
        {
			$resp->setSuccess(200, "user profiles returned")
			     ->bindValue("userID", $userID)
			     ->bindValue("nbOfProfiles", count($profiles))
			     ->bindValue("profiles", $profiles)
                 ->send();

            return;
        }

        $resp->setFailure(404, "user profiles not found")
             ->send();
	}

	/**
	 * Update the specified element of the user
	 * @param  text $field Field to be updated
	 * @return Response        JSON
	 */
	public function edit($field)
	{
		$rsp = new Response();

		//get userID
		$userID = Session::read("userID");

		// If userID is wrong
		if(!$this->setUser($userID))
        {
			return;
		}

		//User is authorized ?
		if(!isAuthorized::isUser($userID))
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

			return;
		}

        //Do we have the info we need ?
        if(($field == "name" && empty($_POST['name'])) ||
           ($field == "email" && empty($_POST['email'])) ||
           ($field == "password" && empty($_POST['passwd'])))
        {
            $rsp->setFailure(400, "Missing value. Edit aborted.")
                ->send();

            return;
        }

        $rsp->bindValue("userID", $userID);

        $actions = ["name", "email", "password"];

        if(!in_array($field, $actions))
        {
            $rsp->setFailure(405)
                ->send();

            return;
        }

		switch($field)
		{
			/*---------- USER_NAME ----------*/
			case "name":

				if(!$this->model->updateName($_POST['name']))
                {
                    $rsp->setFailure(409, "incorrect value")
                        ->send();

                    return;
                }

                $rsp->setSuccess(200, "name changed")
				    ->bindValue("userName", $this->model->getName())
                    ->send();

            break;
			/*---------- USER_EMAIL ----------*/
			case "email":

                if(!$this->model->isUnique($_POST['email']))
                {
				    $rsp->setFailure(403, "User already exists")
                        ->send();

                    return;
                }

                if(!$this->model->updateEmail($_POST['email']))
                {
                    $rsp->setFailure(409, "Incorrect email")
                        ->send();

                    return;
                }

                $ban = new BanController;

				if ($ban->model->isEmailBan($_POST['user_email']))
                {
                    $rsp->setFailure(406, "Email banned")
                        ->send();

                    return;
                }

				Session::renewKey();

                $rsp->setSuccess(200, "email changed")
				    ->bindValue("userEmail", $this->model->getEmail())
                    ->send();

            break;
			/*---------- USER_PASSWORD ----------*/
			case "password":

                if($_POST['passwd'] !== $_POST['passwd_confirm'])
                {
                    $rsp->setFailure(409, "password and confirmation do not correspond")
                        ->send();

                    return;
                }

                if(!$this->model->updatePassword($_POST['passwd']))
                {
                    $rsp->setFailure(409, "incorrect password")
                        ->send();

                    return;
                }

                Session::renewKey();

				$rsp->setSuccess(200, "password changed")
                    ->send();

            break;
		}
	}

	/**
	 * Handle user priveleges assignment, or removal.
	 * @param string $field Action to do
	 */
	public function rules($field)
	{
		$rsp = new Response();

		//get userID
		$userID = Session::read("userID");

		// If userID is wrong
		if(!$this->setUser($userID))
        {
			return;
		}

        //Do we have all we need
        if(!empty($_POST['id']))
        {
            $rsp->setFailure(400, "Missing value. Edit aborted.")
                ->send();
        }

		//User is authorized ?
		if(!isAuthorized::isAdmin())
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

			return;
		}

        //Is this a legit user ID ?
        if($this->model->userExists($_POST['id']))
        {
            $rsp->setFailure(404, "Given user ID does not exist")
                ->send();

            return;
        }

		$rsp->bindValue("userID", $userID);

        $actions = ["setModerator", "setAdmin", "setUser"];

        //Is this action supported?
        if(!in_array($field, $actions))
        {
            $rsp->setFailure(405)
                ->send();

            return;
        }

		switch($field)
		{
			/*---------- USER_MODERATOR ----------*/
			case "setModerator":

				if(!$this->model->setModerator($_POST['id']))
                {
                    $rsp->setFailure(409, "incorrect id")
                        ->send();

                    return;
                }
                    Session::renewKey();

                $rsp->setSuccess(200, "user is now moderator")
                    ->bindValue("userModeratorID", $_POST['id'])
                    ->bindValue("userModerator", 1)
                    ->send();

            break;
			/*---------- USER_ADMIN ----------*/
			case "":

				if(!$this->model->setAdmin($_POST['id']))
                {
                    $rsp->setFailure(409, "incorrect ID")
                        ->send();

                    return;
                }

                Session::renewKey();

                $rsp->setSuccess(200, "user is now admin")
                    ->bindValue("userAdminID", $_POST['id'])
                    ->bindValue("userModerator", 1)
                    ->bindValue("userAdmin", 1)
                    ->send();

            break;
			/*---------- USER ----------*/
			case "setUser":

                if($this->model->setToUser($_POST['id']))
                {
                    $rsp->setFailure(409, "incorrect ID")
                        ->send();

                    return;
                }

                Session::renewKey();

                $rsp->setSuccess(200, "user is now just user")
					->bindValue("oldModeratorAdminID", $_POST['id'])
					->bindValue("userModerator", 0)
					->bindValue("userAdmin", 0)
                    ->send();

            break;
		}
	}

	/**
	 * Delete user account
	 */
	public function delete()
	{
		$rsp = new Response();

		$userID = Session::read("userID");

        //Are we logged in ?
		if(!$userID)
        {
			$rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        //Do we have all we need ?
        if(empty($_POST['user_passwd']))
        {
            $rsp->setFailure(400, "tous les champs ne sont pas remplis")
                ->send();

            return;
        }

        //Can we delete this user?
        if(!$this->authModel->checkDelete($userID, $_POST['user_passwd']))
        {
            $rsp->setFailure(409, "incorrect password")
                ->send();

            return;
        }

        //Delete the user
        if(!$this->authModel->delete($userID))
        {
            $rsp->setFailure(403, "fail to delete")
                ->send();

             return;
        }

	    /* TODO : SUPPRIMER LES PROFILS LORS DE LA SUPPRESSION DU COMPTE */

        Response::read("auth", "signOut", true);

        $rsp->setSuccess(200, "account deleted")
            ->bindValue("userID", $userID)
            ->send();
	}

	/**
	 * Return all notifications for the current user
	 */
	public function notifications()
	{
		$rsp = new Response();

		$userID = Session::read("userID");

        //Are we logged in?
		if($userID)
        {
			$rsp->setFailure(401, "you are not connected")
                ->send();

            return;
        }

		if(!$this->setUser($userID))
			return;

        //Do the user has profiles to retreive notifications from ?
        if(!$this->profileModel->hasProfiles($userID))
        {
            $rsp->setFailure(401, "You don't have profiles")
                ->send();

            return;
        }

        $notif = $this->notifModel->getUserNotifications($userID);

        foreach($notif as $n)
        {
            $tab[$n['profile_id']] = $n;
        }

        $rsp->setSuccess(200, "notif returned")
            ->bindValue("notif", $tab)
		    ->send();
	}
}
