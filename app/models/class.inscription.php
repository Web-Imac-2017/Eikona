<?php

class Inscription extends DBInterface{

	/**
	 * Vérifie si l'utilisateur (email) n'est pas présent dans la database (mail unique)
	 * @param   $email email à vérifier
	 * @return         true si utilisateur unique, false sinon
	 */
	public function uniqueUser($email){
		$sql = "SELECT user_email FROM users WHERE user_email = '$email'";
		$res = $this->cnx->query($sql);
		if($res->rowCount() == 0){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Ajoute un nouvel utilisateur dans la database
	 * @param text $name   Le nom de l'utilisateur
	 * @param text $email  L'email de l'utilisateur
	 * @param text $passwd Le mot de passe non crypté de l'utilisateur
	 * @param time $time   L'heure (tout en seconde) de son inscription
	 */
	public function addUser($name, $email, $passwd, $time){
		$pass = hash('sha256', $passwd);		
		$sql = "INSERT INTO users (user_name, user_email, user_passwd, user_register_time) VALUES('$name', '$email', '$pass', '$time')";
		$res = $this->cnx->query($sql);
	}

	/**
	 * Envoi un mail d'activation à l'utilisateur
	 * @param  text $email L'email de l'utilisateur sur lequel envoyé le mail
	 * @param  time $time  L'heure d'inscription (va permettre à générer une clé de vérification)
	 */
	public function sendMail($email, $time){
		$subject = 'Activez votre compte Ekona';

		$content = "
		<!DOCTYPE html>
		<html>
			<head>
				<title>Activez votre compte</title>
				<meta charset='UTF-8'>
			</head>
			<body>
				<h2>Pour activer votre compte, veuillez appuyer sur le bouton ci-dessous</h2>
				<form method='POST' action='localhost/Groupe1/app/controllers/activation.php'>
					<input type='hidden' value='".$this->cnx->lastInsertId()."' name='id'>
					<input type='hidden' value='".sha1($time)."' name='key'>
					<input type='submit' value='ACTIVER'>
				</form>	
				<h3>Si le formulaire ne s'affiche pas correctement, <a href='localhost/Groupe1/app/controllers/activation.php?id=".$this->cnx->lastInsertId()."&key=".sha1($time)."'>veuillez suivre ce lien.</a>	
			</body>
		</html>";

		$headers = 'From: zobeleflorian@gmail.com' . "\r\n" .
                   'MIME-Version: 1.0' . "\r\n" .
                   'Content-type: text/html; charset=utf-8';

		mail($email, $subject, $content, $headers);
	}

	/**
	 * Vérifie di l'utilisateur existe bel est bien dans la database
	 * @param  int $id  id de l'utilisateur
	 * @param  text $key clé pour une meilleur sécurité
	 * @return boolean      true si oui, false sinon
	 */
	public function checkUserExists($id, $key){
		$sql = "SELECT user_id, user_register_time, user_activated FROM users WHERE '$id' = user_id AND '$key' = sha1(user_register_time)";
		$res = $this->cnx->query($sql);
		if($res->rowCount() == 1){
			return true; 
		}else{
			return false;
		}
	}
}