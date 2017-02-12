<?php

require_once '../models/class.dbinterface.php';
require_once '../models/class.inscription.php';

$Inscription = new Inscription();

if(isset($_POST["user_name"]) && !empty($_POST["user_name"]) && !empty($_POST["user_email"]) && !empty($_POST["user_passwd"]) && !empty($_POST["user_passwd_confirm"])){
	if($_POST["user_passwd"] == $_POST["user_passwd_confirm"]){
		if($Inscription->uniqueUser($_POST["user_email"])){
			$user_register_time = time();
			$id = $Inscription->addUser($_POST["user_name"], $_POST["user_email"], $_POST["user_passwd"], $user_register_time);
			$Inscription->sendMail($_POST["user_email"], $user_register_time, $id);
			$log = "Un email d'activation a été envoyé à l'adresse " . $_POST["user_email"] . ".";
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


require_once '../views/inscription.php';

?>