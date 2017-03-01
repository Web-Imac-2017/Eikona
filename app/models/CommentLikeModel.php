<?php

class CommentLikeModel extends DBInterface
{

	public function __construct()
	{
		parent::__construct();
	}

	public function isLiked($commentID, $profileID)
	{
		$stmt = $this->cnx->prepare("
			SELECT COUNT(*) FROM comment_likes
			WHERE :commentID = comment_id
			AND :profileID = profile_id");
		$stmt->execute([":commentID" => $commentID,
			            ":profileID" => $profileID]);

		return ($stmt->fetchColumn() == 0) ? false : true;
	}

    /**
     * Like a comment 
     *
     * @param $profileID ID du profil utilisé pour liker le commentaire
     * @param $commentID ID du commentaire liké
     *
    */
	public function like($profileID, $commentID)
	{
		$stmt = $this->cnx->prepare("
			INSERT INTO comment_likes(profile_id, comment_id, like_time)
			VALUES (:profile, :comment, :time)");
		$stmt->execute([":profile" => $profileID,
						":comment" => $commentID,
						":time"    => time()]);
	}

    /**
     * Unlike a comment
     *
     * @param $profileID ID du profil utilisé pour unliker le commentaire
     * @param $commentID ID du commentaire unliké
     *
    */
	public function unlike($profileID, $commentID)
	{
		$stmt = $this->cnx->prepare("
			DELETE FROM comment_likes 
			WHERE profile_id = :profile 
			AND comment_id = :comment");
		$stmt->execute([":profile" => $profileID,
						":comment" => $commentID]);
	}

    /**
     * Récupère et renvoie le nombre de like d'un commentaire
     *
     * @param $commentID ID du commentaire dont on veut compter les likes
     *
    */
	public function getLikes($commentID)
	{
		$cmnt = Sanitize::int($commentID);
		
		if($cmnt < 1)
            return false;

		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = :comment");
		$stmt->execute(["comment" => $cmnt]);

		return $stmt->fetchColumn();
	}

}