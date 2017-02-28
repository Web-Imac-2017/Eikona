<?php

class CommentLikeModel extends DBInterface
{
	private $comLikeID = 0;
	private $comLikedATAS = null;

	public function __construct($comLikeID = 0)
	{
		parent::__construct();
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
		$prfl = Sanitize::int($profileID);
		$cmnt = Sanitize::int($commentID);
		
		if($prfl < 1 || $cmnt < 1)
            return false;

		$stmt = $this->cnx->prepare("INSERT INTO comment_likes(profile_id, comment_id, like_time) VALUES (:profile, :comment, :time)");
		$stmt->execute([	":profile" => $prfl,
							":comment" => $cmnt,
							":time" => time()
		]);

		return true;
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
		$prfl = Sanitize::int($profileID);
		$cmnt = Sanitize::int($commentID);
		
		if($prfl < 1 || $cmnt < 1)
            return false;

		$stmt = $this->cnx->prepare("DELETE FROM comment_likes WHERE profile_id = :profile AND comment_id = :comment");
		$stmt->execute([	":profile" => $prfl,
							":comment" => $cmnt
		]);

		return true;
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

    /**
     * renvoie true si le commentaire et liké par le profil donné
     *
     * @param $profileID ID du profil dont on veut vérifier
     * @param $commentID ID du commentaire qu'on veut vérifier
     *
    */
	public function isLiking($profileID, $commentID)
	{
		$prfl = Sanitize::int($profileID);
		$cmnt = Sanitize::int($commentID);
		
		if($prfl < 1 || $cmnt < 1)
            return false;

		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM comment_likes WHERE profile_id = :profile AND comment_id = :comment");
		$stmt->execute([	":profile" => $prfl,
							":comment" => $cmnt
		]);
		
		if($stmt->fetchColumn() == 0)
		{
			return false;
		}
		else
		{
			return true;			
		}
	}
}