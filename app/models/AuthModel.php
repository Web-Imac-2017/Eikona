<?php

class AuthModel extends DBInterface{

	public function __construct()
	{
		parent::__construct();
	}

	/***********************/
	/***** INSCRIPTION *****/
	/***********************/

	/**
	 * Vérifie si l'utilisateur est unique
	 * @param  text $email user_email
	 * @return boolean	    true / false
	 */
	public function uniqueUser($email)	
	{
		$stmt = $this->cnx->prepare("
			SELECT COUNT(*) FROM users
			WHERE user_email = :email");	
		$stmt->execute([":email" => $email]);

		return ($stmt->fetchColumn() == 0) ? true : false;
	}

	/**
	 * Ajoute un utilisateur dans la base de données
	 * @param text $name   user_name
	 * @param text $email  user_email
	 * @param text $passwd user_passwd
	 * @param time $time   time()
	 * @return int         id ajouté dans la table
	 */
	public function addUser($name, $email, $passwd, $time)
	{
		// cryptage du mot de passe en sha256
		$pwd = hash('sha256', $passwd);

		$stmt = $this->cnx->prepare("
			INSERT INTO users (user_name, user_email, user_passwd, user_register_time)
			VALUES (:name, :email, :pwd, :time)");
		$stmt->execute([":name"  => $name,
			            ":email" => $email,
			            ":pwd"   => $pwd,
			            ":time"  => $time]);

		return $this->cnx->lastInsertId();
	}

	/**
	 * Envoi d'un email d'activation
	 * @param  int $id    user_id
	 * @param  text $email user_email
	 * @param  time $time  time()
	 * @return [type]        [description]
	 */
	public function sendMail($id, $email, $time)
	{
		require_once 'Library/Mail.php';

		$subject = "ACTIVER VOTRE COMPTE EIKONA";

		//TODO 
		//CHANGER L'ADRESSE D'ENVOI POUR LA MISE EN PROD
		$headers = 'From: zobeleflorian@gmail.com' . "\r\n" .
                   'MIME-Version: 1.0' . "\r\n" .
                   'Content-type: text/html; charset=utf-8';

        mail($email, $subject);

    }

	/**********************/
	/***** ACTIVATION *****/
	/**********************/

	/**
	 * Vérifie si l'utilisateur existe bien dans la databse
	 * @param  int $id  user_id
	 * @param  int  $key clé_cryptée
	 * @return boolean   true / false
	 */
	public function checkUserExists($id, $key)
	{
		$stmt = $this->cnx->prepare("
			SELECT user_id, user_register_time, user_activated FROM users
			WHERE :id = user_id
			AND :key = sha1(user_register_time)");
		$stmt->execute([":id"  => $id,
			            ":key" => $key]);

		return ($stmt->rowCount() == 1) ? true : false;
	}

	/**
	 * Active le compte de l'utilisateeur
	 * @param  int $id user_id 	
	 */
	public function updateUserActivated($id)
	{	
		$stmt = $this->cnx->prepare("
			UPDATE users SET user_activated = 1
			WHERE :id= user_id");
		$stmt->execute([":id" => $id]);	
	}


	/*********************/
	/***** CONNEXION *****/
	/*********************/

	/**
	 * Connexion (ou non) de l'utilisateur
	 * @param  text $email  user_email
	 * @param  text $passwd user_passwd
	 * @return boolean      user_id / null
	 */
	public function checkAuth($email, $passwd)
	{
		//cryptage du passwd
		$pwd = hash('sha256', $passwd);
		$stmt = $this->cnx->prepare("
			SELECT user_id FROM  users
			WHERE :email = user_email
			AND :passwd = user_passwd");
		$stmt->execute([":email"  => $email,
			            ":passwd" => $pwd]);

		$u = $stmt->fetch(PDO::FETCH_ASSOC);
		return new UserModel($u['user_id']);
	}
}
