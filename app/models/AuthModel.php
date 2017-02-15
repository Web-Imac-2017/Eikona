<?php

class AuthModel extends DBInterface{

	public function __construct()
	{
		parent::__construct();
	}

	public function uniqueUser($email)	
	{

	}

	public function addUser($name, $email, $passwd, $time)
	{

	}

	public function sendMail($id, $email, $time)
	{

	}

	public function checkUserExists($id, $key)
	{

	}

	public function updateUserActivated($id)
	{
		
	}

}