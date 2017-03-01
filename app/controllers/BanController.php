<?php


interface BanInterface
{
    public function add($type);

//    public function remove($type);
//
//    public function is($type);
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

        if($this->wordModel->add($word))
        {
            $rsp->setSuccess(200)
                ->send();
        }

        $rsp->setFailure(409, "The word is already banned")
            ->send();
    }


}


