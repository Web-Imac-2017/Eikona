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
		if(!empty($_POST['user_name']) &&
		   !empty($_POST['user_email']) &&
		   !empty($_POST['user_passwd']) &&
		   !empty($_POST['user_passwd_confirm'])){

			//Si passwd == passwd confirm
			if($_POST['user_passwd'] == $_POST['user_passwd_confirm']){
				//si user est unique
				if($this->model->uniqueUser($_POST['user_email'])){
					//insertion dans la base de données
					$user_register_time = time();
					$id = $this->model->addUser(
						$_POST['user_name'],
						$_POST['user_email'],
						$_POST['user_passwd'],
						$user_register_time);
					//envoi d'un mail d'activation
					$this->model->sendMail(
						$_POST['user_email'],
						$user_register_time,
						$id);
					$resp->setSuccess(200, "user added")
					     ->bindValue("email", $_POST['user_email']);
				}else{
					$resp->setFailure(403, "user already exists");
				}				
			}else{
				$resp->setFailure(404, "user_passwd et user_passwd_confirm ne sont pas les mêmes");
			}
		}else{
			$resp->setFailure(400, "Tous les champs ne sont pas remplis");
		}
		
		//envoi de la réponse	
		$resp->send();
	}

	public function activate()
	{
		$resp = new Response();

		if(!empty($_POST['user_id']) &&
		   !empty($_POST['user_key'])){

			//activation du compte
			$this->model->updateUserActivated($_POST['user_id']);
			$resp->setSuccess();
		}else{
			$resp->setFailure();
		}
		
		//envoi de la réponse
		$resp->send();
	}

	public function signIn()
	{
		$resp = new Response();

		//si les deux champs de connexion sont remplis
		if(!empty($_POST['user_email']) &&
		   !empty($_POST['user_passwd'])){

		   	//Si la combinaison email / password est correcte
			$user = $this->model->checkAuth($_POST['user_email'], $_POST['user_passwd']);
			if($user != null){
				//si le compte est activé
				if($user->getActivated()){
					$resp->setSuccess(200, "user connected");
				}else{

				}
			}else{

			}
		}else{
			$resp->setFailure(401, "Tous les champs ne sont pas remplis");
		}

		//envoi de la réponse
		$resp->send();
	}
}
