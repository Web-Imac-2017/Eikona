<?php

class BlockController
{
    private $model;
    private $view;

    public function __construct()
    {
        $this->model = new BlockModel();
    }

    private function entryIsOkay($blocker_id, $blocked_id){
        $blocker_id = Sanitize::int($blocker_id);
        $blocked_id = Sanitize::int($blocked_id);

        $resp = new Response();

        if ($blocker_id == $blocked_id)
        {
            $resp->setFailure(406, "blocker and blocked are identical")
                 ->bindValue("blocker_ID", $blocker_id)
                 ->bindValue("blocked_ID", $blocked_id)
                 ->send();
            return false;
        }

        if ($blocker_id <= 0)
        {
            $resp->setFailure(400, "bad id format for the blocker")
                 ->bindValue("blocker_ID", $blocker_id)
                 ->send();
            return false;
        }

        if ($blocked_id <= 0)
        {
            $resp->setFailure(400, "bad id format for the blocker")
                 ->bindValue("blocked_ID", $blocked_id)
                 ->send();
            return false;
        }

        $userModel = new UserModel();
        if ( !($userModel->userExists($blocker_id)) )
        {
            $resp->setFailure(404, "user not found")
                 ->bindValue("blocker_ID", $blocker_id)
                 ->send();
            return false;
        }

        if ( !($userModel->userExists($blocked_id)) )
        {
            $resp->setFailure(404, "user not found")
                 ->bindValue("blocked_ID", $blocked_id)
                 ->send();
            return false;
        }

        return true;
    }

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
