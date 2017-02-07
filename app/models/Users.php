<?php

class Users{

	private $id;
	private $name;
	private $email;
	private $passwd;

	public function __construct($_id, $_name, $_email, $_passwd){
		$this->id = $_id;
		$this->name = $_name;
		$this->email = $_email;
		$this->passwd = $_passwd;
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
}

