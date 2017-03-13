<?php

/**
 * AuthModel
 * Handle authentication related calls to the database
 */

interface AuthModelInterface
{
	public function isUnique($email);

	public function addUser($name, $email, $passwd, $time);

	public function sendMail($userID, $email, $time);

    public function getByKey($key);

    public function addCode($email);

    public function checkCode($email, $code);

    public function sendRecuperationMail($email, $code);

    public function updatePassword($email, $passwd);

    public function deleteCode($email);

	public function checkActivation($userID, $key);

	public function updateUserActivated($userID);

	public function checkEmail($email);

	public function checkConnection($email, $passwd);

	public function checkDelete($userID, $passwd);

	public function delete($userID);
}

class AuthModel extends DBInterface implements AuthModelInterface
{
	public function __construct()
	{
		parent::__construct();
	}





	/***********************/
	/******* REGISTER ******/
	/***********************/

	/**
	 * Confirm given email is unique => not in the user table already
	 * @param  text    $email user_email
	 * @return boolean	true / false
	 */
	public function isUnique($email)
	{
		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM users WHERE user_email = :email");
		$stmt->execute([":email" => $email]);

		return ($stmt->fetchColumn() == 0) ? true : false;
	}

	/**
	 * Register a new user
	 * @param text $name   user_name
	 * @param text $email  user_email
	 * @param text $passwd user_passwd
	 * @param time $time   time()
	 * @return int  ID of the new user
	 */
	public function addUser($name, $email, $passwd, $time)
	{
		//Encrypt password using sha256
		$pwd = hash('sha256', $passwd);

		$stmt = $this->cnx->prepare(" INSERT INTO users (user_name, user_email, user_passwd, user_register_time, user_last_activity, user_key) VALUES (:name, :email, :pwd, :time, :lastAct, UUID())");
		$stmt->execute([":name"    => $name,
			            ":email"   => $email,
			            ":pwd"     => $pwd,
			            ":time"    => $time,
			            ":lastAct" => $time]);

		return $this->cnx->lastInsertId();
	}

	/**
	 * Send the activation email
	 * @param  int     $userID    user_id
	 * @param  text    $email user_email
	 * @param  time    $time  time()
	 * @return boolean true on success, false on failure
	 */
	public function sendMail($userID, $email, $time)
	{
		require_once 'Library/Mail.php';

		$subject = "ACTIVER VOTRE COMPTE EIKONA";

		//TODO
		//CHANGER L'ADRESSE D'ENVOI POUR LA MISE EN PROD

		$headers = 'From: donotreply@eikona.com' . "\r\n" .
                   'MIME-Version: 1.0' . "\r\n" .
                   'Content-type: text/html; charset=utf-8';

       return (mail($email, $subject, $content, $headers)) ? true : false;
    }

