<?php

require_once 'models/DBInterface.php';
require_once 'models/Users.php';

$DBI = new DBInterface();

if(isset($_POST["user_name"]) && !empty($_POST["user_name"]) && !empty($_POST["user_email"]) && !empty($_POST["user_passwd"]) && !empty($_POST["user_passwd_confirm"])){
	if($_POST["user_passwd"] == $_POST["user_passwd_confirm"]){
		if($DBI->uniqueUser($_POST["user_email"])){
			$DBI->addUser();
		}
	}
}

require_once 'views/inscription.php';

?>