<?php

class Inscription extends DBInterface{

	public function uniqueUser($email){
		$sql = "SELECT user_email FROM users WHERE user_email = '$email'";
		$res = $this->cnx->query($sql);
		if($res->rowCount() == 0){
			return true;
		}else{
			return false;
		}
	}

	public function addUser($name, $email, $passwd){
		$pass = hash('sha256', $passwd);
		$sql = "INSERT INTO users (user_name, user_email, user_passwd) VALUES('$name', '$email', '$pass')";
		$res =  $this->cnx->query($sql);
	}

}