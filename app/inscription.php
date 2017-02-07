<?php

require_once 'models/DBInterface.php';
require_once 'models/Inscription.php';
require_once 'models/Users.php';

$Inscription = new Inscription();

if(isset($_POST["user_name"]) && !empty($_POST["user_name"]) && !empty($_POST["user_email"]) && !empty($_POST["user_passwd"]) && !empty($_POST["user_passwd_confirm"])){
	if($_POST["user_passwd"] == $_POST["user_passwd_confirm"]){
		if($Inscription->uniqueUser($_POST["user_email"])){
			$log = $Inscription->addUser($_POST["user_name"], $_POST["user_email"], $_POST["user_passwd"]);
		}else{
			$log = "L'utilisateur existe déjà";
		}
	}else{
		$log = "les mots de passe ne correspondent pas";
	}
}

if(isset($log)){
	echo $log;
}


require_once 'views/inscription.php';

?>