<?php

class CommentLikeModel extends DBInterface
{

	public function __construct()
	{
		parent::__construct();
	}

	public function isLiked($profileID, $commentID)
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
			WHERE profile_id = :profileID 
			AND comment_id = :commentID");
		$stmt->execute([":profileID" => $profileID,
						":commentID" => $commentID]);
	}

    /**
     * Récupère et renvoie le nombre de like d'un commentaire
     *
     * @param $commentID ID du commentaire dont on veut compter les likes
     *
    */
	public function getLikes($commentID)
	{
		$stmt = $this->cnx->prepare("
			SELECT comment_id, comment_likes.profile_id, profiles.profile_name, like_time
			FROM comment_likes
			JOIN profiles ON comment_likes.comment_id = profiles.profile_id
			WHERE :commentID = comment_id");
		$stmt->execute([":commentID" => $commentID]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

}