<?php

class User extends DBInterface{

	private $id;
	private $u = NULL;

	public function __construct($_id)
	{
		parent::__construct();

		$this->setUser($_id);
	}

	public function setUser($userID)
	{
		$userID = Sanitize::int($userID);

		
	}

	public function getActivated()
	{
	
	}

}