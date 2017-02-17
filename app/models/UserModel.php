<?php

class User extends DBInterface{

	private $id = 0;
	private $u = NULL;

	public function __construct($_id)
	{
		parent::__construct();

		$this->setUser($_id);
	}

	public function setUser($userID)
	{
		$userID = Sanitize::int($userID);

		$stmt = $this->cnx->prepare("
			SELECT user_id, user_name, user_email, user_passd, user_register_time,
			       user_last_activity, user_moderator, user_admin, user_activated
			FROM users
			WHERE user_id = :id");
		$stmt->execute([":id" => $userID]);

		//Request Failed
		if($stmt->fetchColumn() == 0)
		{
			$this->u = NULL;
			$this->id = 0;
			return;
		}

		$this->id = $userID;
		$this->u = $stmt->fetch();
	}

	/**
	 * Return if user account is activated
	 * @return boolean true(1) / false(0)
	 */
	public function getActivated()
	{
		if($this->id == 0)
			return;

		return $this->u['user_activated'];
	}

}