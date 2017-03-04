<?php


class Ban
{
    private $wordsModel;
    private $emailsModel;

    public function __construct()
    {
        $this->wordsModel = BannedWordsModel();
    }
    
    
    
    
    public function add($type)
    {
        $rsp = new Response();
        
    }
    
    private function addWord() {}
    private function addEmail() {}
    
    
    
    
    public function remove($type)
    {
        
    }
    
    private function removeWord() {}
    private function removeEmail() {}
    
    
    
    
    public function is($type)
    {
        
    }
    
    private function isWordBan() {}
    private function isEmailBan() {}
    
    
    
    
    public function get($type)
    {
        
    }
    
    private function getWords() {}
    private function getEmails() {}
}


