<?php

interface UserModelInterface
{
	public function setUser($userID);

    public function exists($userID);

    public function isModerator($userID);

    public function isAdmin($userID);

	public function getFullUser();

	public function getID();

	public function getName();

	public function getEmail();

	public function getActivated();

	public function getKey();

	public function updateName($newName);

	public function updateEmail($newEmail);

	public function updatePassword($newPasswd);

	public function setModerator($userID);

	public function setAdmin($userID);

	public function setToUser($userID);

	public function isUnique($email);

	public function userExists($userID);
}

class UserModel extends DBInterface implements UserModelInterface
{

	private $userID = 0;
	private $user = NULL;


	public function __construct($_id = 0)
	{
		parent::__construct();

		$this->setUser($_id);
	}

	/**
	 * Class initializer. Tries to load informations on the given user
	 * @param int $userID user_id
	 */
	public function setUser($userID)
	{
		$userID = Sanitize::int($userID);

		if($userID < 1 || $userID == $this->userID)
		{
			$this->userID = 0;
			$this->user = NULL;

			return "wrongFormat";
		}

		//Confirm the id before doing anything
		$stmt = $this->cnx->prepare("SELECT COUNT(user_id) FROM users WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		//user not found
		if($stmt->fetchColumn() == 0)
		{
			$this->userID = 0;
			$this->user = NULL;

			return "notFound";
		}

		//user found
		$stmt = $this->cnx->prepare("
			SELECT user_id, user_name, user_email, user_passwd, user_register_time,
			       user_last_activity, user_moderator, user_admin, user_activated, user_key
			FROM users
			WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		$this->userID = $userID;
		$this->user = $stmt->fetch();

		return "success";
	}



    /**
     * Tell if the specified user exists or not
     * @param integer $userID User ID to verify
     */
    public function exists($userID)
    {
        $userID = Sanitize::int($userID);

        if($userID < 1)
            return false;

		$stmt = $this->cnx->prepare("SELECT COUNT(user_id) FROM users WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

        return $stmt->fetchColumn() == "1" ? true : false;
    }


    /**
     * Tell if the specified user is a moderator
     * @param integer $userID User ID to verify
     */
    public function isModerator($userID)
    {
        $userID = Sanitize::int($userID);

        if($userID < 1)
            return false;

		$stmt = $this->cnx->prepare("SELECT user_moderator FROM users WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

        return $stmt->fetchColumn() == "1" ? true : false;
    }

    /**
     * Tell if the specified user is an administrator
     * @param integer $userID User ID to verify
     */
    public function isAdmin($userID)
    {
        $userID = Sanitize::int($userID);

        if($userID < 1)
            return false;

		$stmt = $this->cnx->prepare("SELECT user_admin FROM users WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

        return $stmt->fetchColumn() == "1" ? true : false;
    }



	/*****************/
	/***** GETTER *****/
	/*****************/

    /**
	 * Return all the informations about the user
	 * @return int user_id
	 */
	public function getFullUser()
	{
		return $this->user;
	}

	/**
	 * Return user ID
	 * @return int user_id
	 */
	public function getID()
	{
		return $this->userID;
	}

	/**
	 * Return user name
	 * @return text user_name
	 */
	public function getName()
	{
		return $this->user['user_name'];
	}

	/**
	 * Return user email
	 * @return text user_email
	 */
	public function getEmail()
	{
		return $this->user['user_email'];
	}	

	/**
	 * Return if user account is activated
	 * @return boolean true(1) / false(0)
	 */
	public function getActivated()
	{
		return $this->user['user_activated'];
	}

	/**
	 * Return the unique key of the user
	 * @return string Key of the user
	 */
	public function getKey()
	{
		return $this->user['user_key'];
	}

	/******************/
	/***** UPDATE *****/
	/******************/

	/**
	 * Update user name
	 * @param  text    $newName user_name
	 * @return boolean true / false
	 */
	public function updateName($newName)
	{
		if($this->userID == 0)
            return false;

		$name = Sanitize::userName($newName);

		if(!$name)
            return false;

		$stmt = $this->cnx->prepare("UPDATE users SET user_name = :name WHERE user_id = :id");
		$stmt->execute([":name" => $name,
						":id"   => $this->userID]);

		$this->user['user_name'] = $name;

		return true;
	}


	/**
	 * Update user email
	 * @param  text    $newEmail user_email
	 * @return boolean true / false
	 */
	public function updateEmail($newEmail)
	{
		if($this->userID == 0)
            return false;

		$email = Sanitize::userEmail($newEmail);

		if(!$email)
            return false;

		$stmt = $this->cnx->prepare("UPDATE users SET user_email = :email WHERE user_id = :id");
		$stmt->execute([":email" => $email,
			            ":id"    => $this->userID]);

		$this->user['user_email'] = $email;

		return true;
	}

	/**
	 * Update user password
	 * @param  text $newPasswd user_passwd
	 */
	public function updatePassword($newPasswd)
    {
		if($this->userID == 0)
            return false;

		$pwd = hash('sha256', $newPasswd);

		$stmt = $this->cnx->prepare("UPDATE users SET user_passwd = :pwd WHERE user_id = :id");
		$stmt->execute([":pwd" => $pwd,
			            ":id"  => $this->userID]);

		$this->user['user_passwd'] = $pwd;

		return true;
	}

	/******************/
	/***** SETTER *****/
	/******************/

	/**
	 * Make given usen a moderator
	 * @param int $userID user_id
	 */
	public function setModerator($userID)
	{
		if($this->userID == 0)
            return false;

		$stmt = $this->cnx->prepare("UPDATE users SET user_moderator = true WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		$this->user['user_moderator'] = true;

		return true;
	}

	/**
	 * Make given user an administrator
	 * @param int $userID user_id
	 */
	public function setAdmin($userID)
	{
		if($this->userID == 0)
            return false;

		$stmt = $this->cnx->prepare("UPDATE users SET user_admin = true, user_moderator = true WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		$this->user['user_moderator'] = true;
		$this->user['user_admin'] = true;

		return true;
	}

	/**
	 * Make tgivenhe user a simple user without privileges
	 * @param int $userID user_id
	 */
	public function setToUser($userID)
	{
		if($this->userID == 0)
            return false;

		$stmt = $this->cnx->prepare("UPDATE users SET user_admin = false, user_moderator = false WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		$this->user['user_moderator'] = false;
		$this->user['user_admin'] = false;

		return true;
	}

	/******************/
	/***** OTHERS *****/
	/******************/

	/**
	 * Confirm uniqueness of user through the email
	 * @param  text $email user_email
	 * @return boolean	    true / false
	 */
	public function isUnique($email)	
	{
		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM users WHERE user_email = :email");
		$stmt->execute([":email" => $email]);

		return ($stmt->fetchColumn() == 0) ? true : false;
	}

	/**
	 * Return if user exists
	 * @param  int $userID user_id
	 * @return boolean     true / false
	 */
	public function userExists($userID)
	{
		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM users WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		return ($stmt->fetchColumn() == 1) ? true : false;
	}

}
