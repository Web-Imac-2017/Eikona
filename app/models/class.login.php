<?php

class Login extends DBInterface{

	public function checkAuthentification($email, $passwd){
		$pwd = hash('sha256', $passwd);
		$sql = "SELECT * FROM users WHERE '$email' = user_email AND '$pwd' = user_passwd";
		$res = $this->cnx->query($sql);
		if($res->rowCount() == 1){
			$u = $res->fetch(PDO::FETCH_ASSOC);
			$User = new User($u['user_id'], $u['user_name'], $u['user_email'], $u['user_passwd']);
			return $User;
		}else{
			return null;
		}
	}

}

