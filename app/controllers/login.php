<?php

require_once '../models/class.dbinterface.php';
require_once '../models/class.login.php';
require_once '../models/class.user.php';

session_start();

$Login = new Login();

if(!isset($_SESSION['user'])){
	if(isset($_POST["user_email"]) && !empty($_POST["user_email"]) && !empty($_POST["user_passwd"])){
		$user = $Login->checkAuthentification($_POST["user_email"], $_POST["user_passwd"]);
		if($user != null){
			$_SESSION['user'] = $user;
			header("LOCATION: index.php");
		}
	}
}

//var_dump("ok");

require_once '../views/login.php';
