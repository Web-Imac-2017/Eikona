<?php

class LikeModel extends DBInterface{

	public function __construct(){

		parent::__construct();

	}

	public function isLiked($postID, $profileID)
	{
		$stmt = $this->cnx->prepare("
			SELECT COUNT(*) FROM post_likes
			WHERE :postID = post_id
			AND :profileID = profile_id");
		$stmt->execute([":postID"    => $postID,
			            ":profileID" => $profileID]);

		return ($stmt->fetchColumn() == 0) ? false : true;
	}

	public function like($postID, $profileID)
	{
		$stmt = $this->cnx->prepare("
			INSERT INTO post_likes (profile_id, post_id, like_time)
			VALUES (:profileID, :postID, :likeTime)");
		$stmt->execute([":profileID" => $profileID,
			            ":postID"    => $postID,
			            ":likeTime"  => time()]);
	}


	public function unlike($postID, $profileID)
	{
		$stmt = $this->cnx->prepare("
			DELETE FROM post_likes
			WHERE :postID = post_id
			AND :profileID = profile_id");
		$stmt->execute([":postID"    => $postID,
			            ":profileID" => $profileID]);
	}

	public function countLike($postID)
	{
		$stmt = $this->cnx->prepare("
			SELECT COUNT(*) FROM post_likes
			WHERE :postID= post_id");
		$stmt->execute([":postID" => $postID]);

		return $stmt->fetchColumn();
	}

}