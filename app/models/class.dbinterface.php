<?php

class DBInterface{

	protected $cnx;

	public function __construct(){
		$this->cnx = new PDO("mysql:dbname=roger;host=localhost;charset=utf8", "root", "") or die("connexion à la bdd impossible");
	}

	public function logout(){
		$_SESSION = array();
   		setcookie(session_name(), '', time() - 42000);
   		session_destroy();
	}
}
