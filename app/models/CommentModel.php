<?php

class CommentModel extends DBInterface
{
    private $commentID = 0;
    private $commentDatas = null;

    public function __construct($commentID = 0)
    {
        parent::__construct();

        $this->setComment($commentID);
    }

    /**
     * Class initializer. Tries to load informations on the given comment
     *
     * @param $commentID Comment to be used unique ID
     */
    public function setComment($commentID)
    {
    	$commentID = Sanitize::int($commentID);

        if($commentID < 1 || $commentID == $this->commentID)
        {
            $this->commentID = 0;
            $commentDatas = null;
            return "wrongFormat";
        }

        //Confirm the id before doing anything
        $stmt = $this->cnx->prepare("SELECT COUNT(*) FROM comments WHERE comment_id = :commentID");
        $stmt->execute([":commentID" => $commentID]);

        //Post ID not found
        if($stmt->fetchColumn() == 0)
        {
            $this->commentID = 0;
            $commentDatas = null;
            return "notFound";
        }

        //Post found
        $stmt = $this->cnx->prepare("SELECT comment_id, profile_id, post_id, comment_text, comment_time FROM comments WHERE comment_id = :commentID");
        $stmt->execute([":commentID" => $commentID]);

        $this->commentID = $commentID;
        $this->commentDatas = $stmt->fetch();

		return "success";
    }

    /************************/
    /******** GETTER ********/
    /************************/

    public function getProfileID()
    {
        return $this->commentDatas['profile_id'];
    }

    /**
     * Create a new comment
     *
     * @param $profile Profile used to post the comment
     * @param $post Targeted post of the comment
     * @param $text Text content of the comment
     * @param $time Time the comment was created
     *
    */
    public function create($profileID, $postID, $comment)
    {
    	$comment = Sanitize::string($comment);

        $stmt = $this->cnx->prepare("
            INSERT INTO comments (profile_id, post_id, comment_text, comment_time)
            VALUES (:profileID, :postID, :comment, :time)");
        $stmt->execute([":profileID" => $profileID,
                        ":postID"    => $postID,
                        ":comment"   => $comment,
                        ":time"      => time()]);
    }

	/*
     * Delete the post
     *
     */
    public function delete($commentID)
    {
        $stmt = $this->cnx->prepare("
            DELETE FROM comments 
            WHERE comment_id = :commentID");
        $stmt->execute([":commentID" => $commentID]);
    }

    public function getComments($postID)
    {
        $stmt = $this->cnx->prepare("
            SELECT comment_id, comments.profile_id, profiles.profile_name, comment_text, comment_time
            FROM comments
            JOIN profiles ON comments.profile_id = profiles.profile_id
            WHERE :postID = post_id");
        $stmt->execute([":postID" => $postID]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getComment($commentID)
    {
        $stmt = $this->cnx->prepare("
            SELECT comment_id, comments.profile_id, profiles.profile_name, comment_text, comment_time
            FROM comments
            JOIN profiles ON comments.profile_id = profiles.profile_id
            WHERE comment_id = :commentID");
        $stmt->execute([":commentID" => $commentID]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
