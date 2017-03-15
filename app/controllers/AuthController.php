<?php

/**
 * AuthController
 * Handle register, login, logout and password forgot
 */

interface AuthControllerInterface
{
    public function register();

    public function activate();

    public function forgottenPassword();

    public function regenere();

    public function signIn();

    public function signOut($silence = false);
}

class AuthController implements AuthControllerInterface
{
	private $model;

	public function __construct()
	{
		$this->model = new AuthModel();
	}

	/**
	 * Register a new user
	 */
	public function register()
	{
		$rsp = new Response();

		//Do we have all the required fields ?
		if(empty($_POST['user_name'])   ||
           empty($_POST['user_email'])  ||
           empty($_POST['user_passwd']) ||
           empty($_POST['user_passwd_confirm']))
        {
			$rsp->setFailure(400, "One or more fields are missing.")
                ->send();
        }

        //Is password confirmation correct ?
        if($_POST['user_passwd'] !== $_POST['user_passwd_confirm'])
        {
            $rsp->setFailure(409, "`user_passwd` and `user_passwd_confirm` does not match.")
                ->send();

            return;
        }

        //Is email valid ?
        if(!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL))
        {
            $rsp->setFailure(409, "`user_email` is not a valid email.")
                ->send();

            return;
        }

        //Is email banned?
        if(Response::read("ban", "is", "email", $_POST['user_email'])["code"] == 401)
        {
            $rsp->setFailure(406, "The email used is banned.")
                ->send();

            return;
        }

        //Is user unique?
        if(!$this->model->isUnique($_POST['user_email']))
        {
            $rsp->setFailure(403, "This email is already in use. You might want to log in.")
                ->send();

            return;
        }

        //Register the user
        $userID = $this->model->addUser($_POST['user_name'], $_POST['user_email'], $_POST['user_passwd'], time());

        //Send an activation email
        if(!$this->model->sendMail($userID, $_POST['user_email'], time()))
        {
            $rsp->setFailure(400, "Error while sending the activation email.")
                ->send();

            return;
        }

        $rsp->setSuccess(201, "User added and activation mail sent.")
            ->bindValue("email", $_POST['user_email'])
            ->bindValue("userID", $userID)
            ->send();
	}

	/**
	 * Activating the account
	 */
	public function activate()
	{
		$rsp = new Response();

        //Make sure we have all we need
		if(empty($_REQUEST['user_id']) || empty($_REQUEST['user_key']))
        {
			$rsp->setFailure(400, "One or more fields are missing.")
                ->send();

            return;
        }

        //Confirm activation
        if(!$this->model->checkActivation($_REQUEST['user_id'], $_REQUEST['user_key']))
        {
            $rsp->setFailure(409, "`user_id` or and `user_key` do not exist.")
                ->send();

            return;
        }

        //And set the user as activated
        $this->model->updateUserActivated($_REQUEST['user_id']);

        $rsp->setSuccess(200, "Account activated.")
            ->bindValue("userID", $_REQUEST['user_id'])
            ->send();
	}


	/**
	 * Send a recovery
	 */
	public function forgottenPassword()
	{
		$rsp = new Response();

		//Make sure we have all we need
		if(empty($_POST['user_email']))
        {
			$rsp->setFailure(400, "One or more fields are missing.")
                ->send();

            return;
        }

        //Confirm email
        if(!$this->model->checkEmail($_POST['user_email']))
        {
            $rsp->setFailure(404, "This is not a valid user.")
                ->send();

            return;
        }

        //Send recovery email
        $code = $this->model->addCode($_POST['user_email']);

        $this->model->sendRecuperationMail($_POST['user_email'], $code);

        $rsp->setSuccess(200, "Recovery email sent.")
            ->bindValue("userEmail", $_POST['user_email'])
            ->send();
	}

	public function regenere()
	{
		$rsp = new Response();

        //Confirm reception of all the fields
		if(empty($_POST['user_email'])          ||
		   empty($_POST['user_passwd'])         ||
		   empty($_POST['user_passwd_confirm']) ||
		   empty($_POST['code']))
        {
			$rsp->setFailure(400, "One or more fields are missing.")
                ->send();

            return;
        }

        //Confirm email
        if(!$this->model->checkEmail($_POST['user_email']))
        {
            $rsp->setFailure(404, "This is not a valid user.")
                ->send();

            return;
        }

        //COnfirm code
        if(!$this->model->checkCode($_POST['user_email'], $_POST['code']))
        {
            $rsp->setFailure(409, "Invalid reset code.")
                ->send();

            return;
        }

        //Confirm new password
        if($_POST['user_passwd'] !== $_POST['user_passwd_confirm'])
        {
            $rsp->setFailure(409, "Password and confirmation do not match.")
                ->send();

            return;
        }

        //Set new password
        $this->model->updatePassword($_POST['user_email'], $_POST['user_passwd']);

        $this->model->deleteCode($_POST['user_email']);

        $rsp->setSuccess(200, "Password correctly updated")
            ->bindValue("userEmail", $_POST['user_email'])
            ->send();
	}

	/**
	 * Connexion
	 */
	public function signIn()
	{
		$rsp = new Response();

        //Check if user has a "Stay connected" cookie
		$userKey = Cookie::read("stayConnected");

        if($userKey !== false)
        {
            //There's a cookie in here!
            $user = $this->model->getByKey($userKey);

            if($user["nbr"] == 0)
            {
                //Cookie is not valid
                Cookie::delete("stayConnected");

                $rsp->setFailure("409", "The cookie received does not match any registered account.")
                    ->send();

                return;
            }

            //Cookie is valid
            Session::renewKey();
            Session::write("userID", $user['user_id']);

            $rsp->setSuccess(200, "User connected.")
                ->bindValue("userID", $user['user_id'])
                ->bindValue("userEmail", $user['user_email'])
                ->send();

            return;
		}

        //Make sure no cookie are left behind
        Cookie::delete("stayConnected");

		//Proceed to connection
		if(empty($_POST['user_email']) ||
		   empty($_POST['user_passwd']))
        {
			$rsp->setFailure(400, "One or more fields are missing.")
                ->send();

            return;
        }

        $email = $this->model->checkEmail($_POST['user_email']);

        //Is this a registered user?
        if(!$email)
        {
            $rsp->setFailure(404, "unknown user")
                ->send();

            return;
        }

        $user = $this->model->checkConnection($_POST['user_email'], $_POST['user_passwd']);

        //Is user/email combo correct?
        if($user->getID() == 0)
        {
            $rsp->setFailure(409, "wrong password")
                ->send();

            return;
        }

        //Is the account activated ?
        if(!$user->getActivated())
        {
            $rsp->setFailure(401, "account not yet activated")
                ->send();

            return;
        }

        Session::renewKey();
        Session::write("userID", $user->getID());

        Cookie::set("stayConnected", $user->getKey(), 2*7*24*3600);

        $rsp->setSuccess(200, "user connected")
            ->bindValue("userID", $user->getID())
            ->bindValue("userEmail", $_POST['user_email'])
            ->send();
	}

	/**
	 * Log out the user and remove the cookie
	 */
	public function signOut($silence = false)
	{
        $rsp = new Response();

        //Are we logged in?
        if(!Session::read("userID"))
        {
            $rsp->setFailure(400, "User not connected")
                ->send();

            return;
        }
        
        
        Cookie::set("stayConnected", "", -1);

		Session::renewKey();
		Session::remove("userID");
		Session::remove("profileID");

		if($silence)
            return;

        $rsp->setSuccess(200, "user deconnected")
            ->bindValue("id", Session::read("userID"));

        $rsp->send();
    }

}

