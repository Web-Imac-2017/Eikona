<?php

class UserModel extends DBInterface{

	private $id;
	private $u;

	public function __construct($_id)
	{
		parent::__construct();

		$this->setUser($_id);
	}

	public function setUser($userID)
	{
		$userID = Sanitize::int($userID);

		if($userID < 1 || $userID == $this->id)
		{
			$this->id = 0;
			$this->u = NULL;
			return "wrongFormat";
		}

		//Confrim the id before doing anything
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

	/**
	 * Return if user account is activated
	 * @return boolean true(1) / false(0)
	 */
	public function getActivated()
	{
		var_dump($this->id);
		//var_dump($this->id);
		if($this->id == 0)
			return;

		return $this->u['user_activated'];
	}

}