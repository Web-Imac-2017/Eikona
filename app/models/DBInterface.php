<?php

class DBInterface{

	private $cnx;

	public function __construct(){
		$this->cnx = new PDO("mysql:dbname=roger;host=localhost;charset=utf8", "root", "") or die("connexion à la bdd impossible");
	}
    
    //Database query handler suggestion - need to be completed with more security
    public function request($sql, $vars = [])
    {
        $stmt = $cnx->prepare($sql);
        $stmt->execute($vars);
        
        return $stmt;
    }

}
