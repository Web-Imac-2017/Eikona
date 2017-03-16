<?php


interface BanInterface
{
    public function add($type);

    public function remove($type);

    public function is($type);

    public function get($type);
}






class BanController implements BanInterface
{
    private $wordsModel;
    private $emailsModel;

    public function __construct()
    {
        $this->wordsModel = new BannedWordsModel();
        $this->emailsModel = new BannedEmailsModel();
    }

    /**
     * Ban a new element
     * @param  string   $type What to ban, email ou word
     */
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
    
    /**
     * Ban a word
     */
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

    /**
     * Ban an email
     */
    private function addEmail()
    {
        $email = strtolower($_POST['email']);
        
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $rsp->setSuccess(400, "The given value is not an email.")
                ->send();

            return;
        }
        
        $rsp = new Response();

        if($this->emailsModel->add($email))
        {
            $rsp->setSuccess(200)
                ->send();

            return;
        }

        $rsp->setFailure(409, "The email is already banned")
            ->send();
    }

    
    
    /**
     * Remove an element from the ban database
     * @param  string   $type What to remove, word or email
     */
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
    
    /**
     * Remove a word
     */
    private function removeWord()
    {
        $word = strtolower($_POST['word']);

        $rsp = new Response();

        $this->wordsModel->remove($word);

        $rsp->setSuccess(200)
            ->send();
    }
    
    /**
     * Remove an emil
     */
    private function removeEmail()
    {
        $email = strtolower($_POST['email']);
        
        $rsp = new Response();

        $this->emailsModel->remove($email);
        
        $rsp->setSuccess(200)
            ->send();
    }



    /**
     * Tell if the element is banned
     * @param  string   $type              What to verify
     * @param  string   [$givenElt         = NULL] The element to verify. Can also be passed as POST
     */
    public function is($type, $givenElt = NULL)
    {
        $rsp = new Response();

        if($type === "word" && (isset($_POST['word']) || !empty($givenElt)))
        {
            $word = isset($_POST['word']) ? $_POST['word'] : $givenElt;
            return $this->isWordBan($word);
        }
           
        if($type === "email" && (isset($_POST['email']) || !empty($givenElt)))
        {
            $email = isset($_POST['email']) ? $_POST['email'] : $givenElt;
            return $this->isEmailBan($email);
        }
           
        $rsp->setFailure(400, "Your request couldn't be executed. Check for wrong argument or missing values.")
            ->send();
    }

    /**
     * Tell if the given word is ban or not
     * @param string $word The word to verify
     */
    private function isWordBan($word)
    {
        $word = strtolower($word);
        
        if($this->wordsModel->exists($word) == 1)
        {
            $rsp = new Response();
            $rsp->setSuccess(401, "This word is not authorized")
                ->bindValue("exists", true)
                ->send();
            
            return;
        }
        
        $rsp = new Response();
        $rsp->setSuccess(404, "This word was not found. It is authorized")
            ->bindValue("exists", false)
            ->send();
    }

    /**
     * Tell if the given email is ban or not
     * @param string $email The email to verify
     */
    private function isEmailBan($email)
    {
        $email = strtolower($email);
        
        if($this->emailsModel->exists($email) == 1)
        {
            $rsp = new Response();
            $rsp->setSuccess(401, "This email is not authorized")
                ->bindValue("exists", true)
                ->send();
            
            return;
        }
        
        $rsp = new Response();
        $rsp->setSuccess(404, "This email was not found. It is authorized")
            ->bindValue("exists", false)
            ->send();

        return;
    }



    /**
     * Return all the element for the chosen type
     * @param  string $type what type to return, email or word
     */
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

    private function getAllEmails()
    {
        $list = $this->emailsModel->getAll();

        $rsp = new Response();
        $rsp->setSuccess(200)
            ->bindValue("emails", $list)
            ->bindValue("nbrEmails", count($list))
            ->send();
    }
}
