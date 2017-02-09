<?php

require_once '../models/class.dbinterface.php';
require_once '../models/class.inscription.php';

$Inscription = new Inscription();

/*
	Si l'utilisateur appuie sur le bouton 'Valider le compte' et que les variables sont exactes
		Vérification si l'utilisateur existe bien (values exactes intactes - pas modifiées)
			Activation de son compte
	envoi d'un log
*/

if(isset($_POST["id"]) && !empty($_POST["id"]) && !empty($_POST["key"])){
	if($Inscription->checkUserExists($_POST["id"], $_POST["key"])){
		$Inscription->updateUserActivated($_POST["id"]);
		$log = "Votre compte est activé";
	}else{
		$log = "Vous n'avez pas de compte chez nous !";
	}
}

require_once '../views/activation.php';

?>