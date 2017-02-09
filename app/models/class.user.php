<?php

class User{
	
	private $id;
	private $name;
	private $email;
	private $passwd;

	public function __construct($_id, $_name, $_email, $_passwd, $_activated){
		$this->id = $_id;
		$this->name = $_name;
		$this->email = $_email;
		$this->passwd = $_passwd;
		$this->activated = $_activated;
	}	

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getEmail(){
		return $this->email;
	}

	public function getPasswd(){
		return $this->passwd;
	}

	public function getActivated(){
		return $this->activated;
	}
}

