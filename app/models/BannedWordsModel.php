<?php

class BannedWordsModel extends DBInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a new banned word to the database
     * @param  string  $word Word to ban
     * @return boolean true on success, false on failure
     */
    public function add($word)
    {
        $rsp = new Response();

        if($this->duplicate($word))
        {
            $rsp->setFailure(400, "The word is already marked as forbidden")
                ->send();

            return false;
        }


        $stmt = $this->cnx->prepare("INSERT INTO banned_words(word) VALUES(:word)");
        $stmt->execute([":word" => $word]);

        return true;
    }

    /**
     * Remove a word from the database
     * @param string $word Word to remove
     */
    public function remove($word)
    {
        $stmt = $this->cnx->prepare("DELETE FROM banned_words WHERE word = :word");
        $stmt->execute(":word" => $word);
    }

    /**
     * Return all the banned words ordered alphabetically
     * @return array list of banned words
     */
    public function getAll()
    {
        $stmt = $this->cnx->prepare("SELECT * FROM banned_words ORDER BY word");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tell if a word is already in the database or not
     * @param  string  $word Word to compare
     * @return integer 0 if the word is absent, 1 if it is already there
     */
    private function duplicate($word)
    {
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM banned_words WHERE word = :word");
        $stmt->execute([":word" => $word]);

        return $stmt->fetchColumn();
    }
}
