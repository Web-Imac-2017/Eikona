<?php
/*
require_once '../models/class.dbinterface.php';
require_once '../models/class.login.php';
require_once '../models/class.user.php';

session_start();

$Login = new Login();

if(!isset($_SESSION['user'])){
	if(isset($_POST["user_email"]) && !empty($_POST["user_email"]) && !empty($_POST["user_passwd"])){
		$user = $Login->checkAuthentification($_POST["user_email"], $_POST["user_passwd"]);
		if($user != null){
			if(!$user->getActivated()){
				$log = "Votre compte n'est pas activé";
			}else{
				$_SESSION['user'] = $user;
				header("LOCATION: index.php");
			}
		}
	}
}

require_once '../views/login.php';
*/

class LoginController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new LoginModel();
    }

    public function login()
    {
        if(!isset($_SESSION['user'])){
            if(isset($_POST["user_email"]) && !empty($_POST["user_email"]) && !empty($_POST["user_passwd"])){
                $user = $this->model->checkAuthentification($_POST["user_email"], $_POST["user_passwd"]);
                if($user != null){
                    if(!$user->getActivated()){
                        $log = "Votre compte n'est pas activé";
                    }else{
                        $_SESSION['user'] = $user;
                        header("LOCATION: index.php");
                    }
                }
            }
        }
    }
}
