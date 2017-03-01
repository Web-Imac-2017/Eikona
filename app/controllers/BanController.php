<?php


interface BanInterface
{
    public function add($type);

    public function remove($type);

    public function is($type);
//
//    public function get($type);
}






class BanController implements BanInterface
{
    private $wordsModel;
    private $emailsModel;

    public function __construct()
    {
        $this->wordsModel = new BannedWordsModel();
    }

    public function add($type)
    {
        $rsp = new Response();

        if(!isAuthorized::isAdmin())
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if($type === "word" && isset($_POST['word']))
            return $this->addWord();

        if($type === "email" && isset($_POST['email']))
            return $this->addEmail();

        $rsp->setFailure(400, "Your request couldn't be executed. Check for wrong arguments or missing values.")
            ->send();
    }

    private function addWord()
    {
        $word = strtolower($_POST['word']);

        $rsp = new Response();

        if($this->wordsModel->add($word))
        {
            $rsp->setSuccess(200)
                ->send();

            return;
        }

        $rsp->setFailure(409, "The word is already banned")
            ->send();
    }




    public function remove($type)
    {
        $rsp = new Response();

        if(!isAuthorized::isAdmin())
        {
            $rsp->setFailure(401, "You are not authorized to do this action.")
                ->send();

            return;
        }

        if($type === "word" && isset($_POST['word']))
            return $this->removeWord();

        if($type === "email" && isset($_POST['email']))
            return $this->removeEmail();

        $rsp->setFailure(400, "Your request couldn't be executed. Check for wrong arguments or missing values.")
            ->send();
    }

    private function removeWord()
    {
        $word = strtolower($_POST['word']);

        $rsp = new Response();

        $this->wordsModel->remove($word);

        $rsp->setSuccess(200)
            ->send();
    }




    public function is($type)
    {
        $rsp = new Response();

        if($type === "word" && isset($_POST['word']))
            return $this->isWordBan();

        if($type === "email" && isset($_POST['email']))
            return $this->isEmailBan();

        $rsp->setFailure(400, "Your request couldn't be executed. Check for wrong arguments or missing values.")
            ->send();
    }

    private function isWordBan()
    {
        $word = strtolower($_POST['word']);

        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("exists", $this->wordsModel->exists($word))
            ->send();

    }




    public function get($type)
    {
        $rsp = new Response();

        if($type === "word")
            return $this->getAllWords();

        if($type === "email")
            return $this->getAllEmails();

        $rsp->setFailure(400, "Your request couldn't be executed. Check for wrong arguments or missing values.")
            ->send();
    }

    private function getAllWords()
    {
        $list = $this->wordsModel->getAll();

        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("words", $list)
            ->bindValue("nbrWords", count($list))
            ->send();

    }


}


