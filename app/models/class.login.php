<?php

class Login extends DBInterface{

	/**
	 * Fonction qui va checker la combinaison email / mot de passe pour la connection
	 * @param  text $email  Email de l'user
	 * @param  text $passwd Mot de passe (non crypté) de l'user
	 * @return boolean / User         class User si oui, null sinon
	 */
	public function checkAuthentification($email, $passwd){
		$pwd = hash('sha256', $passwd);
		$res = $this->cnx->prepare("SELECT * FROM users WHERE :email = user_email AND :passwd = user_passwd");
		$res->execute([":email"  => $email,
                       ":passwd" => $pwd]);
		if($res->rowCount() == 1){
			$u = $res->fetch(PDO::FETCH_ASSOC);
			$User = new User($u['user_id'], $u['user_name'], $u['user_email'], $u['user_passwd'], $u['user_activated']);
			return $User;
		}else{
			return null;
		}
	}

}

