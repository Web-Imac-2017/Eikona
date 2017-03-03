<?php

class BannedEmailsModel extends DBInterface
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a new banned email to the database
     * @param  string  $email Email to ban
     * @return boolean true on success, false on failure
     */
    public function add($email)
    {
        $rsp = new Response();

        if($this->exists($email))
        {
            $rsp->setFailure(400, "The email is already marked as forbidden")
                ->send();

            return false;
        }


        $stmt = $this->cnx->prepare("INSERT INTO banned_emails(banned_email) VALUES(:email)");
        $stmt->execute([":email" => $email]);

        return true;
    }

    /**
     * Remove an email from the database
     * @param string $mail Email to remove
     */
    public function remove($email)
    {
        $stmt = $this->cnx->prepare("DELETE FROM banned_emails WHERE banned_email = :email");
        $stmt->execute([":email" => $email]);

        return true;
    }

    /**
     * Return all the banned emails ordered alphabetically
     * @return array list of banned emails
     */
    public function getAll()
    {
        $stmt = $this->cnx->prepare("SELECT banned_email AS email FROM banned_emails ORDER BY banned_email");
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tell if an email is already in the database or not
     * @param  string  $email Email to compare
     * @return integer 0 if the email is absent, 1 if it is already there
     */
    public function exists($email)
    {
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM banned_emails WHERE banned_email = :email");
        $stmt->execute([":email" => $email]);

        return $stmt->fetchColumn();
    }
}
