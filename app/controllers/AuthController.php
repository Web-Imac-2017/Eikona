<?php

class AuthController{

	private $model;

	public function __construct()
	{
		$this->model = new AuthModel();
	}

	/**
	 * Inscription de l'user
	 * @return Response JSON
	 */
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
				//Si le mail est valide
				if(filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)){
					//si user est unique
					if($this->model->isUnique($_POST['user_email'])){
						//insertion dans la base de données
						$user_register_time = time();
						$id = $this->model->addUser(
							$_POST['user_name'],
							$_POST['user_email'],
							$_POST['user_passwd'],
							$user_register_time);
						//envoi d'un mail d'activation
						if($this->model->sendMail($id, $_POST['user_email'], $user_register_time)){
							$resp->setSuccess(201, "user added and activation mail sent")
						         ->bindValue("email", $_POST['user_email'])
						         ->bindValue("userID", $id);
						}else{
							$resp->setFailure(400, "mail not sent");
						}
					}else{
						$resp->setFailure(403, "user already exists");
					}				
				}else{
					$resp->setFailure(409, "user_email is not an email");
				}				
			}else{
				$resp->setFailure(409, "user_passwd et user_passwd_confirm ne sont pas les mêmes");
			}
		}else{
			$resp->setFailure(400, "tous les champs ne sont pas remplis");
		}
		
		//envoi de la réponse	
		$resp->send();
	}

	/**
	 * Activation du compte
	 * @return Response JSON
	 */
	public function activate()
	{
		$resp = new Response();

		if(!empty($_REQUEST['user_id']) && !empty($_REQUEST['user_key'])){

			//Activation du compte
			$res = $this->model->checkActivation($_REQUEST['user_id'], $_REQUEST['user_key']);
			//Si l'user existe bien
			if($res){
				$this->model->updateUserActivated($_REQUEST['user_id']);
				$resp->setSuccess(200, "Account activated")
				     ->bindValue("userID", $_REQUEST['user_id']); 
			}else{
				$resp->setFailure(409, "user_id or and user_key do not exist");
			}
		}else{
			$resp->setFailure(400, "tous les champs ne sont pas remplis");
		} 	
		
		//envoi de la réponse
		$resp->send();
	}


	public function forgottenPassword()
	{
		$resp = new Response();

		//Si un mail a été entré
		if(!empty($_POST['user_email'])){
			//Si l'email existe bien
			if($this->model->checkEmail($_POST['user_email'])){
				$code = $this->model->addCode($_POST['user_email']);
				$this->model->sendRecuperationMail($_POST['user_email'], $code);
				$resp->setSuccess(200, "email sent")
					 ->bindValue("userEmail", $_POST['user_email']);
			}else{
				$resp->setFailure(404, "unknown user");
			}
		}else{
			$resp->setFailure(400, "Tous les champs ne sont pas remplis");
		}

		$resp->send();
	}

	public function regenere()
	{
		$resp = new Response();

		if(!empty($_POST['user_email']) &&
		   !empty($_POST['user_passwd']) && 
		   !empty($_POST['user_passwd_confirm']) &&
		   !empty($_POST['code'])){

			if($this->model->checkEmail($_POST['user_email'])){
				if($this->model->checkCode($_POST['user_email'], $_POST['code'])){
					if($_POST['user_passwd'] == $_POST['user_passwd_confirm']){
						$this->model->updatePassword($_POST['user_email'], $_POST['user_passwd']);
						$this->model->deleteCode($_POST['user_email']);
						$resp->setSuccess(200, "password regenerated")
						     ->bindValue("userEmail", $_POST['user_email']);
					}else{
						$resp->setFailure(409, "password and confirmation do not correspond");
					}
				}else{
					$resp->setFailure(409, "invalid reset code");
				}				
			}else{
				$resp->setFailure(404, "unknown user");
			}
		}else{
			$resp->setFailure(400, "Tous les champs ne sont pas remplis");
		}

		$resp->send();
	}

	/**
	 * Connexion
	 * @return Response JSON
	 */
	public function signIn()
	{
		$resp = new Response();

		//si les deux champs de connexion sont remplis
		if(!empty($_POST['user_email']) &&
		   !empty($_POST['user_passwd'])){

			$email = $this->model->checkEmail($_POST['user_email']);

			//Si l'user est inscrit dans la bdd
			if($email){
				$user = $this->model->checkConnection($_POST['user_email'], $_POST['user_passwd']);
				//Si la combinaison email /pwd est correcte
				if($user->getID() != 0){
					//Si le compte est activé
					if($user->getActivated()){
						$resp->setSuccess(200, "user connected")
						     ->bindValue("userID", $user->getID())
						     ->bindValue("userEmail", $_POST['user_email']);
						Session::renewKey();
						Session::write("userID", $user->getID());
					}else{
						$resp->setFailure(401, "account not yet activated");
					}
				}else{
					$resp->setFailure(409, "wrong password");
				}
			}else{
				$resp->setFailure(404, "unknown user");
			}
		}else{
			$resp->setFailure(400, "tous les champs ne sont pas remplis");
		}
		//envoi de la réponse
		$resp->send();
	}

	/**
	 * Deconnexion
	 * @return Response JSON
	 */
	public function signOut($silence = false)
	{		
		if(!$silence){
			$resp = new Response();

			if(!Session::read("userID")){
				$resp->setFailure(400, "User not connected");
			}else{
				$resp->setSuccess(200, "user deconnected")
				     ->bindValue("id", Session::read("userID"));
			}
			$resp->send();
		}
		
		Session::renewKey();
		Session::remove("userID");
		Session::remove("profileID");
	}
	
}
