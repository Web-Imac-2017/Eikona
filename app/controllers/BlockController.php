<?php

interface BlockControllerInterface
{
    public function block($blocker_id, $blocked_id);

    public function unblock($blocker_id, $blocked_id);

    public function isBlocked($blocker_id, $blocked_id);
}

class BlockController extends BlockControllerInterface
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new BlockModel();
    }
    
    /**
     * Validate given users ID
     * @param  integer $blocker_id User ID
     * @param  integer $blocked_id User ID
     * @return boolean true if they are both valid, false otherwise
     */
    private function entryIsOkay($blocker_id, $blocked_id)
    {
        $blocker_id = Sanitize::int($blocker_id);
        $blocked_id = Sanitize::int($blocked_id);

        $resp = new Response();
        
        //IS this the same user both times
        if ($blocker_id == $blocked_id)
        {
            $resp->setFailure(406, "blocker and blocked are identical")
                 ->bindValue("blocker_ID", $blocker_id)
                 ->bindValue("blocked_ID", $blocked_id)
                 ->send();
            return false;
        }

        //Is this id valid?
        if ($blocker_id <= 0)
        {
            $resp->setFailure(400, "bad id format for the blocker")
                 ->bindValue("blocker_ID", $blocker_id)
                 ->send();
            return false;
        }

        //Is this id valid?
        if ($blocked_id <= 0)
        {
            $resp->setFailure(400, "bad id format for the blocker")
                 ->bindValue("blocked_ID", $blocked_id)
                 ->send();
            return false;
        }

        $userModel = new UserModel();
        
        //Does this user exists?
        if ( !($userModel->userExists($blocker_id)) )
        {
            $resp->setFailure(404, "user not found")
                 ->bindValue("blocker_ID", $blocker_id)
                 ->send();
            return false;
        }
        
        //Does this user exists?
        if ( !($userModel->userExists($blocked_id)) )
        {
            $resp->setFailure(404, "user not found")
                 ->bindValue("blocked_ID", $blocked_id)
                 ->send();
            return false;
        }

        return true;
    }
    
    /**
     * Block a user
     * @param integer $blocker_id User who is blocking
     * @param integer $blocked_id User who is getting blocked
     */
    public function block($blocker_id, $blocked_id)
    {
        $resp = new Response();

        if ($this->model->isBlocked($blocker_id, $blocked_id))
        {
            $resp->setFailure(406, "User already blocked")
                 ->send();

            return;
        }

        if ($this->entryIsOkay($blocker_id, $blocked_id))
        {
            $this->model->blockUser($blocker_id, $blocked_id);
            $resp->setSuccess(200)
                 ->send();
        }
    }

    
    /**
     * Unlock a user
     * @param integer $blocker_id User who was blocking
     * @param integer $blocked_id User who was blocked
     */
    public function unblock($blocker_id, $blocked_id)
    {
        $resp = new Response();

        if ( !($this->model->isBlocked($blocker_id, $blocked_id)) )
        {
            $resp->setFailure(406, "User not blocked")
                 ->send();
            return;
        }

        if ($this->entryIsOkay($blocker_id, $blocked_id) )
        {
            $this->model->unblockUser($blocker_id, $blocked_id);
            $resp->setSuccess(200)
                 ->send();
        }
    }

    
    /**
     * Tell is a user is blocking another one
     * @param integer $blocker_id User who is blocking
     * @param integer $blocked_id User who is getting blocked
     */
    public function isBlocked($blocker_id, $blocked_id)
    {
        if ($this->entryIsOkay($blocker_id, $blocked_id))
        {
            $resp = new Response();
            $rep = $this->model->isBlocked($blocker_id, $blocked_id);
            $resp->setSuccess(200)
                 ->bindValue("isBlocked", $rep)
                 ->send();
        }
    }
}
