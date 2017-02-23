<?php

class UserModel extends DBInterface{

	private $id = 0;
	private $u = NULL;


	public function __construct($_id = 0)
	{
		parent::__construct();

		$this->setUser($_id);
	}


	/**
	 * Instancie la classe User
	 * @param int $userID user_id
	 */
	public function setUser($userID)
	{
		$userID = Sanitize::int($userID);

		if($userID < 1 || $userID == $this->id)
		{
			$this->id = 0;
			$this->u = NULL;
			return "wrongFormat";
		}

		//Confirm the id before doing anything
		$stmt = $this->cnx->prepare("
			SELECT COUNT(user_id) FROM users
			WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		//user not found
		if($stmt->fetchColumn() == 0)
		{
			$this->id = 0;
			$this->u = NULL;
			return "notFound";
		}

		//profile found
		$stmt = $this->cnx->prepare("
			SELECT user_id, user_name, user_email, user_passwd, user_register_time,
			       user_last_activity, user_moderator, user_admin, user_activated
			FROM users
			WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		$this->id = $userID;
		$this->u = $stmt->fetch();

		return "success";
	}

	/*****************/
	/***** GETTER *****/
	/*****************/

	public function getFullUser()
	{
		return $this->u;
	}

	/**
	 * Return user ID
	 * @return int user_id
	 */
	public function getID()
	{
		return $this->id;
	}

	/**
	 * Return user name
	 * @return text user_name
	 */
	public function getName()
	{
		return $this->u['user_name'];
	}

	/**
	 * Return user email
	 * @return text user_email
	 */
	public function getEmail()
	{
		return $this->u['user_email'];
	}	

	/**
	 * Return if user account is activated
	 * @return boolean true(1) / false(0)
	 */
	public function getActivated()
	{
		return $this->u['user_activated'];
	}

	/**
	 * Return if user id admin
	 * @return boolean true(1) / false(0)
	 */
	public function getAdmin()
	{
		return $this->u['user_admin'];
	}

	/******************/
	/***** UPDATE *****/
	/******************/

	/**
	 * Update user name
	 * @param  text $newName user_name
	 * @return boolean       true / false
	 */
	public function updateName($newName)
	{
		if($this->id == 0) return false;

		$name = Sanitize::userName($newName);

		if(!$name) return false;

		$stmt = $this->cnx->prepare("
			UPDATE users
			SET user_name = :name
			WHERE user_id = :id");
		$stmt->execute([":name" => $name,
						":id"   => $this->id]);

		$this->u['user_name'] = $name;
		return true;
	}


	/**
	 * Update user email
	 * @param  text $newEmail user_email
	 * @return boolean           true / false
	 */
	public function updateEmail($newEmail)
	{
		if($this->id == 0) return false;

		$email = Sanitize::userEmail($newEmail);

		if(!$email) return false;

		$stmt = $this->cnx->prepare("
			UPDATE users
			SET user_email = :email
			WHERE user_id = :id");
		$stmt->execute([":email" => $email,
			            ":id"    => $this->id]);

		$this->u['user_email'] = $email;

		return true;
	}

	/**
	 * Update user password
	 * @param  text $newPasswd user_passwd
	 */
	public function updatePassword($newPasswd){
		if($this->id == 0) return false;

		$pwd = hash('sha256', $newPasswd);

		$stmt = $this->cnx->prepare("
			UPDATE users
			SET user_passwd = :pwd
			WHERE user_id = :id");
		$stmt->execute([":pwd" => $pwd,
			            ":id"  => $this->id]);

		$this->u['user_passwd'] = $pwd;

		return true;
	}

	/******************/
	/***** SETTER *****/
	/******************/

	/**
	 * L'utilisateur devient un modérateur
	 * @param int $id user_id
	 */
	public function setModerator($id)
	{
		if($this->id == 0) return false;

		$stmt = $this->cnx->prepare("
			UPDATE users
			SET user_moderator = true
			WHERE user_id = :id");
		$stmt->execute([":id" => $id]);

		$this->u['user_moderator'] = true;

		return true;
	}

	/**
	 * Vérifie si l'utilisateur est unique
	 * @param  text $email user_email
	 * @return boolean	    true / false
	 */
	public function isUnique($email)	
	{
		$stmt = $this->cnx->prepare("
			SELECT COUNT(*) FROM users
			WHERE user_email = :email");	
		$stmt->execute([":email" => $email]);

		return ($stmt->fetchColumn() == 0) ? true : false;
	}

	/**
	 * Return if user exists
	 * @param  int $id user_id
	 * @return boolean     true / false
	 */
	public function userExists($id)
	{
		$stmt = $this->cnx->prepare("
			SELECT COUNT(*) FROM users
			WHERE user_id = :id");	
		$stmt->execute([":id" => $id]);

		return ($stmt->fetchColumn() == 1) ? true : false;
	}

}
