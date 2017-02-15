<?php

class User extends DBInterface{

	private $id;

	public function __construct($_id)
	{
		parent::__construct();

		$this->id = $_id;
	}

	public function getActivated()
	{
		var_dump($this->id);
	}

}