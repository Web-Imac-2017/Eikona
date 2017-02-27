<?php

class CommentLikeModel extends DBInterface
{
	private $comLikeID = 0;
	private $comLikedATAS = null;

	public function __construct($comLikeID = 0)
	{
		parent::__construct();
	}

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

	public function unlike($profile, $comment)
	{
		$stmt = $this->cnx->prepare("DELETE FROM comment_likes WHERE profile_id = :profile AND comment_id = :comment");
		$stmt->execute([	":profile" => $profile,
							":comment" => $comment
		]);
	}

	public function getLikes($comment)
	{
		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = :comment");
		$stmt->execute(["comment" => $comment]);

		return $stmt->fetchColumn();
	}

	public function isLiking()
	{
		$stmt = $this->cnx->prepare("SELECT COUNT(*) FROM comment_likes WHERE profile_id = :profile AND comment_id = :comment");
		$stmt->execute([	":profile" => $profile,
							":comment" => $comment
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