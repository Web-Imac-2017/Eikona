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
	 * @return [type]        [description]
	 */
	public function uniqueUser($email)	
	{
		
	}

	/**
	 * Ajoute un utilisateur dans la base de données
	 * @param text $name   user_name
	 * @param text $email  user_email
	 * @param text $passwd user_passwd
	 * @param time $time   time()
	 */
	public function addUser($name, $email, $passwd, $time)
	{

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

	}

	/**********************/
	/***** ACTIVATION *****/
	/**********************/

	/**
	 * Vérifie si l'utilisateur existe bien dans la databse
	 * @param  int $id  user_id
	 * @param  int  $key clé_cryptée
	 * @return [type]      [description]
	 */
	public function checkUserExists($id, $key)
	{

	}

	/**
	 * Active le compte de l'utilisateeur
	 * @param  int $id user_id
	 * @return [type]     [description]
	 */
	public function updateUserActivated($id)
	{

	}


	/*********************/
	/***** CONNEXION *****/
	/*********************/

	/**
	 * Connexion (ou non) de l'utilisateur
	 * @param  text $email  user_email
	 * @param  text $passwd user_passwds
	 * @return [type]         [description]
	 */
	public function checkAuth($email, $passwd)
	{

	}
}