    /**
     * Return the user who match the given key
     * @param  string $key The user key to use
     * @return array  Informations on the found user
     */
    public function getByKey($key)
    {
        $stmt = $this->cnx->prepare("SELECT COUNT(user_id) AS nbr, user_id, user_email FROM users WHERE user_key = :key LIMIT 1");
        $stmt->execute([":key" => $key]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }



    /************************/
	/******* RECOVERY *******/
	/************************/

    /**
     * Generate a random string 6 char long
     * @return string The generated string
     */
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

    /**
     * Create a random recovery code for the given user email
     * @param  string  $email The email of the user
     * @return integer The generated code
     */
    public function addCode($email)
    {
    	$code = $this->randomString();

    	$stmt = $this->cnx->prepare("UPDATE users SET user_code = :code WHERE user_email = :email");
    	$stmt->execute([":code" => $code,
    		            ":email" => $email]);

    	return $code;
    }

    /**
     * Confirm the given code match the code of the given user
     * @param  string  $email Email of the user
     * @param  string  $code  String given by the user
     * @return boolean True on match, false otherwise
     */
    public function checkCode($email, $code)
    {
    	$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM users WHERE :code = user_code AND :email = user_email");
    	$stmt->execute([":code" => $code,
    		            ":email" => $email]);

    	return ($stmt->fetchColumn() == 1) ? true : false;
    }

    /**
     * Send the recovery email for the given user
     * @param  string  $email Email of the user
     * @param  string  $code  The recovery code to send
     * @return boolean True on success, false otherwise
     */
    public function sendRecuperationMail($email, $code)
    {
    	require_once 'Library/RecuperationMail.php';

    	$subject = "RECUPEREZ VOTRE MOT DE PASSE";

    	$headers = 'From: zobeleflorian@gmail.com' . "\r\n".
                   'MIME-Version: 1.0' . "\r\n".
                   'Content-type: text/html; charset=utf-8';

        return (mail($email, $subject, $content, $headers)) ? true : false;
    }

    /**
     * Update user password after a recovery
     * @param string $email  Email of the user
     * @param string $passwd New password
     */
    public function updatePassword($email, $passwd)
    {
    	$pwd = hash("sha256", $passwd);

    	$stmt = $this->cnx->prepare("UPDATE users SET user_passwd = :pwd WHERE user_email = :email");
    	$stmt->execute([":pwd" => $pwd,
    		            ":email" => $email]);
    }

    /**
     * Remove revovery code once it has been used
     * @param string $email Email of the user
     */
    public function deleteCode($email)
    {
    	$stmt = $this->cnx->prepare("UPDATE users SET user_code = NULL WHERE user_email = :email");
    	$stmt->execute([":email" => $email]);
    }

	/**********************/
	/***** ACTIVATION *****/
	/**********************/

	/**
	 * Confirm the given user is in the database
	 * @param  int     $userID  user_id
	 * @param  int     $key encrypted key
	 * @return boolean true / false
	 */
	public function checkActivation($userID, $key)
	{
		$stmt = $this->cnx->prepare("SELECT user_id, user_register_time, user_activated FROM users WHERE :id = user_id AND :key = sha1(user_register_time)");
		$stmt->execute([":id"  => $userID,
			            ":key" => $key]);

		return ($stmt->fetchColumn() != 0) ? true : false;
	}

	/**
	 * Activate given user
	 * @param  int $userID user_id
	 */
	public function updateUserActivated($userID)
	{
		$stmt = $this->cnx->prepare("UPDATE users SET user_activated = 1 WHERE :id = user_id");
		$stmt->execute([":id" => $userID]);
	}


	/*********************/
	/****** LOGIN IN *****/
	/*********************/

	/**
	 * Confirm the given email is in the database
	 * @param  text $email user_email
	 * @return boolean        true / false
	 */
	public function checkEmail($email)
	{
		//Savoir si l'user est inscrit
		$stmt = $this->cnx->prepare("SELECT user_id FROM users WHERE :email = user_email");
		$stmt->execute([":email" => $email]);

		return ($stmt->fetchColumn() != null) ? true : false;
	}

	/**
	 * Confirm the connection is valid
	 * @param  text $email  user_email
	 * @param  text $passwd user_passwd
	 * @return User
	 */
	public function checkConnection($email, $passwd)
	{
		$pwd = hash('sha256', $passwd);
		$stmt = $this->cnx->prepare("SELECT user_id FROM users WHERE :email = user_email AND :passwd = user_passwd");
		$stmt->execute([":email"  => $email,
			            ":passwd" => $pwd]);

		$user = $stmt->fetch(PDO::FETCH_ASSOC);

		return new UserModel($user['user_id']);
	}

	/***********************/
	/******* REMOVING ******/
	/***********************/

	/**
	 * Confirm the given id+password combo is valid
	 * @param  integer $userID     User ID
	 * @param  string  $passwd User Password
	 * @return boolean True if it match, false otherwise
	 */
	public function checkDelete($userID, $passwd)
	{

		$pwd = hash("sha256", $passwd);

		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM users WHERE :pwd = user_passwd AND :id = user_id");
		$stmt->execute([":pwd" => $pwd,
			            ":id"  => $userID]);

		return ($stmt->fetchColumn() == 1) ? true : false;
	}

	/**
	 * Reomve a user account
	 * @param  integer $userID User ID to deleted
	 * @return boolean True on success, false otherwise.
	 */
	public function delete($userID)
	{
		if($userID == 0)
            return false;


        /////////////////
        //TODO : CASCADE REMOVE ALL USER DATA
        /////////////////


        $stmt = $this->cnx->prepare("
			DELETE FROM users
			WHERE :id = user_id");
		$stmt->execute([":id" => $userID]);

		return true;
	}

}
