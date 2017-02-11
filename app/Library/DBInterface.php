<?php

class DBInterface{

	protected $cnx;

	public function __construct(){
		$this->cnx = new PDO("mysql:dbname=roger;host=localhost;charset=utf8", "root", "root") or die("connexion à la bdd impossible");
        $this->cnx->exec("SET CHARACTER SET utf8");
	}
}
