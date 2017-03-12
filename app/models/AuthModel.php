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
	public function isUnique($email)
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
			INSERT INTO users (user_name, user_email, user_passwd, user_register_time, user_last_activity)
			VALUES (:name, :email, :pwd, :time, :lastAct)");
		$stmt->execute([":name"    => $name,
			            ":email"   => $email,
			            ":pwd"     => $pwd,
			            ":time"    => $time,
			            ":lastAct" => $time]);

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
		$headers = 'From: j9b455c69@gmail.com' . "\r\n" .
                   'MIME-Version: 1.0' . "\r\n" .
                   'Content-type: text/html; charset=utf-8';

       return (mail($email, $subject, $content, $headers)) ? true : false;
    }


    /************************/
	/***** RECUPERATION *****/
	/************************/

    private function randomString()
    {
    	$res = "";
    	$length = 6;
    	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$array = str_split($chars);

    	for($i=0; $i<$length; $i++){
    		$res .= $array[array_rand($array)];
    	}

    	return $res;
    }

    public function addCode($email)
    {
    	$code = $this->randomString();

    	$stmt = $this->cnx->prepare("
    		UPDATE users SET user_code = :code
    		WHERE user_email = :email");
    	$stmt->execute([":code" => $code,
    		            ":email" => $email]);

    	return $code;
    }

    public function checkCode($email, $code)
    {
    	$stmt = $this->cnx->prepare("
    		SELECT COUNT(*) FROM users
    		WHERE :code = user_code
    		AND :email = user_email");
    	$stmt->execute([":code" => $code,
    		            ":email" => $email]);

    	return ($stmt->fetchColumn() == 1) ? true : false;
    }

    public function sendRecuperationMail($email, $code)
    {
    	require_once 'Library/RecuperationMail.php';

    	$subject = "RECUPEREZ VOTRE MOT DE PASSE";

    	$headers = 'From: zobeleflorian@gmail.com' . "\r\n" .
                   'MIME-Version: 1.0' . "\r\n" .
                   'Content-type: text/html; charset=utf-8';

        return (mail($email, $subject, $content, $headers)) ? true : false;
    }

    public function updatePassword($email, $passwd)
    {
    	$pwd = hash("sha256", $passwd);

    	$stmt = $this->cnx->prepare("
    		UPDATE users SET user_passwd = :pwd
    		WHERE user_email = :email");
    	$stmt->execute([":pwd" => $pwd,
    		            ":email" => $email]);
    }

    public function deleteCode($email)
    {
    	$stmt = $this->cnx->prepare("
    		UPDATE users SET user_code = NULL
    		WHERE user_email = :email");
    	$stmt->execute([":email" => $email]);
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
	public function checkActivation($id, $key)
	{
		$stmt = $this->cnx->prepare("
			SELECT user_id, user_register_time, user_activated FROM users
			WHERE :id = user_id
			AND :key = sha1(user_register_time)");
		$stmt->execute([":id"  => $id,
			            ":key" => $key]);

		return ($stmt->fetchColumn() != 0) ? true : false;
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
	 * Return if email exists in database
	 * @param  text $email user_email
	 * @return boolean        true / false
	 */
	public function checkEmail($email)
	{
		//Savoir si l'user est inscrit
		$stmt = $this->cnx->prepare("
			SELECT user_id FROM users
			WHERE :email = user_email");
		$stmt->execute([":email" => $email]);

		return ($stmt->fetchColumn() != null) ? true : false;
	}

	/**
	 * Return User (qu'il existe ou non)
	 * @param  text $email  user_email
	 * @param  text $passwd user_passwd
	 * @return User
	 */
	public function checkConnection($email, $passwd)
	{
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

	/***********************/
	/***** SUPPRESSION *****/
	/***********************/

	public function checkDelete($id, $passwd)
	{

		$pwd = hash("sha256", $passwd);

		$stmt = $this->cnx->prepare("
			SELECT COUNT(*) FROM users
			WHERE :pwd = user_passwd
			AND :id = user_id");
		$stmt->execute([":pwd" => $pwd,
			            ":id"  => $id]);

		return ($stmt->fetchColumn() == 1) ? true : false;
	}

	public function delete($id)
	{
		if($id == 0) return false;

		$stmt = $this->cnx->prepare("
			DELETE FROM users
			WHERE :id = user_id");
		$stmt->execute([":id" => $id]);

		return true;
	}

}
