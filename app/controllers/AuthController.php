<?php

class AuthController{

	private $model;

	public function __construct()
	{
		$this->model = new AuthModel();
	}

	public function register()
	{
		$resp = new Response();
		//Si formulaire rempli
		if( isset($_POST['user_name']) &&
		   !empty($_POST['user_name']) &&
		   !empty($_POST['user_email']) &&
		   !empty($_POST['user_passwd']) &&
		   !empty($_POST['user_passwd_confirm'])){
			//Si passwd == passwd confirm
			if($_POST['user_passwd'] == $_POST['user_passwd_confirm']){
			}
		}
		//envoi de la réponse	
		$resp->send();
	}

	public function signIn()
	{

	}

	public function activate()
	{
		
	} 

}
